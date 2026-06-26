# Running Shoes E-Commerce Website

PHP-based e-commerce platform for running shoes with product catalog, cart, checkout, user accounts, and admin panel.

## Setup

```bash
# 1. Create database
mysql -u root -p -e "CREATE DATABASE runningdb"

# 2. Import schema + seed data
mysql -u root -p runningdb < running_shoes_website_db.sql
mysql -u root -p runningdb < seed.sql

# 3. Configure env
cp .env.example .env
# Edit .env with your DB credentials

# 4. Start dev server
php -S localhost:8000
```

## Project State

- Product catalog with gender-based filtering (men/women), new arrivals, sale items, search
- Product detail with color swatches (click to swap image), size selection
- Shopping cart (add/remove), checkout with shipping rules
- User auth (register/login), account dashboard
- Admin panel for products, variants, orders, refunds, inventory
- Variant-level discount system (percentage/fixed)
- Responsive design
- Server-side filtering (price, color, material, size)
- Database: MySQL/MariaDB via PDO
- Stack: PHP 8+, Vanilla JS, jQuery, GSAP
