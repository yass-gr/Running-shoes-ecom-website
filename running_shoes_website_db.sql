-- ============================================================
-- Running Shoes E-Commerce Website — Database Schema
-- Database: runningdb
-- ============================================================

CREATE DATABASE IF NOT EXISTS runningdb
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE runningdb;

-- ── Brands ────────────────────────────────────────────────────
CREATE TABLE Brands (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ── Categories (material-based, manually supplied ID) ─────────
CREATE TABLE Categories (
    id       INT PRIMARY KEY,
    material VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ── Products ──────────────────────────────────────────────────
CREATE TABLE Products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    brand_id    INT NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    base_price  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    sales       INT NOT NULL DEFAULT 0,
    gender      ENUM('mens','womens','unisex') NOT NULL DEFAULT 'unisex',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id)    REFERENCES Brands(id)    ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Product Images ────────────────────────────────────────────
CREATE TABLE Product_img (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    thumbnail   VARCHAR(500) DEFAULT NULL,
    top_view    VARCHAR(500),
    bottom_view VARCHAR(500),
    side_view   VARCHAR(500),
    pair_view   VARCHAR(500)
) ENGINE=InnoDB;

-- ── Discounts / Coupons ───────────────────────────────────────
CREATE TABLE Discounts (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    code           VARCHAR(50) UNIQUE,
    discount_type  ENUM('fixed','%') NOT NULL DEFAULT '%',
    value          DECIMAL(10,2) NOT NULL,
    start_date     DATETIME,
    end_date       DATETIME,
    n_uses         INT,
    is_active      TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ── Product Variants (size/color/SKU/stock) ───────────────────
CREATE TABLE Product_variants (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    product_id        INT NOT NULL,
    womens_variant_id INT DEFAULT NULL,
    size              DECIMAL(4,1) NOT NULL,
    color             VARCHAR(100) NOT NULL,
    sku               VARCHAR(100) NOT NULL UNIQUE,
    stock_quantity    INT NOT NULL DEFAULT 0,
    product_img_id    INT DEFAULT NULL,
    variant_price     DECIMAL(10,2) NOT NULL,
    reorder_level     INT NOT NULL DEFAULT 5,
    discount_id       INT DEFAULT NULL,
    FOREIGN KEY (product_id)        REFERENCES Products(id)         ON DELETE CASCADE,
    FOREIGN KEY (womens_variant_id) REFERENCES Product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (product_img_id)    REFERENCES Product_img(id)      ON DELETE SET NULL,
    FOREIGN KEY (discount_id)       REFERENCES Discounts(id)        ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Collections ───────────────────────────────────────────────
CREATE TABLE Collections (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(200) NOT NULL,
    description  TEXT,
    img          VARCHAR(500),
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    is_limited   TINYINT(1) NOT NULL DEFAULT 0,
    release_date DATE DEFAULT NULL
) ENGINE=InnoDB;

-- ── Product <-> Collection pivot ──────────────────────────────
CREATE TABLE Product_collections (
    product_id    INT NOT NULL,
    collection_id INT NOT NULL,
    PRIMARY KEY (product_id, collection_id),
    FOREIGN KEY (product_id)     REFERENCES Products(id)    ON DELETE CASCADE,
    FOREIGN KEY (collection_id)  REFERENCES Collections(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Shipping Rules ────────────────────────────────────────────
CREATE TABLE Shipping_rules (
    id                      INT AUTO_INCREMENT PRIMARY KEY,
    name                    VARCHAR(100) NOT NULL,
    price                   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    delivery_commission     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    free_shipping_threshold DECIMAL(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;

-- ── Cities ────────────────────────────────────────────────────
CREATE TABLE Cities (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(100) NOT NULL,
    shipping_rule_id INT DEFAULT NULL,
    FOREIGN KEY (shipping_rule_id) REFERENCES Shipping_rules(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Users ─────────────────────────────────────────────────────
CREATE TABLE Users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name  VARCHAR(100) NOT NULL,
    email      VARCHAR(255) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin','delivery_guy','user') NOT NULL DEFAULT 'user',
    city_id    INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES Cities(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Orders ────────────────────────────────────────────────────
CREATE TABLE Orders (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    client_id        INT NOT NULL,
    city_id          INT NOT NULL,
    subtotal         DECIMAL(10,2) NOT NULL,
    discount_id      INT DEFAULT NULL,
    shipping_status  ENUM('pending','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
    delivery_guy_id  INT DEFAULT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at     DATETIME DEFAULT NULL,
    cancelled_at     DATETIME DEFAULT NULL,
    FOREIGN KEY (client_id)       REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id)         REFERENCES Cities(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_id)     REFERENCES Discounts(id) ON DELETE SET NULL,
    FOREIGN KEY (delivery_guy_id) REFERENCES Users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Order Line Items ──────────────────────────────────────────
CREATE TABLE Order_items (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    order_id         INT NOT NULL,
    variant_id       INT NOT NULL,
    quantity         INT NOT NULL DEFAULT 1,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES Orders(id)           ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES Product_variants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Reviews ───────────────────────────────────────────────────
CREATE TABLE Reviews (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT NOT NULL,
    product_id        INT NOT NULL,
    rating            TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment           TEXT,
    verified_purchase TINYINT(1) NOT NULL DEFAULT 0,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES Users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Refunds ───────────────────────────────────────────────────
CREATE TABLE Refunds (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT NOT NULL,
    amount     DECIMAL(10,2) NOT NULL,
    reason     TEXT,
    status     ENUM('processing','approved','rejected') NOT NULL DEFAULT 'processing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES Orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Refund Line Items ─────────────────────────────────────────
CREATE TABLE Refund_items (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    refund_id        INT NOT NULL,
    variant_id       INT NOT NULL,
    quantity         INT NOT NULL DEFAULT 1,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (refund_id)  REFERENCES Refunds(id)         ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES Product_variants(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Audit Logs (admin actions) ────────────────────────────────
CREATE TABLE Audit_logs (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    admin_id         INT NOT NULL,
    action_performed VARCHAR(255) NOT NULL,
    target_table     VARCHAR(100) NOT NULL,
    target_id        VARCHAR(50) NOT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES Users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Inventory Logs (restock events) ───────────────────────────
CREATE TABLE Inventory_logs (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    product_variant_id INT NOT NULL,
    admin_id           INT NOT NULL,
    quantity_added     INT NOT NULL,
    unit_price         DECIMAL(10,2) NOT NULL,
    restocked_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_variant_id) REFERENCES Product_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id)           REFERENCES Users(id)            ON DELETE CASCADE
) ENGINE=InnoDB;
