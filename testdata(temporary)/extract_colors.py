import json, urllib.request, os
from PIL import Image
from io import BytesIO
from collections import Counter

DATA = os.path.join(os.path.dirname(__file__), "allbirds_products.json")
OUT = os.path.join(os.path.dirname(__file__), "allbirds_products.json")

with open(DATA) as f:
    products = json.load(f)

targets = [p for p in products if "collection:apr26" in p.get("tags", [])]
print(f"Processing {len(targets)} products...")

for idx, p in enumerate(targets):
    src = p.get("images", [{}])[0].get("preview_image", {}).get("src")
    if not src:
        p["colorcode"] = "#c8c8c8"
        continue
    url = f"https:{src}"

    try:
        req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
        data = urllib.request.urlopen(req, timeout=15).read()
        img = Image.open(BytesIO(data)).convert("RGB")
        img = img.resize((50, 50), Image.BILINEAR)
        pixels = list(img.getdata())

        buckets = Counter()
        for r, g, b in pixels:
            r = round(r / 32) * 32
            g = round(g / 32) * 32
            b = round(b / 32) * 32
            if r + g + b > 650 or r + g + b < 80:
                continue
            key = f"{r},{g},{b}"
            buckets[key] += 1

        if not buckets:
            p["colorcode"] = "#c8c8c8"
        else:
            top = buckets.most_common(1)[0][0]
            parts = [int(x) for x in top.split(",")]
            p["colorcode"] = "#" + "".join(f"{min(255, v) & 255:02x}" for v in parts)
    except Exception as e:
        print(f"\nError on {idx}: {e}")
        p["colorcode"] = "#c8c8c8"

    if (idx + 1) % 10 == 0:
        print(f"  {idx + 1}/{len(targets)}")

with open(OUT, "w") as f:
    json.dump(products, f, indent=2)

# Also write JS module
JS = os.path.join(os.path.dirname(__file__), "allbirds_products.js")
with open(JS, "w") as f:
    f.write("export const data = ")
    json.dump(products, f, indent=2)
    f.write(";\n")

print(f"Done. {sum(1 for p in products if 'colorcode' in p)} products have colorcode")
