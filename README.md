# Running Shoes E-Commerce Website

A PHP-based e-commerce platform for running shoes, featuring product catalog browsing, filtering, cart management, checkout, user accounts, and admin panel.

## Tech Stack

- **Backend:** PHP 8+, MariaDB/MySQL
- **Frontend:** Vanilla JS, jQuery, GSAP (animations)
- **Database:** MySQL via PDO
- **Architecture:** Custom MVC (Controllers, Models, Views)

## Project Structure

```
├── config/              # Database connection
├── controllers/         # Route handlers (ShopAll, Mens, Womens, Sale, Cart, etc.)
├── models/              # Database models (Product, ProductVariant, Discount, Order, etc.)
├── views/               # HTML templates
│   ├── components/      # Reusable UI (navbar, footer, filter-panel, product-card, etc.)
│   ├── tabs/            # Mega-menu dropdown content (men, women, sale)
│   └── admin/           # Admin dashboard views
├── assets/              # CSS, JS, images
│   ├── css/
│   ├── js/
│   └── images/
├── utils/               # Helper functions
├── index.php            # Entry point
├── routes.php           # Route definitions
├── seed.sql             # Sample data (products, variants, discounts, etc.)
└── running_shoes_website_db.sql  # Database schema
```

## Features

- **Product Catalog** — Browse all products, filter by gender (men/women), new arrivals, sale items
- **Client-Side Filtering** — Filter by price, color, material, and size with cascading disable
- **Product Detail** — View product images, color swatches, size selection
- **Shopping Cart** — Add/remove items, quantity management
- **Checkout** — Order placement with city-based shipping rules
- **User Authentication** — Register, login, account management
- **Search** — Product search with results grid
- **Admin Panel** — Manage products, variants, orders, refunds, inventory
- **Sales & Discounts** — Variant-level discount system with percentage/fixed types
- **Responsive Design** — Adapts to desktop and mobile

## Routes

| Route | Page |
|-------|------|
| `?route=home` | Homepage |
| `?route=shop-all` | All products |
| `?route=mens` | Men's products |
| `?route=womens` | Women's products |
| `?route=new-arrivals` | New arrivals |
| `?route=sale` | Sale/discounted products |
| `?route=product&id=N` | Product detail |
| `?route=search&q=...` | Search results |
| `?route=cart` | Shopping cart |
| `?route=checkout` | Checkout |
| `?route=login` | Login |
| `?route=register` | Register |
| `?route=account` | Account dashboard |
| `?route=admin/*` | Admin panel |

## Getting Started

### Prerequisites

- PHP 8.0+
- MariaDB 10+ / MySQL 8+
- Apache or Nginx (with PHP-FPM)
- Composer (optional, for any PHP packages)

### Setup

1. **Clone the repo**
   ```bash
   git clone <repo-url>
   cd Running-shoes-ecom-website
   ```

2. **Configure the database**
   ```bash
   # Import schema
   mysql -u root -p < running_shoes_website_db.sql

   # Import seed data (optional, for sample products)
   mysql -u root -p runningdb < seed.sql
   ```

3. **Configure environment**
   Create a `.env` file in the project root:
   ```
   DB_HOST=localhost
   DB_NAME=runningdb
   DB_USER=g13
   DB_PASS=yesnomaybeso
   ```

4. **Serve the application**

   **Option A — PHP built-in server (development)**
   ```bash
   php -S localhost:8000
   ```
   Then open `http://localhost:8000` in your browser.

   **Option B — Apache/Nginx**
   Point your web root to the project directory. Ensure `index.php` is the default document.

### Default Credentials

- **Admin:** admin@admin.com / password123
- **Client:** (register through the site)

## Database

The database schema (`running_shoes_website_db.sql`) includes these tables:

- `Brands`, `Categories` — Product taxonomy
- `Products` — Product listings with base prices
- `Product_variants` — Size/color variants with stock, pricing, and discount linkage
- `Product_img` — Multi-angle product images
- `Discounts` — Sale/discount rules with date ranges
- `Orders`, `Order_items` — Customer orders
- `Users` — Customers and admins
- `Cities`, `Shipping_rules` — Shipping zones
- `Refunds`, `Inventory_logs` — Admin operations
- `Audit_logs` — Change tracking
