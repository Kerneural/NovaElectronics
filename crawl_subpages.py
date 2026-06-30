import os
import re
import ssl
import json
import urllib.request
from urllib.parse import urlparse, urljoin

# Config
target_domain = "dienmay8.mauthemewp.com"
base_url = f"https://{target_domain}"
base_dir = os.path.dirname(os.path.abspath(__file__))
src_dir = os.path.join(base_dir, "src")
html_path = os.path.join(base_dir, "index.html")
theme_dir = os.path.join(src_dir, "wp-content", "themes", "dienmay8-clone")
templates_dir = os.path.join(theme_dir, "templates")

# Ensure directories exist
os.makedirs(templates_dir, exist_ok=True)

# Ignore SSL verification
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'}

downloaded_assets = set()

# Prepopulate already downloaded assets to avoid downloading them again
# We can traverse src/wp-content/ and src/wp-includes/ if we want,
# but keeping track of downloaded files in a set is simpler. We will just check if file exists.

def check_and_download_asset(url):
    parsed = urlparse(url)
    if parsed.netloc != target_domain:
        return
    
    path = parsed.path
    local_rel_path = path.lstrip('/')
    local_file_path = os.path.join(src_dir, local_rel_path)
    
    if os.path.exists(local_file_path):
        return # Already exists
        
    os.makedirs(os.path.dirname(local_file_path), exist_ok=True)
    try:
        req = urllib.request.Request(url, headers=headers)
        print(f"Downloading new asset: {url} -> {local_file_path}")
        with urllib.request.urlopen(req, context=ctx) as response:
            with open(local_file_path, "wb") as f:
                f.write(response.read())
    except Exception as e:
        print(f"Failed to download asset {url}: {e}")

def parse_and_download_page_assets(html_content):
    # Find all potential asset URLs (images, CSS, JS, etc.)
    urls = re.findall(r'src=["\'](https?://dienmay8\.mauthemewp\.com/[^\s\'">]+)["\']', html_content)
    urls += re.findall(r'href=["\'](https?://dienmay8\.mauthemewp\.com/[^\s\'">]+\.(?:css|png|jpg|jpeg|gif|svg|woff2|woff|ttf))["\']', html_content)
    
    for url in urls:
        url = url.replace('\\/', '/')
        # Strip query params/hash for path checking
        parsed = urlparse(url)
        if any(parsed.path.lower().endswith(ext) for ext in ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.woff', '.woff2', '.ttf', '.eot', '.ico', '.webp']):
            check_and_download_asset(url)

def sanitize_template_name(path):
    # Remove leading/trailing slashes
    path = path.strip('/')
    if not path:
        return "homepage"
    # Replace slashes and other chars with hyphens
    sanitized = re.sub(r'[^a-zA-Z0-9\-]', '-', path)
    return sanitized

def main():
    print("Reading homepage to find subpages...")
    with open(html_path, "r", encoding="utf-8") as f:
        html = f.read()
        
    # Extract links in href attributes
    links = re.findall(r'href=["\'](https?://dienmay8\.mauthemewp\.com/[^\s\'">]+)["\']', html)
    
    subpage_urls = set()
    for link in links:
        link = link.replace('\\/', '/')
        parsed = urlparse(link)
        path = parsed.path
        
        # Exclude assets and system endpoints
        exclude_exts = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.woff', '.woff2', '.ttf', '.eot', '.ico', '.webp', '.xml', '.json']
        if any(path.lower().endswith(ext) for ext in exclude_exts):
            continue
        
        # Exclude feeds or json endpoints
        if '/feed' in path or '/wp-json' in path or 'xmlrpc.php' in path:
            continue
            
        # We only want internal links
        if parsed.netloc == target_domain:
            subpage_urls.add(link)
            
    print(f"Found {len(subpage_urls)} unique internal subpage URLs.")
    
    routes = {}
    
    for i, url in enumerate(sorted(subpage_urls)):
        parsed = urlparse(url)
        path = parsed.path
        
        # Normalize route path (e.g. "gioi-thieu")
        normalized_route = path.strip('/')
        if not normalized_route:
            # Homepage is already handled by index.php
            continue
            
        template_name = sanitize_template_name(path) + ".php"
        template_path = os.path.join(templates_dir, template_name)
        
        print(f"[{i+1}/{len(subpage_urls)}] Crawling: {url} -> {template_name}")
        
        try:
            req = urllib.request.Request(url, headers=headers)
            with urllib.request.urlopen(req, context=ctx) as response:
                page_html = response.read().decode('utf-8', errors='ignore')
                
            # Scan and download new assets loaded by this page
            parse_and_download_page_assets(page_html)
            
            # Rewrite URLs in subpage HTML
            rewritten_html = page_html.replace("https://dienmay8.mauthemewp.com/", "/")
            rewritten_html = rewritten_html.replace("https:\\/\\/dienmay8.mauthemewp.com\\/", "\\/")
            rewritten_html = rewritten_html.replace("http://dienmay8.maugiaodien.com", "/")
            
            # Save rewritten content to template
            with open(template_path, "w", encoding="utf-8") as f:
                f.write(rewritten_html)
                
            routes[normalized_route] = f"templates/{template_name}"
            print(f"Saved template: {template_name}")
            
        except Exception as e:
            print(f"Error crawling {url}: {e}")
            
    # Write routes map to a JSON file in the theme
    routes_json_path = os.path.join(theme_dir, "routes.json")
    with open(routes_json_path, "w", encoding="utf-8") as f:
        json.dump(routes, f, indent=4)
        
    print(f"Successfully generated {len(routes)} subpage templates.")
    print(f"Routes map written to {routes_json_path}")

if __name__ == "__main__":
    main()
