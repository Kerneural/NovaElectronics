import os
import zipfile
import shutil

zip_path = "flatsome.zip"
extract_dir = "src/wp-content/themes/flatsome"
nested_dir = "src/wp-content/themes/flatsome/flatsome"

if not os.path.exists(zip_path):
    print(f"Flatsome zip file not found at: {zip_path}. Please place flatsome.zip in the root directory.")
    exit(0) # Not failing, just skipping

print(f"Extracting {zip_path} to {extract_dir}...")
os.makedirs(extract_dir, exist_ok=True)

try:
    with zipfile.ZipFile(zip_path, 'r') as zip_ref:
        zip_ref.extractall(extract_dir)
    print("Extraction completed successfully!")
except Exception as e:
    print(f"Failed to extract: {e}")
    exit(1)

# Check if there is a nested folder and merge it
if os.path.exists(nested_dir):
    print(f"Merging nested folder {nested_dir} to {extract_dir}...")
    for item in os.listdir(nested_dir):
        src_item = os.path.join(nested_dir, item)
        dst_item = os.path.join(extract_dir, item)
        
        if os.path.isdir(src_item):
            if os.path.exists(dst_item):
                for subitem in os.listdir(src_item):
                    src_sub = os.path.join(src_item, subitem)
                    dst_sub = os.path.join(dst_item, subitem)
                    if os.path.isdir(src_sub):
                        shutil.copytree(src_sub, dst_sub, dirs_exist_ok=True)
                    else:
                        shutil.copy2(src_sub, dst_sub)
                shutil.rmtree(src_item)
            else:
                shutil.move(src_item, dst_item)
        else:
            if os.path.exists(dst_item):
                os.remove(dst_item)
            shutil.move(src_item, dst_item)
            
    os.rmdir(nested_dir)
    print("Nested folder cleaned up and merged successfully!")
