import os
import re
import ssl
import urllib.request
from urllib.parse import urlparse, urljoin

# Config
target_domain = "dienmay8.mauthemewp.com"
base_url = f"https://{target_domain}"
base_dir = os.path.dirname(os.path.abspath(__file__))
src_dir = os.path.join(base_dir, "src")
html_path = os.path.join(base_dir, "index.html")

# Ignore SSL verification
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'}

downloaded_urls = set()

def download_file(url, local_path):
    if url in downloaded_urls:
        return True
    
    # Create directories if they don't exist
    os.makedirs(os.path.dirname(local_path), exist_ok=True)
    
    try:
        req = urllib.request.Request(url, headers=headers)
        print(f"Downloading: {url} -> {local_path}")
        with urllib.request.urlopen(req, context=ctx) as response:
            with open(local_path, "wb") as f:
                f.write(response.read())
        downloaded_urls.add(url)
        return True
    except Exception as e:
        print(f"Failed to download {url}: {e}")
        return False

def scan_and_download_css_assets(css_url, css_local_path):
    try:
        with open(css_local_path, "r", encoding="utf-8", errors="ignore") as f:
            content = f.read()
    except Exception as e:
        print(f"Failed to read CSS file {css_local_path}: {e}")
        return

    # Find url(...) patterns in CSS
    urls = re.findall(r'url\s*\(\s*[\'"]?([^\'"\)]+)[\'"]?\s*\)', content)
    for rel_url in urls:
        # Ignore data URLs or absolute HTTP URLs to other domains
        if rel_url.startswith("data:") or rel_url.startswith("http://") or rel_url.startswith("https://"):
            if not rel_url.startswith(base_url):
                continue
        
        # Resolve URL relative to the CSS file URL
        resolved_url = urljoin(css_url, rel_url)
        parsed_resolved = urlparse(resolved_url)
        
        # Only download if it belongs to our target domain
        if parsed_resolved.netloc == target_domain:
            # Clean URL (remove query strings like ?v=3.1)
            clean_path = parsed_resolved.path
            local_rel_path = clean_path.lstrip('/')
            local_file_path = os.path.join(src_dir, local_rel_path)
            
            # Download asset
            if download_file(resolved_url, local_file_path):
                # If it's a nested CSS file, scan it recursively
                if clean_path.endswith(".css"):
                    scan_and_download_css_assets(resolved_url, local_file_path)

def main():
    print("Starting asset download and processing...")
    
    # Read HTML
    with open(html_path, "r", encoding="utf-8") as f:
        html = f.read()
    
    # Find all references starting with base_url or relative paths like /wp-content/ or /wp-includes/
    # 1. Absolute URLs on the target domain
    abs_urls = re.findall(r'https?://dienmay8\.mauthemewp\.com/[^\s\'">]+', html)
    # 2. Relative URLs starting with /wp-content/ or /wp-includes/
    rel_wp_urls = re.findall(r'/(?:wp-content|wp-includes)/[^\s\'">]+', html)
    
    all_candidate_urls = set()
    
    # Clean up absolute URLs (remove backslashes used in JSON scripts)
    for url in abs_urls:
        url = url.replace('\\/', '/')
        # Remove trailing characters that might be part of attributes
        url = url.split(')')[0].split(',')[0].split('"')[0].split("'")[0]
        all_candidate_urls.add(url)
        
    for url in rel_wp_urls:
        url = url.replace('\\/', '/')
        url = url.split(')')[0].split(',')[0].split('"')[0].split("'")[0]
        # Make absolute
        abs_url = base_url + url
        all_candidate_urls.add(abs_url)
        
    # Filter only static files
    asset_exts = {'.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.woff', '.woff2', '.ttf', '.eot', '.ico', '.webp'}
    assets_to_download = []
    
    for url in all_candidate_urls:
        parsed = urlparse(url)
        path = parsed.path
        
        is_asset = False
        if any(path.lower().endswith(ext) for ext in asset_exts):
            is_asset = True
        elif '/wp-content/uploads/' in path:
            is_asset = True
        elif ('/wp-content/' in path or '/wp-includes/' in path) and '.' in path.split('/')[-1]:
            is_asset = True
            
        if is_asset:
            assets_to_download.append((url, path))
            
    print(f"Identified {len(assets_to_download)} static assets to download.")
    
    # Download assets
    css_files = []
    for url, path in assets_to_download:
        local_rel_path = path.lstrip('/')
        local_file_path = os.path.join(src_dir, local_rel_path)
        
        if download_file(url, local_file_path):
            if path.endswith(".css"):
                css_files.append((url, local_file_path))
                
    # Scan CSS files for fonts/images and download them
    print(f"Scanning {len(css_files)} CSS files for nested assets...")
    for css_url, local_path in css_files:
        scan_and_download_css_assets(css_url, local_path)
        
    # Create the custom theme directory
    theme_dir = os.path.join(src_dir, "wp-content", "themes", "dienmay8-clone")
    os.makedirs(theme_dir, exist_ok=True)
    
    # Rewrite the HTML contents:
    # 1. Replace all absolute references to target domain with root relative paths
    rewritten_html = html.replace("https://dienmay8.mauthemewp.com/", "/")
    rewritten_html = rewritten_html.replace("https:\\/\\/dienmay8.mauthemewp.com\\/", "\\/")
    
    # 2. Also replace "http://dienmay8.maugiaodien.com" if any
    rewritten_html = rewritten_html.replace("http://dienmay8.maugiaodien.com", "/")
    
    # Write to the theme's index.php
    theme_index_path = os.path.join(theme_dir, "index.php")
    with open(theme_index_path, "w", encoding="utf-8") as f:
        f.write(rewritten_html)
    print(f"Created theme index.php at {theme_index_path}")
    
    # Create theme style.css
    theme_style_path = os.path.join(theme_dir, "style.css")
    style_content = """/*
Theme Name: Dienmay8 Clone
Theme URI: http://localhost:8080/
Description: 100% Clone of Dienmay8 layout using exported HTML.
Version: 1.0
Author: Antigravity
Author URI: https://github.com/google-deepmind
Text Domain: dienmay8-clone
*/
"""
    with open(theme_style_path, "w", encoding="utf-8") as f:
        f.write(style_content)
    print(f"Created theme style.css at {theme_style_path}")

    # Create empty functions.php
    theme_functions_path = os.path.join(theme_dir, "functions.php")
    with open(theme_functions_path, "w", encoding="utf-8") as f:
        f.write("<?php\n// Custom theme functions\n")
    print(f"Created theme functions.php at {theme_functions_path}")
    
    print("Asset download and theme preparation complete!")

if __name__ == "__main__":
    main()
