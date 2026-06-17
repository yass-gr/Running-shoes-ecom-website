const sharp = require("sharp");
const https = require("https");
const fs = require("fs");
const path = require("path");

const DATA = path.join(__dirname, "allbirds_products.json");
const OUT = path.join(__dirname, "allbirds_products.json");
const products = JSON.parse(fs.readFileSync(DATA, "utf-8"));

async function getDominantColor(imgUrl) {
  const buf = await new Promise((resolve, reject) => {
    https.get(imgUrl, (res) => {
      const chunks = [];
      res.on("data", (c) => chunks.push(c));
      res.on("end", () => resolve(Buffer.concat(chunks)));
    }).on("error", reject);
  });

  const { data } = await sharp(buf)
    .resize(50, 50, { fit: "fill" })
    .raw()
    .toBuffer({ resolveWithObject: true });

  const buckets = {};
  for (let i = 0; i < data.length; i += 3) {
    const r = Math.min(255, Math.round(data[i] / 32) * 32);
    const g = Math.min(255, Math.round(data[i + 1] / 32) * 32);
    const b = Math.min(255, Math.round(data[i + 2] / 32) * 32);
    const key = `${r},${g},${b}`;
    if (r + g + b > 650 || r + g + b < 80) continue;
    buckets[key] = (buckets[key] || 0) + 1;
  }

  const entries = Object.entries(buckets).sort((a, b) => b[1] - a[1]);
  if (!entries.length) return "#c8c8c8";
  const [r, g, b] = entries[0][0].split(",").map(n => +n);
  return `#${[r, g, b].map(n => n.toString(16).padStart(2, "0")).join("")}`;
}

(async () => {
  const targets = products.filter(p => p.tags?.includes("collection:apr26"));
  console.log(`Processing ${targets.length} products...`);

  for (let i = 0; i < targets.length; i++) {
    const p = targets[i];
    const imgUrl = `https:${p.images?.[0]?.preview_image?.src}`;
    if (!imgUrl || imgUrl === "https:undefined") {
      p.color = "#c8c8c8";
    } else {
      try {
        p.color = await getDominantColor(imgUrl);
      } catch {
        p.color = "#c8c8c8";
      }
    }
    process.stdout.write(`\r${i + 1}/${targets.length} - ${p.color}`);
  }

  fs.writeFileSync(OUT, JSON.stringify(products, null, 2));
  console.log(`\nDone. Written to ${OUT}`);
})();
