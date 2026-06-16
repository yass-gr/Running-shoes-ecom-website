import json, re, urllib.request, sys, time
from pathlib import Path

URL = "https://www.allbirds.com/collections/shop-all-26"
OUTPUT = Path(__file__).parent / "allbirds_products.json"

def extract_products(html):
    idx = html.find('"products":')
    if idx < 0:
        raise ValueError("products array not found")
    start = html.index('[', idx)
    depth, in_str, esc = 0, False, False
    for i in range(start, len(html)):
        ch = html[i]
        if esc: esc = False; continue
        if ch == '\\' and in_str: esc = True; continue
        if ch == '"': in_str = not in_str; continue
        if in_str: continue
        if ch == '[': depth += 1
        elif ch == ']':
            depth -= 1
            if depth == 0:
                return json.loads(html[start:i+1])
    raise ValueError("unterminated products array")

def main():
    t0 = time.time()
    req = urllib.request.Request(URL, headers={
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
    })
    with urllib.request.urlopen(req, timeout=30) as r:
        html = r.read().decode()
    fetch_time = time.time() - t0
    products = extract_products(html)
    parse_time = time.time() - t0 - fetch_time

    print(f"Fetched {len(html):,} bytes in {fetch_time:.2f}s")
    print(f"Parsed {len(products)} products in {parse_time:.2f}s")
    print(f"Total: {time.time()-t0:.2f}s")

    OUTPUT.write_text(json.dumps(products, indent=2, ensure_ascii=False))
    print(f"Saved to {OUTPUT} ({OUTPUT.stat().st_size:,} bytes)")

if __name__ == "__main__":
    main()
