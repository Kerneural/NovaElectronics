# WordPress Hybrid E-Commerce Clone (Flatsome + WooCommerce)

A portable, high-performance hybrid WordPress e-commerce application. It combines crawled high-fidelity static HTML pages for instantaneous page loading with dynamic WooCommerce endpoints (Cart, Checkout, and Accounts) styled natively by the Flatsome parent theme.

This repository is structured for seamless local Docker development, dynamic database population, and automated VPS deployment (such as 1Panel Git webhook integration).

---

## Architecture Overview

```
                      +-------------------+
                      |   Client Request  |
                      +---------+---------+
                                |
                                v
                      +---------+---------+
                      |   Theme Router    |
                      |    (index.php)    |
                      +---------+---------+
                                |
        +-----------------------+-----------------------+
        | Path is in routes.json                        | Path NOT in routes.json
        v                                               v
+-------+---------------+                       +-------+---------------+
| Static Template       |                       | Valid WP DB Query?    |
| (High Fidelity HTML)  |                       +-------+-------+-------+
+-----------------------+                               |       |
                                                    Yes |       | No (Real 404)
                                                        v       v
                                                +-------+---+  +-------+---+
                                                | Flatsome  |  | Homepage  |
                                                | Engine    |  | Redirect  |
                                                +-----------+  +-----------+
```

1.  **Static Layer**: Predetermined routes (Home, Products, Categories, Blog, etc.) map directly to optimized crawled templates inside `src/wp-content/themes/dienmay8-clone/templates/`.
2.  **Dynamic Layer**: Dynamic pages (`/gio-hang/`, `/thanh-toan/`, `/tai-khoan/`) bypass static routing and fall back to the Flatsome parent theme and WooCommerce engine, allowing real checkouts and account logins.
3.  **Dynamic Binding**: Interactive buy buttons and forms on crawled templates submit the correct local WooCommerce product IDs to the database, enabling unified cart updates.

---

## Prerequisites

Ensure you have the following installed on your host machine:
*   [Docker](https://www.docker.com/) & Docker Compose
*   [Python 3.x](https://www.python.org/)
*   The official Flatsome parent theme zip (`flatsome.zip`) placed in the root directory.

---

## Getting Started

A unified setup script automates the installation, asset downloading, template crawling, theme activation, and database seed.

### 1. Windows (PowerShell)
Open PowerShell inside the repository root and run:
```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force
.\setup.ps1
```

### 2. Linux / macOS / WSL (Bash)
Open your terminal inside the repository root and run:
```bash
chmod +x setup.sh
./setup.sh
```

---

## Local Development Configurations

Once the setup script finishes successfully, the services will be running under the following parameters:

### Port Bindings
*   **Web Server (WordPress)**: [http://localhost:8080/](http://localhost:8080/)
*   **Database (MySQL)**: `127.0.0.1:3306`

### Credentials
*   **WordPress Admin User**: `admin`
*   **WordPress Admin Password**: `adminpassword`
*   **Database User**: `wordpress`
*   **Database Password**: `wordpress_db_pass_123`
*   **Database Name**: `wordpress`

---

## Directory Structure

```
.
├── src/                                  # Mounted webroot for WordPress container
│   └── wp-content/
│       └── themes/
│           ├── flatsome/                 # Parent theme (Flatsome)
│           └── dienmay8-clone/           # Custom child theme
│               ├── templates/            # Crawled high-fidelity subpage layouts
│               ├── index.php             # Request Router
│               ├── style.css             # Theme metadata (Parent template declared)
│               └── products_map.json     # Remote ID to Local ID mapping
├── docker-compose.yml                    # Docker Compose configuration (App, DB, WP-CLI)
├── setup_flatsome.py                     # Flatsome extraction utility
├── download_assets.py                    # Static CSS/JS/images crawler
├── crawl_subpages.py                     # Content crawler generating child theme routes
├── setup_real_products.py                # Parses homepage, seeds WooCommerce, updates forms
├── setup.sh / setup.ps1                  # Orchestrated setup entrypoints
└── README.md
```

---

## Portability & Deploying to VPS (e.g. 1Panel)

All scripts use dynamic path resolution, making the repository completely portable. You can configure automated deployment on a VPS (such as 1Panel Git Manager) with the following Git-Flow:

1.  **Work with Pull Requests**: Team members branch, commit, and open PRs on GitHub.
2.  **Continuous Integration**: Merge verified PRs into the `main` branch.
3.  **1Panel Webhook**:
    *   Set up a new website Git repository on your 1Panel Dashboard mapping to `/public_html`.
    *   Under Actions, configure the Webhook URL and Secret.
    *   Add this Webhook to your GitHub repository under **Settings > Webhooks**.
    *   Every merge to `main` will trigger the Webhook and pull the latest code to the VPS instantly.
