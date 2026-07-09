import os
import urllib.request

failed_images = {
    "google-nest-learning-thermostat": "https://images.unsplash.com/photo-1558002038-1055907df827?w=600&auto=format&fit=crop",
    "august-wi-fi-smart-lock": "https://images.unsplash.com/photo-1582139329536-e7284fece509?w=600&auto=format&fit=crop",
    "anker-737-power-bank": "https://images.unsplash.com/photo-1591405351990-4726e331f141?w=600&auto=format&fit=crop",
    "belkin-3-in-1-wireless-charger": "https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=600&auto=format&fit=crop",
    "noco-boost-plus-gb40": "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=600&auto=format&fit=crop",
    "texas-instruments-ti-84-plus": "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&auto=format&fit=crop",
    "hp-12c-financial-calculator": "https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=600&auto=format&fit=crop",
    "philips-hue-starter-kit": "https://images.unsplash.com/photo-1517483000871-1dbf64a6e1c6?w=600&auto=format&fit=crop"
}

dest_dir = "wp-content/uploads/2022/11"

print("Downloading alternative images for failed downloads...")
headers = {'User-Agent': 'Mozilla/5.0'}
downloaded_count = 0

for slug, url in failed_images.items():
    dest_path = os.path.join(dest_dir, f"{slug}.jpg")
    try:
        req = urllib.request.Request(url, headers=headers)
        with urllib.request.urlopen(req) as response, open(dest_path, 'wb') as out_file:
            out_file.write(response.read())
        print(f"  [OK] Downloaded alternative for {slug}.jpg")
        downloaded_count += 1
    except Exception as e:
        print(f"  [ERROR] Failed to download alternative for {slug}: {e}")

print(f"Finished downloading alternative {downloaded_count}/8 product images!")
