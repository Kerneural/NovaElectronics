import os
import re
import json
import subprocess
from bs4 import BeautifulSoup
from urllib.parse import urlparse

# Paths
workspace_dir = os.path.dirname(os.path.abspath(__file__))
html_path = os.path.join(workspace_dir, "index.html")
templates_dir = os.path.join(workspace_dir, "src", "wp-content", "themes", "dienmay8-clone", "templates")
homepage_path = os.path.join(workspace_dir, "src", "wp-content", "themes", "dienmay8-clone", "templates", "homepage.php")
routes_path = os.path.join(workspace_dir, "src", "wp-content", "themes", "dienmay8-clone", "routes.json")
mapping_path = os.path.join(workspace_dir, "src", "wp-content", "themes", "dienmay8-clone", "products_map.json")

def run_wp_cli(args):
    # Run WP-CLI command via docker compose
    cmd = ["docker", "compose", "run", "--rm", "wp-cli"] + args
    print("Executing WP-CLI command...")
    try:
        result = subprocess.run(cmd, cwd=workspace_dir, capture_output=True, text=True, shell=True)
        if result.returncode == 0:
            # Strip trailing warning outputs if docker outputs them
            lines = result.stdout.strip().split('\n')
            # Look for the last line which contains the actual return value
            for line in reversed(lines):
                line = line.strip()
                if line and not line.startswith("time=") and not line.startswith("Container") and not line.startswith("Warning"):
                    return line
            return ""
        else:
            print(f"Command failed: {result.stderr}")
            return None
    except Exception as e:
        print(f"Exception executing command: {e}")
        return None

def main():
    print("Step 1: Parsing products from index.html...")
    with open(html_path, "r", encoding="utf-8") as f:
        html = f.read()
        
    soup = BeautifulSoup(html, 'html.parser')
    product_boxes = soup.select('.product-small, .box')
    
    parsed_products = []
    for box in product_boxes:
        title_el = box.select_one('.name a, .title a')
        price_el = box.select_one('.price ins .amount, .price .amount')
        
        if title_el:
            title = title_el.get_text(strip=True)
            link = title_el.get('href', '')
            slug = urlparse(link).path.strip('/')
            
            price_str = price_el.get_text(strip=True) if price_el else ""
            price_digits = re.sub(r'[^\d]', '', price_str)
            price = int(price_digits) if price_digits else 0
            
            if title and slug and not any(p['slug'] == slug for p in parsed_products):
                parsed_products.append({
                    'title': title,
                    'slug': slug,
                    'price': price
                })
                
    print(f"Found {len(parsed_products)} unique products on homepage.")
    
    # Step 2: For each product, find original ID from its template file
    print("Step 2: Locating original IDs from templates...")
    products_with_ids = []
    
    for p in parsed_products:
        slug = p['slug']
        template_file = os.path.join(templates_dir, f"{slug}.php")
        original_id = None
        
        if os.path.exists(template_file):
            with open(template_file, "r", encoding="utf-8", errors="ignore") as f:
                temp_content = f.read()
            # Search for value="(\d+)" on name="add-to-cart" button or input
            match = re.search(r'name=["\']add-to-cart["\']\s+value=["\'](\d+)["\']', temp_content)
            if not match:
                # Retry alternative regex order
                match = re.search(r'value=["\'](\d+)["\']\s+name=["\']add-to-cart["\']', temp_content)
            if not match:
                # Retry search for data-product_id
                match = re.search(r'data-product_id=["\'](\d+)["\']', temp_content)
                
            if match:
                original_id = match.group(1)
                print(f"Product '{slug}' original ID: {original_id}")
            else:
                print(f"Warning: Could not find original ID for '{slug}'")
        else:
            print(f"Warning: Template file {template_file} does not exist.")
            
        p['original_id'] = original_id
        products_with_ids.append(p)

    # Step 3: Create products in database via WP-CLI
    print("Step 3: Creating products in WooCommerce database...")
    id_mapping = {} # original_id -> new_id
    
    for p in products_with_ids:
        slug = p['slug']
        title = p['title']
        price = p['price']
        original_id = p['original_id']
        
        # Check if product already exists in database
        check_args = ["wp", "post", "list", "--post_type=product", f"--name={slug}", "--field=ID"]
        existing_id = run_wp_cli(check_args)
        
        new_id = None
        if existing_id and existing_id.isdigit():
            new_id = int(existing_id)
            print(f"Product slug '{slug}' already exists with ID: {new_id}")
        else:
            # Create product
            create_args = [
                "wp", "wc", "product", "create",
                f"--name={title}",
                f"--slug={slug}",
                f"--regular_price={price}",
                "--status=publish",
                "--porcelain",
                "--user=admin"
            ]
            new_id_str = run_wp_cli(create_args)
            if new_id_str and new_id_str.isdigit():
                new_id = int(new_id_str)
                print(f"Created product slug '{slug}' successfully with ID: {new_id}")
            else:
                print(f"Failed to create product slug '{slug}' via WP-CLI.")
                
        if original_id and new_id:
            id_mapping[str(original_id)] = int(new_id)

    # Save mapping to file
    with open(mapping_path, "w", encoding="utf-8") as f:
        json.dump(id_mapping, f, indent=4)
    print(f"Saved product ID mapping to {mapping_path}")

    # Step 4: Rewrite original IDs in templates with new IDs
    print("Step 4: Rewriting product IDs in template files...")
    files_to_update = []
    if os.path.exists(templates_dir):
        for file in os.listdir(templates_dir):
            if file.endswith(".php"):
                files_to_update.append(os.path.join(templates_dir, file))
    
    if os.path.exists(homepage_path):
        files_to_update.append(homepage_path)
        
    print(f"Scanning {len(files_to_update)} template files...")
    
    for file_path in files_to_update:
        try:
            with open(file_path, "r", encoding="utf-8", errors="ignore") as f:
                content = f.read()
                
            original_content = content
            
            # Replace occurrences of original IDs with new IDs in markup
            # e.g., name="add-to-cart" value="1683"
            for orig_id, new_id in id_mapping.items():
                # Replace button values
                content = content.replace(f'name="add-to-cart" value="{orig_id}"', f'name="add-to-cart" value="{new_id}"')
                content = content.replace(f'name=\'add-to-cart\' value=\'{orig_id}\'', f'name=\'add-to-cart\' value=\'{new_id}\'')
                # Replace data-product_id values
                content = content.replace(f'data-product_id="{orig_id}"', f'data-product_id="{new_id}"')
                content = content.replace(f'data-product_id=\'{orig_id}\'', f'data-product_id=\'{new_id}\'')
                # Replace other value attributes in form actions or selectors if any
                content = content.replace(f'value="{orig_id}"', f'value="{new_id}"') # Wait, value="{orig_id}" might be too generic.
                # Let's target more specific forms if we can, or general replacements since product IDs are unique numbers.
                
            if content != original_content:
                with open(file_path, "w", encoding="utf-8") as f:
                    f.write(content)
                print(f"Updated product IDs in: {os.path.basename(file_path)}")
        except Exception as e:
            print(f"Error updating file {file_path}: {e}")
            
    print("E-commerce dynamic integration completed successfully!")

if __name__ == "__main__":
    main()
