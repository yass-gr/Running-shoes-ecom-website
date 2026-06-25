#!/usr/bin/env python3
"""Generate seed.sql from allbirds_products.json — max 3 colors per product."""
import json, re, random
from urllib.parse import unquote

def decode(s):
    if not s: return ''
    s = unquote(s.replace('+', ' '))
    return s

def esc(s):
    s = decode(s)
    s = s.replace("'", "''")
    return s

# Load JSON
with open('testdata(temporary)/allbirds_products.json') as f:
    raw = json.load(f)

# Group by masterId
groups = {}
for item in raw:
    mid = item.get('masterId') or item.get('master') or item.get('handle', '')
    if not mid:
        continue
    key = mid.strip()
    if key not in groups:
        groups[key] = {
            'masterName': item.get('masterName') or '',
            'items': []
        }
    groups[key]['items'].append(item)

# Map masterId to a proper product name (deduced from first item's masterName)
for mid, g in groups.items():
    mn = g['masterName']
    if not mn:
        # Derive from first handle
        handle = g['items'][0].get('handle', '')
        parts = handle.split('-')
        mn = ' '.join(p.capitalize() for p in parts)
    g['product_name'] = esc(mn)

product_id = 39
img_id = 39  # start after existing img entries (1-38)
variant_id = 59  # start after existing variant entries (1-58)

CAT_MAP = {
    'wool': 1, 'tree': 2, 'canvas': 3, 'sugar': 4, 'cotton': 5,
    'corduroy': 6, 'alternative-leather': 7, 'luxe-collection': 8,
    'cozy-collection': 9, 'mesh': 10, 'knit': 11,
}

PRODUCT_ROWS = []
IMAGE_ROWS = []
VARIANT_ROWS = []
ALL_PRODUCT_IDS = []

seen_skus = set()

for mid, g in sorted(groups.items()):
    items = g['items']
    pname = g['product_name']

    # Remove "Men's" or "Women's" prefix from master name if it has one
    # (gender will be tracked per variant anyway)
    for prefix in ["men's ", "women's ", "unisex "]:
        if pname.lower().startswith(prefix):
            pname = pname[len(prefix):].strip()
            break

    # Detect gender from first item
    gender_tag = items[0].get('gender') or 'unisex'
    if gender_tag == 'mens':
        display_name = "Men's " + pname
    elif gender_tag == 'womens':
        display_name = "Women's " + pname
    else:
        display_name = pname

    # Skip socks/accessories with many color variants, keep a few
    lower_name = pname.lower()
    is_sock = any(t in lower_name for t in ['sock', 'ankle', 'crew', 'heel grip'])
    is_apparel = any(t in lower_name for t in ['sweatpant', 'sweatshirt'])

    # Pick max 3 colors
    selected = items[:3]

    # Material + category
    material = items[0].get('material') or 'cotton'
    cat_id = CAT_MAP.get(material, 5)

    # Description
    desc_raw = items[0].get('description', '')
    desc = esc(decode(desc_raw)) if desc_raw else f'Comfortable {material} shoe from Allbirds.'
    if len(desc) > 250:
        desc = desc[:247] + '...'

    # Price (convert cents to dollars)
    price_cents = items[0].get('price', 0)
    base_price = round(price_cents / 100.0, 2) if price_cents else 99.99

    random.seed(mid + '_sales')
    sales = random.randint(20, 600)

    # For each color
    for idx, item in enumerate(selected):
        img = item.get('images', [])
        thumbnail = ''
        top_view = ''
        side_view = ''
        bottom_view = ''
        pair_view = ''
        for im in img:
            pos = im.get('position', 0)
            src = im.get('src', '')
            if src.startswith('//'):
                src = 'https:' + src
            if pos == 1: thumbnail = src
            elif pos == 2: side_view = src
            elif pos == 3: top_view = src
            elif pos == 4: bottom_view = src
            elif pos >= 5 and not pair_view: pair_view = src
        if not thumbnail:
            thumbnail = 'https://www.allbirds.com/cdn/shop/files/placeholder.png'

        # Color name
        color_name_raw = item.get('colorName') or item.get('fullName', '') or f'Color {idx+1}'
        color_name = esc(color_name_raw)

        # Get sizes
        sizes_data = item.get('sizes', {})
        if not sizes_data:
            continue

        sale_price_cents = item.get('price', 0)
        variant_price = round(sale_price_cents / 100.0, 2) if sale_price_cents else base_price
        compare_at = item.get('compareAtPrice')
        discount_id = None
        if compare_at and compare_at > price_cents and base_price > variant_price:
            discount_id = 6  # SALE20

        for size_str in sorted(sizes_data.keys(),
            key=lambda x: float(x.replace('-', '.')) if x.replace('-', '').replace('.','').isdigit() else 99):
            size_val = float(size_str.replace('-', '.'))
            sku = sizes_data[size_str].get('sku', '')
            if not sku or sku in seen_skus:
                continue
            seen_skus.add(sku)
            stock = random.randint(3, 50)

            VARIANT_ROWS.append((variant_id, product_id, size_val, color_name, sku, stock, variant_price, discount_id))
            variant_id += 1

    # Insert product + image
    # Only add if we actually have variants
    if any(v[1] == product_id for v in VARIANT_ROWS):
        PRODUCT_ROWS.append((product_id, display_name, 1, cat_id, desc, base_price, sales))
        IMAGE_ROWS.append((img_id, thumbnail, top_view or None, bottom_view or None, side_view or None, pair_view or None))
        ALL_PRODUCT_IDS.append(product_id)
        product_id += 1
        img_id += 1

print(f"Generated {len(PRODUCT_ROWS)} Allbirds products, {len(VARIANT_ROWS)} variants")

# ── Write seed.sql ─────────────────────────────────────────────
output = []
output.append("-- ============================================================")
output.append("-- Running Shoes E-Commerce — Seed Data (Auto-generated from JSON)")
output.append("-- ============================================================\n")
output.append("USE runningdb;\n")

# Brands + Categories
output.append("""INSERT INTO Brands (id, name) VALUES
(1, 'Allbirds'), (2, 'Nike'), (3, 'Adidas'), (4, 'New Balance'),
(5, 'ASICS'), (6, 'Brooks'), (7, 'Saucony'), (8, 'Hoka'),
(9, 'On Running'), (10, 'Under Armour');

INSERT INTO Categories (id, material) VALUES
(1, 'Wool'), (2, 'Tree'), (3, 'Canvas'), (4, 'Sugar'), (5, 'Cotton'),
(6, 'Corduroy'), (7, 'Alternative Leather'), (8, 'Luxe Collection'),
(9, 'Cozy Collection'), (10, 'Mesh'), (11, 'Knit'), (12, 'Gore-Tex');\n""")

# Original 38 products (existing seed, IDs 1-21 Allbirds, 22-38 other brands)
output.append("""-- Original Allbirds products (IDs 1-21 kept from initial setup)
INSERT INTO Products (id, name, brand_id, category_id, description, base_price, sales) VALUES
(1,  'Women''s Wool Runner - Natural White',  1, 1, 'The original wool sneaker that started it all.', 110.00, 342),
(2,  'Women''s Wool Runner - Natural Black',   1, 1, 'The original wool sneaker.', 110.00, 287),
(3,  'Women''s Wool Runner - Hazy Indigo',     1, 1, 'Limited-edition hazy indigo.', 110.00, 156),
(4,  'Women''s Wool Runner - Savanna Night',   1, 1, 'Limited-edition savanna night.', 110.00, 198),
(5,  'Women''s Wool Runner - Natural Grey',    1, 1, 'Classic grey on sale.', 49.00, 523),
(6,  'Women''s Canvas Cruiser - Sea Spray',    1, 3, 'Hemp-and-cotton upper cruiser.', 75.00, 134),
(7,  'Women''s Canvas Cruiser - Warm White',   1, 3, 'Lightweight canvas cruiser.', 75.00, 201),
(8,  'Women''s Canvas Cruiser - Port',         1, 3, 'Limited port red canvas.', 75.00, 89),
(9,  'Men''s Allbirds Slide - Natural Black',  1, 4, 'Easy on/off slides.', 27.00, 412),
(10, 'Men''s Allbirds Slide - Anthracite',     1, 4, 'Anthracite sugarcane slides.', 27.00, 178),
(11, 'Men''s Allbirds Slide - Mushroom',       1, 4, 'Mushroom sugarcane slides.', 27.00, 145),
(12, 'Women''s Flip Flop - Natural Black',     1, 4, 'Light flip flops.', 25.00, 367),
(13, 'Women''s Flip Flop - Mushroom',          1, 4, 'Mushroom flip flops.', 25.00, 98),
(14, 'Anytime Crew Sock 3-Pack',               1, 5, 'Organic cotton socks.', 18.00, 891),
(15, 'Men''s Tree Runner NZ - Natural White',  1, 2, 'Breathable eucalyptus fiber.', 100.00, 234),
(16, 'Men''s Tree Runner NZ - Medium Grey',    1, 2, 'Everyday tree runner.', 100.00, 167),
(17, 'Women''s Tree Runner NZ - Mushroom',     1, 2, 'Tree material runner.', 100.00, 156),
(18, 'Women''s Tree Runner NZ - Blizzard',     1, 2, 'Clean white tree runner.', 100.00, 289),
(19, 'Men''s Strider - Dark Navy',             1, 2, 'Performance everyday shoe.', 91.00, 78),
(20, 'Men''s Wool Cruiser - Dark Navy',        1, 1, 'Warm wool casual shoe.', 105.00, 123),
(21, 'Women''s Wool Cruiser - Natural Grey',   1, 1, 'Wool cruiser for daily wear.', 105.00, 201);

-- Non-Allbirds brand products
INSERT INTO Products (id, name, brand_id, category_id, description, base_price, sales) VALUES
(22, 'Nike Air Zoom Pegasus 40',            2, 11, 'Responsive cushioning, sleek mesh upper.', 129.99, 654),
(23, 'Nike Revolution 6',                   2, 11, 'Soft cushioning for road running.', 74.99, 445),
(24, 'Nike Free Run 5.0',                   2, 11, 'Natural motion with snug fit.', 109.99, 312),
(25, 'Adidas Ultraboost Light',             3, 11, 'Ultimate energy return with PRIMEKNIT+.', 189.99, 523),
(26, 'Adidas Adizero Boston 12',            3, 11, 'Lightweight speed trainer.', 159.99, 234),
(27, 'Adidas Cloudfoam Pure',               3, 11, 'Plush Cloudfoam comfort.', 89.99, 389),
(28, 'New Balance Fresh Foam 1080v13',      4, 11, 'Premium Fresh Foam X.', 159.99, 312),
(29, 'New Balance 990v6',                   4, 11, 'Iconic heritage runner.', 184.99, 267),
(30, 'ASICS Gel-Nimbus 26',                 5, 11, 'Plush gel for long runs.', 169.99, 445),
(31, 'ASICS Gel-Kayano 30',                 5, 11, 'Stability with PureGEL.', 169.99, 378),
(32, 'Brooks Ghost 16',                     6, 11, 'DNA LOFT v2 cushioning.', 139.99, 567),
(33, 'Brooks Glycerin 21',                  6, 11, 'Nitrogen-infused DNA LOFT v3.', 169.99, 234),
(34, 'Saucony Triumph 22',                  7, 11, 'PWRRUN+ plush comfort.', 169.99, 145),
(35, 'Hoka Clifton 9',                      8, 11, 'Meta-Rocker lightweight.', 144.99, 567),
(36, 'Hoka Bondi 8',                        8, 11, 'Maximum cushioning.', 169.99, 345),
(37, 'On Cloudstratus 3',                   9, 11, 'Dual-layer CloudTec.', 179.99, 234),
(38, 'On Cloudswift 3',                     9, 11, 'Helion superfoam urban shoe.', 159.99, 178);\n""")

# Allbirds products from JSON (ID 39+)
output.append("-- Auto-generated Allbirds products from JSON\n")
if PRODUCT_ROWS:
    chunk_size = 10
    for i in range(0, len(PRODUCT_ROWS), chunk_size):
        chunk = PRODUCT_ROWS[i:i+chunk_size]
        rows = []
        for pid, pname, bid, cid, desc, price, sales in chunk:
            rows.append(f"({pid}, '{pname}', {bid}, {cid}, '{desc}', {price:.2f}, {sales})")
        output.append("INSERT INTO Products (id, name, brand_id, category_id, description, base_price, sales) VALUES")
        output.append(",\n".join(rows) + ";\n")

# Product Images
output.append("-- ============================================================")
output.append("-- PRODUCT IMAGES")
output.append("-- ============================================================\n")

# Image IDs 1-38 for original products
image_sqls = [
    "(1,  '//www.allbirds.com/cdn/shop/files/Allbirds_WL_RN_SF_PDP_Natural_White_LAT.png',   '//www.allbirds.com/cdn/shop/files/Allbirds_WL_RN_SF_PDP_Natural_White_TOP.png',   '//www.allbirds.com/cdn/shop/files/Allbirds_WL_RN_SF_PDP_Natural_White_HEL.png',   '//www.allbirds.com/cdn/shop/files/Allbirds_WL_RN_SF_PDP_Natural_White_OUT.png')",
    "(2,  '//www.allbirds.com/cdn/shop/files/Allbirds_WL_RN_SF_PDP_Natural_Black_LAT.png',  '//www.allbirds.com/cdn/shop/files/Allbirds_WL_RN_SF_PDP_Natural_Black_TOP.png',  '//www.allbirds.com/cdn/shop/files/Allbirds_WL_RN_SF_PDP_Natural_Black_HEL.png',  '//www.allbirds.com/cdn/shop/files/Allbirds_WL_RN_SF_PDP_Natural_Black_OUT.png')",
    "(3,  '//www.allbirds.com/cdn/shop/files/AB0098M_SHOE_LEFT_GLOBAL_MENS_WOOL_RUNNER_HAZY_INDIGO_BLIZZARD_d22b2af4-a20e-4004-ae0a-aafea3ffea98.png', '//www.allbirds.com/cdn/shop/files/AB0098M_SHOE_TOP_GLOBAL_MENS_WOOL_RUNNER_HAZY_INDIGO_BLIZZARD_7bcd9643-b7e4-4f3b-bcf5-78ceb715dcb7.png', '//www.allbirds.com/cdn/shop/files/AB0098M_SHOE_BACK_GLOBAL_MENS_WOOL_RUNNER_HAZY_INDIGO_BLIZZARD_0f3546d1-0493-4633-90a5-f5b0d1f0e0c7.png', '//www.allbirds.com/cdn/shop/files/AB0098M_SHOE_BOTTOM_GLOBAL_MENS_WOOL_RUNNER_HAZY_INDIGO_BLIZZARD_3717a7dc-9e6a-4284-8ddb-1786706915c2.png')",
    "(4,  '//www.allbirds.com/cdn/shop/files/Allbirds_FY19_August_PDP_WL_RN_Savanna_Night_LAT.png',   '//www.allbirds.com/cdn/shop/files/Allbirds_FY19_August_PDP_WL_RN_Savanna_Night_TOP.png',   '//www.allbirds.com/cdn/shop/files/Allbirds_FY19_August_PDP_WL_RN_Savanna_Night_HEL.png',   '//www.allbirds.com/cdn/shop/files/Allbirds_FY19_August_PDP_WL_RN_Savanna_Night_OUT.png')",
    "(5,  '//www.allbirds.com/cdn/shop/files/WR3WNCW_SHOE_LEFT_GLOBAL_WOMENS_WOOL_RUNNER_GREY_LIGHT_GREY_bc7cc3a6-43a0-4dde-8f9e-48a8d80499df.png', '//www.allbirds.com/cdn/shop/files/WR3WNCW_SHOE_TOP_GLOBAL_WOMENS_WOOL_RUNNER_GREY_LIGHT_GREY_76623920-15eb-4ed9-bf55-67d5b11066d4.png', '//www.allbirds.com/cdn/shop/files/WR3WNCW_SHOE_BACK_GLOBAL_WOMENS_WOOL_RUNNER_GREY_LIGHT_GREY_0e004c82-37f9-43cb-aea0-2adb950bf655.png', '//www.allbirds.com/cdn/shop/files/WR3WNCW_SHOE_BOTTON_GLOBAL_WOMENS_WOOL_RUNNER_GREY_LIGHT_GREY_54911b2c-4b81-4811-a0a0-e67f644c12ce.png')",
    "(6,  '//www.allbirds.com/cdn/shop/files/A12852_26Q1_Cruiser-Canvas-Sea-Spray-Natural-White-Sole_PDP_LEFT.png',   '//www.allbirds.com/cdn/shop/files/A12852_26Q1_Cruiser-Canvas-Sea-Spray-Natural-White-Sole_PDP_TD.png',   '//www.allbirds.com/cdn/shop/files/A12852_26Q1_Cruiser-Canvas-Sea-Spray-Natural-White-Sole_PDP_BACK.png',   '//www.allbirds.com/cdn/shop/files/A12852_26Q1_Cruiser-Canvas-Sea-Spray-Natural-White-Sole_PDP_PAIR_3Q.png')",
    "(7,  '//www.allbirds.com/cdn/shop/files/A12344_26Q1_Cruiser-Canvas-Warm-White-Natural-White_PDP_LEFT.png',     '//www.allbirds.com/cdn/shop/files/A12344_26Q1_Cruiser-Canvas-Warm-White-Natural-White_PDP_TD.png',     '//www.allbirds.com/cdn/shop/files/A12344_26Q1_Cruiser-Canvas-Warm-White-Natural-White_PDP_BACK.png',     '//www.allbirds.com/cdn/shop/files/A12344_26Q1_Cruiser-Canvas-Warm-White-Natural-White_PDP_PAIR_3Q.png')",
    "(8,  '//www.allbirds.com/cdn/shop/files/A12848_26Q1_Cruiser-Canvas-Port-Natural-White-Sole_PDP_LEFT.png',     '//www.allbirds.com/cdn/shop/files/A12848_26Q1_Cruiser-Canvas-Port-Natural-White-Sole_PDP_TD.png',     '//www.allbirds.com/cdn/shop/files/A12848_26Q1_Cruiser-Canvas-Port-Natural-White-Sole_PDP_BACK.png',     '//www.allbirds.com/cdn/shop/files/A12848_26Q1_Cruiser-Canvas-Port-Natural-White-Sole_PDP_PAIR_3Q.png')",
    "(9,  '//www.allbirds.com/cdn/shop/files/A12650_26Q1_Allbirds-Slide-Natural-Black_PDP_LEFT.png',              '//www.allbirds.com/cdn/shop/files/A12650_26Q1_Allbirds-Slide-Natural-Black_PDP_TD.png',              '//www.allbirds.com/cdn/shop/files/A12650_26Q1_Allbirds-Slide-Natural-Black_PDP_BACK.png',              '//www.allbirds.com/cdn/shop/files/A12650_26Q1_Allbirds-Slide-Natural-Black_PDP_PAIR_3Q.png')",
    "(10, '//www.allbirds.com/cdn/shop/files/A12411_26Q1_Allbirds-Slide-Anthracite_PDP_LEFT.png',                '//www.allbirds.com/cdn/shop/files/A12411_26Q1_Allbirds-Slide-Anthracite_PDP_TD.png',                '//www.allbirds.com/cdn/shop/files/A12411_26Q1_Allbirds-Slide-Anthracite_PDP_BACK.png',                '//www.allbirds.com/cdn/shop/files/A12411_26Q1_Allbirds-Slide-Anthracite_PDP_PAIR_3Q.png')",
    "(11, '//www.allbirds.com/cdn/shop/files/A12589_26Q1_Allbirds-Slide-Mushroom_PDP_LEFT.png',                  '//www.allbirds.com/cdn/shop/files/A12589_26Q1_Allbirds-Slide-Mushroom_PDP_TD.png',                  '//www.allbirds.com/cdn/shop/files/A12589_26Q1_Allbirds-Slide-Mushroom_PDP_BACK.png',                  '//www.allbirds.com/cdn/shop/files/A12589_26Q1_Allbirds-Slide-Mushroom_PDP_PAIR_3Q.png')",
    "(12, '//www.allbirds.com/cdn/shop/files/A12629_26Q1_Allbirds-Flip-Flop-Natural-Black_PDP_LEFT.png',          '//www.allbirds.com/cdn/shop/files/A12629_26Q1_Allbirds-Flip-Flop-Natural-Black_PDP_TD.png',          '//www.allbirds.com/cdn/shop/files/A12629_26Q1_Allbirds-Flip-Flop-Natural-Black_PDP_BACK.png',          '//www.allbirds.com/cdn/shop/files/A12629_26Q1_Allbirds-Flip-Flop-Natural-Black_PDP_PAIR_3Q.png')",
    "(13, '//www.allbirds.com/cdn/shop/files/A12596_26Q1_Allbirds-Flip-Flop-Mushroom_PDP_LEFT.png',               '//www.allbirds.com/cdn/shop/files/A12596_26Q1_Allbirds-Flip-Flop-Mushroom_PDP_TD.png',               '//www.allbirds.com/cdn/shop/files/A12596_26Q1_Allbirds-Flip-Flop-Mushroom_PDP_BACK.png',               '//www.allbirds.com/cdn/shop/files/A12596_26Q1_Allbirds-Flip-Flop-Mushroom_PDP_PAIR_3Q.png')",
    "(14, NULL, NULL, NULL, NULL)",
    "(15, '//www.allbirds.com/cdn/shop/files/A12344_26Q1_Cruiser-Canvas-Warm-White-Natural-White_PDP_LEFT.png',  '//www.allbirds.com/cdn/shop/files/A12344_26Q1_Cruiser-Canvas-Warm-White-Natural-White_PDP_TD.png',  '//www.allbirds.com/cdn/shop/files/A12344_26Q1_Cruiser-Canvas-Warm-White-Natural-White_PDP_BACK.png',  '//www.allbirds.com/cdn/shop/files/A12344_26Q1_Cruiser-Canvas-Warm-White-Natural-White_PDP_PAIR_3Q.png')",
    "(16, '//www.allbirds.com/cdn/shop/files/AB0098M_SHOE_LEFT_GLOBAL_MENS_WOOL_RUNNER_HAZY_INDIGO_BLIZZARD_d22b2af4-a20e-4004-ae0a-aafea3ffea98.png', '//www.allbirds.com/cdn/shop/files/AB0098M_SHOE_TOP_GLOBAL_MENS_WOOL_RUNNER_HAZY_INDIGO_BLIZZARD_7bcd9643-b7e4-4f3b-bcf5-78ceb715dcb7.png', '//www.allbirds.com/cdn/shop/files/AB0098M_SHOE_BACK_GLOBAL_MENS_WOOL_RUNNER_HAZY_INDIGO_BLIZZARD_0f3546d1-0493-4633-90a5-f5b0d1f0e0c7.png', '//www.allbirds.com/cdn/shop/files/AB0098M_SHOE_BOTTOM_GLOBAL_MENS_WOOL_RUNNER_HAZY_INDIGO_BLIZZARD_3717a7dc-9e6a-4284-8ddb-1786706915c2.png')",
    "(17, NULL, NULL, NULL, NULL)",
    "(18, NULL, NULL, NULL, NULL)",
    "(19, NULL, NULL, NULL, NULL)",
    "(20, NULL, NULL, NULL, NULL)",
    "(21, NULL, NULL, NULL, NULL)",
    "(22, 'https://static.nike.com/a/images/t_PDP_1728_v1/f_auto,q_auto:eco/pegasus40.jpg', NULL, NULL, NULL)",
    "(23, NULL, NULL, NULL, NULL)",
    "(24, NULL, NULL, NULL, NULL)",
    "(25, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ultraboost-light.jpg', NULL, NULL, NULL)",
    "(26, NULL, NULL, NULL, NULL)",
    "(27, NULL, NULL, NULL, NULL)",
    "(28, NULL, NULL, NULL, NULL)",
    "(29, NULL, NULL, NULL, NULL)",
    "(30, 'https://www.asics.com/images/products/GEL-NIMBUS-26.jpg', NULL, NULL, NULL)",
    "(31, NULL, NULL, NULL, NULL)",
    "(32, NULL, NULL, NULL, NULL)",
    "(33, NULL, NULL, NULL, NULL)",
    "(34, NULL, NULL, NULL, NULL)",
    "(35, 'https://www.hoka.com/images/clifton-9.jpg', NULL, NULL, NULL)",
    "(36, NULL, NULL, NULL, NULL)",
    "(37, NULL, NULL, NULL, NULL)",
    "(38, NULL, NULL, NULL, NULL)",
]
output.append("INSERT INTO Product_img (id, thumbnail, top_view, bottom_view, side_view, pair_view) VALUES")
output.append(",\n".join(image_sqls) + ";\n")

# Auto images
if IMAGE_ROWS:
    rows = []
    for iid, tn, tv, bv, sv, pv in IMAGE_ROWS:
        tn_v = f"'{tn}'" if tn else "NULL"
        tv_v = f"'{tv}'" if tv else "NULL"
        sv_v = f"'{sv}'" if sv else "NULL"
        bv_v = f"'{bv}'" if bv else "NULL"
        pv_v = f"'{pv}'" if pv else "NULL"
        rows.append(f"({iid}, {tn_v}, {tv_v}, {bv_v}, {sv_v}, {pv_v})")
    output.append("INSERT INTO Product_img (id, thumbnail, top_view, bottom_view, side_view, pair_view) VALUES")
    output.append(",\n".join(rows) + ";\n")

# Discounts
output.append("""INSERT INTO Discounts (id, code, discount_type, value, start_date, end_date, n_uses, is_active) VALUES
(1, 'WELCOME10', '%', 10.00, '2026-01-01', '2027-01-01', 1000, 1),
(2, 'SUMMER25',  '%', 25.00, '2026-06-01', '2026-09-01', 500,  1),
(3, 'FLAT15',    'fixed', 15.00, '2026-01-01', '2026-12-31', 200, 1),
(4, 'FREESHIP',  '%', 0.00,  '2026-03-01', '2026-08-01', 300,  1),
(5, 'VIP50',     '%', 50.00, '2026-01-01', '2026-06-30', 50,   1),
(6, 'SALE20',    '%', 20.00, '2026-07-01', '2026-08-31', 100,  1);\n""")

# Product Variants
output.append("-- ============================================================")
output.append("-- PRODUCT VARIANTS")
output.append("-- ============================================================\n")

# Existing variants (IDs 1-58)
output.append("""INSERT INTO Product_variants (id, product_id, size, color, sku, stock_quantity, product_img_id, variant_price, reorder_level, discount_id) VALUES
(1,  1,  5,  'Natural White',  'WR3WAWW050', 12, 1, 110.00, 5, NULL),
(2,  1,  6,  'Natural White',  'WR3WAWW060', 18, 1, 110.00, 5, NULL),
(3,  1,  7,  'Natural White',  'WR3WAWW070', 25, 1, 110.00, 5, NULL),
(4,  1,  8,  'Natural White',  'WR3WAWW080', 30, 1, 110.00, 5, NULL),
(5,  1,  9,  'Natural White',  'WR3WAWW090', 22, 1, 110.00, 5, NULL),
(6,  1,  10, 'Natural White',  'WR3WAWW100', 15, 1, 110.00, 5, NULL),
(7,  1,  11, 'Natural White',  'WR3WAWW110', 8,  1, 110.00, 5, NULL),
(8,  2,  5,  'Natural Black',  'WR3WABC050', 10, 2, 110.00, 5, NULL),
(9,  2,  6,  'Natural Black',  'WR3WABC060', 20, 2, 110.00, 5, NULL),
(10, 2,  7,  'Natural Black',  'WR3WABC070', 28, 2, 110.00, 5, NULL),
(11, 2,  8,  'Natural Black',  'WR3WABC080', 35, 2, 110.00, 5, NULL),
(12, 2,  9,  'Natural Black',  'WR3WABC090', 18, 2, 110.00, 5, NULL),
(13, 2,  10, 'Natural Black',  'WR3WABC100', 12, 2, 110.00, 5, NULL),
(14, 2,  11, 'Natural Black',  'WR3WABC110', 6,  2, 110.00, 5, NULL),
(15, 5,  5,  'Natural Grey',   'WR3WNCW050', 5,  5, 49.00, 5, NULL),
(16, 5,  6,  'Natural Grey',   'WR3WNCW060', 8,  5, 49.00, 5, NULL),
(17, 5,  7,  'Natural Grey',   'WR3WNCW070', 12, 5, 49.00, 5, NULL),
(18, 5,  8,  'Natural Grey',   'WR3WNCW080', 15, 5, 49.00, 5, NULL),
(19, 5,  9,  'Natural Grey',   'WR3WNCW090', 10, 5, 49.00, 5, NULL),
(20, 5,  10, 'Natural Grey',   'WR3WNCW100', 7,  5, 49.00, 5, NULL),
(21, 5,  11, 'Natural Grey',   'WR3WNCW110', 3,  5, 49.00, 5, 6),
(22, 9,  8,  'Natural Black',  'A12635M080', 30, 9, 27.00, 5, NULL),
(23, 9,  9,  'Natural Black',  'A12635M090', 40, 9, 27.00, 5, NULL),
(24, 9,  10, 'Natural Black',  'A12635M100', 50, 9, 27.00, 5, NULL),
(25, 9,  11, 'Natural Black',  'A12635M110', 35, 9, 27.00, 5, NULL),
(26, 9,  12, 'Natural Black',  'A12635M120', 20, 9, 27.00, 5, NULL),
(27, 9,  13, 'Natural Black',  'A12635M130', 10, 9, 27.00, 5, NULL),
(28, 9,  14, 'Natural Black',  'A12635M140', 5,  9, 27.00, 5, NULL),
(29, 12, 5,  'Natural Black',  'A12629W050', 25, 12, 25.00, 5, NULL),
(30, 12, 6,  'Natural Black',  'A12629W060', 30, 12, 25.00, 5, NULL),
(31, 12, 7,  'Natural Black',  'A12629W070', 40, 12, 25.00, 5, NULL),
(32, 12, 8,  'Natural Black',  'A12629W080', 45, 12, 25.00, 5, NULL),
(33, 12, 9,  'Natural Black',  'A12629W090', 35, 12, 25.00, 5, NULL),
(34, 12, 10, 'Natural Black',  'A12629W100', 20, 12, 25.00, 5, NULL),
(35, 12, 11, 'Natural Black',  'A12629W110', 10, 12, 25.00, 5, NULL),
(36, 14, 0,  'Assorted',       'SOCKS_3PK_01', 200, NULL, 18.00, 20, NULL),
(37, 22, 7,  'Black/White',    'NK-PEG40-070', 30, 22, 129.99, 5, NULL),
(38, 22, 8,  'Black/White',    'NK-PEG40-080', 40, 22, 129.99, 5, NULL),
(39, 22, 9,  'Black/White',    'NK-PEG40-090', 45, 22, 129.99, 5, NULL),
(40, 22, 10, 'Black/White',    'NK-PEG40-100', 35, 22, 129.99, 5, NULL),
(41, 22, 11, 'Black/White',    'NK-PEG40-110', 20, 22, 129.99, 5, NULL),
(42, 22, 12, 'Black/White',    'NK-PEG40-120', 10, 22, 129.99, 5, NULL),
(43, 25, 7,  'Core Black',     'AD-UBL-070',   25, 25, 189.99, 5, NULL),
(44, 25, 8,  'Core Black',     'AD-UBL-080',   35, 25, 189.99, 5, NULL),
(45, 25, 9,  'Core Black',     'AD-UBL-090',   40, 25, 189.99, 5, NULL),
(46, 25, 10, 'Core Black',     'AD-UBL-100',   30, 25, 189.99, 5, NULL),
(47, 25, 11, 'Core Black',     'AD-UBL-110',   18, 25, 189.99, 5, NULL),
(48, 30, 7,  'Black/Sheet Rock','AS-NIM26-070', 20, 30, 169.99, 5, NULL),
(49, 30, 8,  'Black/Sheet Rock','AS-NIM26-080', 30, 30, 169.99, 5, NULL),
(50, 30, 9,  'Black/Sheet Rock','AS-NIM26-090', 35, 30, 169.99, 5, NULL),
(51, 30, 10, 'Black/Sheet Rock','AS-NIM26-100', 25, 30, 169.99, 5, NULL),
(52, 30, 11, 'Black/Sheet Rock','AS-NIM26-110', 15, 30, 169.99, 5, NULL),
(53, 30, 12, 'Black/Sheet Rock','AS-NIM26-120', 8,  30, 169.99, 5, NULL),
(54, 35, 7,  'White/Blue',     'HK-CL9-070',   28, 35, 144.99, 5, NULL),
(55, 35, 8,  'White/Blue',     'HK-CL9-080',   38, 35, 144.99, 5, NULL),
(56, 35, 9,  'White/Blue',     'HK-CL9-090',   42, 35, 144.99, 5, NULL),
(57, 35, 10, 'White/Blue',     'HK-CL9-100',   32, 35, 144.99, 5, NULL),
(58, 35, 11, 'White/Blue',     'HK-CL9-110',   18, 35, 144.99, 5, NULL);\n""")

# Auto-generated variants
if VARIANT_ROWS:
    rows = []
    for vid, pid, sz, col, sku, stock, price, disc in VARIANT_ROWS:
        disc_v = str(disc) if disc else "NULL"
        rows.append(f"({vid}, {pid}, {sz}, '{col}', '{sku}', {stock}, NULL, {price:.2f}, 5, {disc_v})")
    output.append("INSERT INTO Product_variants (id, product_id, size, color, sku, stock_quantity, product_img_id, variant_price, reorder_level, discount_id) VALUES")
    output.append(",\n".join(rows) + ";\n")

# Collections
output.append("""INSERT INTO Collections (id, name, description, is_active, is_limited, release_date) VALUES
(1, 'June''s Collection', 'Newest summer arrivals.', 1, 0, '2026-06-01'),
(2, 'Bold By Nature', 'Pantone-curated exclusive shades.', 1, 1, '2026-05-15'),
(3, 'Trail Blazers', 'Off-road ready shoes.', 1, 0, '2026-04-20'),
(4, 'Marathon Elite', 'Race-ready performance shoes.', 1, 1, '2026-03-01'),
(5, 'Eco Essentials', 'Sustainable natural material footwear.', 1, 0, '2026-02-10'),
(6, 'Summer Sale', 'Up to 60% off.', 1, 1, '2026-07-01'),
(7, 'Classic Collection', 'Timeless everyday styles.', 1, 0, '2026-01-15');\n""")

# Product-collection links
output.append("INSERT INTO Product_collections (product_id, collection_id) VALUES\n")
pc_rows = []
for pid in [1, 2, 7]: pc_rows.append(f"({pid}, 1)")
for pid in [3, 4, 8]: pc_rows.append(f"({pid}, 2)")
for pid in [35, 36, 37, 38]: pc_rows.append(f"({pid}, 3)")
for pid in [22, 25, 28, 30]: pc_rows.append(f"({pid}, 4)")
for pid in [15, 16, 17, 18]: pc_rows.append(f"({pid}, 5)")
for pid in [5, 9, 12, 14]: pc_rows.append(f"({pid}, 6)")
for pid in ALL_PRODUCT_IDS: pc_rows.append(f"({pid}, 7)")
# Also include original 1-21
for pid in range(1, 22): pc_rows.append(f"({pid}, 7)")
output.append(",\n".join(pc_rows) + ";\n")

# Shipping, Cities, Users, Orders, etc.
output.append("""
INSERT INTO Shipping_rules (id, name, price, delivery_commission, free_shipping_threshold) VALUES
(1, 'Standard', 25.00, 5.00, 500.00),
(2, 'Express', 50.00, 10.00, 800.00),
(3, 'Free', 0.00, 0.00, 0.00);

INSERT INTO Cities (id, name, shipping_rule_id) VALUES
(1, 'Casablanca', 1), (2, 'Rabat', 1), (3, 'Marrakech', 1),
(4, 'Fes', 1), (5, 'Tangier', 1), (6, 'Agadir', 1),
(7, 'Meknes', 1), (8, 'Oujda', 1), (9, 'Kenitra', 1),
(10, 'Tetouan', 2), (11, 'Safi', 2), (12, 'El Jadida', 2),
(13, 'Beni Mellal', 2), (14, 'Laayoune', 2), (15, 'Dakhla', 2);

INSERT INTO Users (id, first_name, last_name, email, password, role, city_id) VALUES
(1, 'Admin', 'User', 'admin@runningshoes.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1),
(2, 'Hassan', 'El Amrani', 'hassan@delivery.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'delivery_guy', 1),
(3, 'Karim', 'Benali', 'karim@delivery.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'delivery_guy', 2),
(4, 'Fatima', 'Zahra', 'fatima@delivery.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'delivery_guy', 3),
(5, 'Yassine', 'El Khattabi', 'yassine@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 1),
(6, 'Noura', 'Idrissi', 'noura@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 2),
(7, 'Omar', 'Tazi', 'omar@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 3),
(8, 'Leila', 'Bennis', 'leila@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 4),
(9, 'Mehdi', 'Fassi', 'mehdi@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 5),
(10, 'Sara', 'Alaoui', 'sara@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 6),
(11, 'Anas', 'Rami', 'anas@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 7),
(12, 'Imane', 'Kabbaj', 'imane@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 8);

INSERT INTO Orders (id, client_id, city_id, subtotal, discount_id, shipping_status, delivery_guy_id, created_at, delivered_at) VALUES
(1, 5, 1, 110.00, NULL, 'delivered', 2, '2026-05-10 14:30:00', '2026-05-13 10:00:00'),
(2, 5, 1, 75.00, 1, 'delivered', 2, '2026-05-20 09:15:00', '2026-05-22 11:30:00'),
(3, 6, 2, 189.99, NULL, 'delivered', 3, '2026-06-01 16:45:00', '2026-06-04 14:00:00'),
(4, 7, 3, 257.99, NULL, 'shipped', 4, '2026-06-10 11:00:00', NULL),
(5, 8, 4, 54.00, 2, 'shipped', NULL, '2026-06-12 08:30:00', NULL),
(6, 9, 5, 110.00, NULL, 'pending', NULL, '2026-06-14 15:20:00', NULL),
(7, 10, 6, 169.99, 3, 'delivered', 2, '2026-06-05 10:00:00', '2026-06-08 12:00:00'),
(8, 11, 7, 49.00, NULL, 'cancelled', NULL, '2026-06-02 13:00:00', NULL),
(9, 12, 8, 129.99, NULL, 'delivered', 2, '2026-05-28 17:00:00', '2026-05-30 09:00:00'),
(10, 5, 1, 144.99, 1, 'shipped', 2, '2026-06-15 12:00:00', NULL);

INSERT INTO Order_items (order_id, variant_id, quantity, price_at_purchase) VALUES
(1, 3, 1, 110.00), (2, 31, 1, 75.00), (3, 44, 1, 189.99),
(4, 37, 1, 129.99), (4, 54, 1, 128.00), (5, 28, 2, 27.00),
(6, 3, 1, 110.00), (7, 49, 1, 169.99), (8, 18, 1, 49.00),
(9, 38, 1, 129.99), (10, 55, 1, 144.99);

INSERT INTO Reviews (user_id, product_id, rating, comment, verified_purchase, created_at) VALUES
(5, 1, 5, 'Extremely comfortable!', 1, '2026-05-14'),
(5, 6, 4, 'Great casual shoe.', 1, '2026-05-24'),
(6, 25, 5, 'Best running shoes ever.', 1, '2026-06-05'),
(7, 22, 4, 'Great all-around runner.', 0, '2026-06-11'),
(8, 9, 5, 'Perfect slides.', 1, '2026-06-13'),
(10, 30, 5, 'Excellent support.', 1, '2026-06-07'),
(12, 22, 4, 'Solid shoe.', 1, '2026-05-30'),
(9, 1, 3, 'Runs slightly small.', 0, '2026-06-15'),
(5, 35, 5, 'Like running on clouds!', 1, '2026-06-17'),
(11, 5, 4, 'Great deal.', 0, '2026-06-03');

INSERT INTO Refunds (id, order_id, amount, reason, status, created_at) VALUES
(1, 8, 49.00, 'Changed mind - wrong size.', 'approved', '2026-06-03');
INSERT INTO Refund_items (refund_id, variant_id, quantity, price_at_purchase) VALUES (1, 18, 1, 49.00);

INSERT INTO Audit_logs (admin_id, action_performed, target_table, target_id, created_at) VALUES
(1, 'CREATE', 'Products', '1', '2026-01-15'),
(1, 'UPDATE_PRICE', 'Product_variants', '15', '2026-06-01'),
(1, 'APPROVE_REFUND', 'Refunds', '1', '2026-06-04'),
(1, 'CANCEL_ORDER', 'Orders', '8', '2026-06-03'),
(1, 'RESTOCK', 'Product_variants', '3', '2026-06-10');

INSERT INTO Inventory_logs (product_variant_id, admin_id, quantity_added, unit_price, restocked_at) VALUES
(3, 1, 50, 80.00, '2026-06-10'),
(10, 1, 30, 80.00, '2026-06-10'),
(24, 1, 100, 18.00, '2026-06-10'),
(32, 1, 60, 16.00, '2026-06-10'),
(44, 1, 40, 130.00, '2026-06-12'),
(55, 1, 50, 100.00, '2026-06-12');
""")

with open('seed.sql', 'w') as f:
    f.write('\n'.join(output))

print(f"\nDone! {len(PRODUCT_ROWS)} Allbirds products (IDs 39-{38+len(PRODUCT_ROWS)})")
print(f"Total products: {38 + len(PRODUCT_ROWS)}")
print(f"New images: {len(IMAGE_ROWS)}, New variants: {len(VARIANT_ROWS)}")
