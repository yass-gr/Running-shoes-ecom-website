const carouselContainer = $(".newArrivals2  .content");

import { data } from "../../../../testdata(temporary)/allbirds_products.js";

const newArrivals = data.filter(
  (i) => i.tags.includes("collection:apr26") && i.type === "shoes",
);

$(".newArrivals2").css("overflow", "hidden");
const content = $(".newArrivals2 .content");
content.css("transition", "transform 0.3s ease-in-out");

let currIdx = 0;
let cardPositions = [];

$(".newArrivals2 .arrows .left").on("click", () => {
  if (currIdx === 0 || !cardPositions.length) return;
  snapTo(--currIdx);
});

$(".newArrivals2 .arrows .right").on("click", () => {
  if (!cardPositions.length || currIdx >= cardPositions.length - 1) return;
  snapTo(++currIdx);
});

function snapTo(idx) {
  content.css("transform", `translateX(${-cardPositions[idx]}px)`);
}

for (let i = 20; i < newArrivals.length; i++) {
  const card = $(`
    <div class="card">
      <img src="${newArrivals[i].images[0].src}" alt="">
      <div class="info">
        <p class="name">${decodeURIComponent(newArrivals[i].masterName)}</p>
        <p class="cName">${decodeURIComponent(newArrivals[i].colorName).split("/")[0].split(")")[0]}</p>
        <p class="price" > $${newArrivals[i].price / 100}</p>
        <div class="hue" style="background-color:${newArrivals[i].colorcode}"></div>
      </div>
      <span class="badge">NEW</span>
    </div>
  `);

  carouselContainer.append(card);
  cardPositions = content
    .find(".card")
    .map((i, el) => el.getBoundingClientRect().left)
    .get();
}
