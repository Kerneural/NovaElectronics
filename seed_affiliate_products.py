import os
import sys
import subprocess

# Paths
workspace_dir = os.path.dirname(os.path.abspath(__file__))

def main():
    print("=========================================")
    print("  Affiliate Products Seeder Entrypoint   ")
    print("=========================================")
    print("Running high-performance PHP database seeder inside container...")
    
    cmd = [
        "docker", "compose", "run", "--rm", "wp-cli", 
        "wp", "eval-file", "/var/www/html/seed_affiliate_products.php", "--allow-root"
    ]
    
    try:
        result = subprocess.run(cmd, cwd=workspace_dir, capture_output=False, text=True, shell=True)
        if result.returncode == 0:
            print("\nDatabase seeding finished successfully!")
            sys.exit(0)
        else:
            print(f"\nSeeding script failed with exit code: {result.returncode}", file=sys.stderr)
            sys.exit(result.returncode)
    except Exception as e:
        print(f"\nException executing seeder: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()
