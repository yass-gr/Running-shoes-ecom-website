let currProd = 3;
let translate = -300;

import { data } from "../../../../testdata(temporary)/allbirds_products.js";

const container = $(".newAriv1content");
const newArrivals = data.filter(
  (i) => i.tags.includes("collection:apr26") && i.type === "shoes",
);
console.log(newArrivals.length);
newArrivals.length = 20;

newArrivals.forEach((a) => {
  const cont = $("<div>");
  const img = $("<img>").attr("src", a.images[0].src);
  cont.append(img);
  container.append(cont);
});

const newArivalsContainer = $(".newAriv1content");
const prodText = $(".pName");
prodText.html(
  `${decodeURIComponent(newArrivals[currProd].fullName)} - $${newArrivals[currProd].price / 100}`,
);
$(".leftControl").on("click", () => {
  translate = translate === 0 ? 0 : translate + 100;
  newArivalsContainer.attr("style", `transform : translateX(${translate}%)`);
  currProd = (translate * -1) / 100;
  prodText.html(
    `${decodeURIComponent(newArrivals[currProd].fullName)} - $${newArrivals[currProd].price / 100}`,
  );
});
$(".rightControl").on("click", () => {
  translate = translate === -1900 ? -1900 : translate - 100;

  newArivalsContainer.attr("style", `transform :translateX(${translate}%)`);
  currProd = (translate * -1) / 100;
  prodText.html(
    `${decodeURIComponent(newArrivals[currProd].fullName)} - $${newArrivals[currProd].price / 100}`,
  );
});

const arrow = $(".arrow");

$(".leftControl").on("mousemove", (e) => {
  arrow.html("⇠");
  $("body").css("cursor", "none");

  arrow.fadeIn(300);
  arrow.css("left", e.clientX);
  arrow.css("top", e.clientY);
});
$(".leftControl").on("mouseout", (e) => {
  arrow.hide();
  $("body").css("cursor", "default");
});

$(".rightControl").on("mousemove", (e) => {
  arrow.html("⇢");
  $("body").css("cursor", "none");

  arrow.fadeIn(300);
  arrow.css("left", e.clientX);
  arrow.css("top", e.clientY);
});
$(".rightControl").on("mouseout", (e) => {
  arrow.hide();
  $("body").css("cursor", "default");
});
