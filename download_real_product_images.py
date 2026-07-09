import os
import urllib.request

products_images = {
    "asus-rog-zephyrus-g14": "https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=600&auto=format&fit=crop",
    "apple-macbook-air-m2": "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&auto=format&fit=crop",
    "dell-xps-13-ultrabook": "https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=600&auto=format&fit=crop",
    "samsung-32-inch-gaming-monitor": "https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=600&auto=format&fit=crop",
    "logitech-mx-master-3s-mouse": "https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=600&auto=format&fit=crop",
    
    "ring-video-doorbell-wired": "https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=600&auto=format&fit=crop",
    "google-nest-learning-thermostat": "https://images.unsplash.com/photo-1595818947265-1d428bf932c0?w=600&auto=format&fit=crop",
    "eufy-security-solocam-s40": "https://images.unsplash.com/photo-1557862921-37829c790f19?w=600&auto=format&fit=crop",
    "august-wi-fi-smart-lock": "https://images.unsplash.com/photo-1507208773393-4019672c4716?w=600&auto=format&fit=crop",
    "tp-link-tapo-smart-plug": "https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=600&auto=format&fit=crop",
    
    "lg-front-load-smart-washer": "https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?w=600&auto=format&fit=crop",
    "dyson-v15-detect-vacuum": "https://images.unsplash.com/photo-1558317374-067fb5f30001?w=600&auto=format&fit=crop",
    "honeywell-hepa-air-purifier": "https://images.unsplash.com/photo-1585338107529-13afc5f02586?w=600&auto=format&fit=crop",
    "honeywell-uberheat-ceramic-heater": "https://images.unsplash.com/photo-1604147706283-d7119b5b822c?w=600&auto=format&fit=crop",
    "dreame-l10s-ultra-robot-vacuum": "https://images.unsplash.com/photo-1569698134101-f15cde5cd66c?w=600&auto=format&fit=crop",
    
    "instant-pot-duo-plus": "https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=600&auto=format&fit=crop",
    "keurig-k-elite-coffee-maker": "https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=600&auto=format&fit=crop",
    "ninja-professional-plus-blender": "https://images.unsplash.com/photo-1578643463396-0997cb5328c1?w=600&auto=format&fit=crop",
    "cosori-pro-ii-air-fryer": "https://images.unsplash.com/photo-1621972750749-0fbb1abb7736?w=600&auto=format&fit=crop",
    "kitchenaid-artisan-stand-mixer": "https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop",
    
    "anker-737-power-bank": "https://images.unsplash.com/photo-1609592806453-6a27ebd750ef?w=600&auto=format&fit=crop",
    "belkin-3-in-1-wireless-charger": "https://images.unsplash.com/photo-1622445262465-2481c4574875?w=600&auto=format&fit=crop",
    "jackery-portable-power-station-240": "https://images.unsplash.com/photo-1624996379697-f01d168b1a52?w=600&auto=format&fit=crop",
    "nekteck-60w-usb-c-charger": "https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=600&auto=format&fit=crop",
    "baseus-65w-gan3-pro-station": "https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=600&auto=format&fit=crop",
    
    "sony-playstation-5-console": "https://images.unsplash.com/photo-1606813907291-d86efa9b94db?w=600&auto=format&fit=crop",
    "nintendo-switch-oled-model": "https://images.unsplash.com/photo-1578301978693-85fa9c0320b9?w=600&auto=format&fit=crop",
    "xbox-series-x-console": "https://images.unsplash.com/photo-1605901309584-818e25960a8f?w=600&auto=format&fit=crop",
    "steelseries-arctis-nova-pro": "https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=600&auto=format&fit=crop",
    "meta-quest-3-vr-headset": "https://images.unsplash.com/photo-1593508512255-86ab42a8e620?w=600&auto=format&fit=crop",
    
    "rexing-v1-dash-cam": "https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=600&auto=format&fit=crop",
    "noco-boost-plus-gb40": "https://images.unsplash.com/photo-1486006920555-c77dce18193b?w=600&auto=format&fit=crop",
    "astroai-digital-tire-inflator": "https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=600&auto=format&fit=crop",
    "garmin-drivesmart-65-gps": "https://images.unsplash.com/photo-1542751371-adc38448a05e?w=600&auto=format&fit=crop",
    "garmin-instinct-2-smartwatch": "https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop",
    
    "oral-b-io-series-9": "https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?w=600&auto=format&fit=crop",
    "waterpik-aquarius-water-flosser": "https://images.unsplash.com/photo-1597362925123-77861d3fbac7?w=600&auto=format&fit=crop",
    "philips-norelco-oneblade-pro": "https://images.unsplash.com/photo-1621607512214-68297480165e?w=600&auto=format&fit=crop",
    "theragun-prime-massage-gun": "https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=600&auto=format&fit=crop",
    "braun-series-9-pro-shaver": "https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=600&auto=format&fit=crop",
    
    "epson-ecotank-et-2800": "https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=600&auto=format&fit=crop",
    "brother-p-touch-ptd210": "https://images.unsplash.com/photo-1562240020-ce31ccb0fa7d?w=600&auto=format&fit=crop",
    "canon-canoscan-lide-300": "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600&auto=format&fit=crop",
    "texas-instruments-ti-84-plus": "https://images.unsplash.com/photo-1603508544113-5a719330113c?w=600&auto=format&fit=crop",
    "hp-12c-financial-calculator": "https://images.unsplash.com/photo-1518133680790-399083047bb4?w=600&auto=format&fit=crop",
    
    "philips-hue-starter-kit": "https://images.unsplash.com/photo-1550523671-77861d3fbac7?w=600&auto=format&fit=crop",
    "nanoleaf-shapes-hexagons": "https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=600&auto=format&fit=crop",
    "govee-rgbic-led-strip-lights": "https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?w=600&auto=format&fit=crop",
    "lifx-color-a19-bulb": "https://images.unsplash.com/photo-1507646227500-4d389b0012be?w=600&auto=format&fit=crop",
    "kasa-smart-light-switch": "https://images.unsplash.com/photo-1558002038-1055907df827?w=600&auto=format&fit=crop"
}

dest_dir = "wp-content/uploads/2022/11"
os.makedirs(dest_dir, exist_ok=True)

print("Downloading accurate unique product images...")
headers = {'User-Agent': 'Mozilla/5.0'}
downloaded_count = 0

for slug, url in products_images.items():
    dest_path = os.path.join(dest_dir, f"{slug}.jpg")
    try:
        req = urllib.request.Request(url, headers=headers)
        with urllib.request.urlopen(req) as response, open(dest_path, 'wb') as out_file:
            out_file.write(response.read())
        print(f"  [OK] Downloaded & Overwrote {slug}.jpg")
        downloaded_count += 1
    except Exception as e:
        print(f"  [ERROR] Failed to download {slug}: {e}")

print(f"Finished downloading {downloaded_count}/50 product images!")
