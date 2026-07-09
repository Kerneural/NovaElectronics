import os
import urllib.request

refined_images = {
    "cosori-pro-ii-air-fryer": "https://images.unsplash.com/photo-1588854337236-6889d631faa8?w=600&auto=format&fit=crop",
    "dreame-l10s-ultra-robot-vacuum": "https://images.unsplash.com/photo-1562408590-e32931084e23?w=600&auto=format&fit=crop",
    "dyson-v15-detect-vacuum": "https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?w=600&auto=format&fit=crop",
    "honeywell-hepa-air-purifier": "https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=600&auto=format&fit=crop",
    "brother-p-touch-ptd210": "https://images.unsplash.com/photo-1586075010923-2dd4570fb338?w=600&auto=format&fit=crop",
    "canon-canoscan-lide-300": "https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=600&auto=format&fit=crop",
    "texas-instruments-ti-84-plus": "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&auto=format&fit=crop"
}

dest_dir = "wp-content/uploads/2022/11"

print("Refining remaining product images...")
headers = {'User-Agent': 'Mozilla/5.0'}
downloaded_count = 0

for slug, url in refined_images.items():
    dest_path = os.path.join(dest_dir, f"{slug}.jpg")
    try:
        req = urllib.request.Request(url, headers=headers)
        with urllib.request.urlopen(req) as response, open(dest_path, 'wb') as out_file:
            out_file.write(response.read())
        print(f"  [OK] Refined {slug}.jpg")
        downloaded_count += 1
    except Exception as e:
        print(f"  [ERROR] Failed to refine {slug}: {e}")

print(f"Finished refining {downloaded_count}/7 product images!")
