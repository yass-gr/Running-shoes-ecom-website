import { data } from "../../../testdata(temporary)/allbirds_products.js";

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
