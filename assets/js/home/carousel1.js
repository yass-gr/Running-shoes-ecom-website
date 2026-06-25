let currProd = 3;
let translate = -300;

const newArrivals = window.newArrivalsData || [];
const container = $(".newAriv1content");
const prodText = $(".pName");

if (newArrivals[currProd]) {
  prodText.html(`${newArrivals[currProd].name} - $${parseFloat(newArrivals[currProd].price).toFixed(2)}`);
}

$(".leftControl").on("click", () => {
  translate = translate === 0 ? 0 : translate + 100;
  container.attr("style", `transform : translateX(${translate}%)`);
  currProd = (translate * -1) / 100;
  if (newArrivals[currProd]) {
    prodText.html(`${newArrivals[currProd].name} - $${parseFloat(newArrivals[currProd].price).toFixed(2)}`);
  }
});

$(".rightControl").on("click", () => {
  translate = translate === -1900 ? -1900 : translate - 100;
  container.attr("style", `transform :translateX(${translate}%)`);
  currProd = (translate * -1) / 100;
  if (newArrivals[currProd]) {
    prodText.html(`${newArrivals[currProd].name} - $${parseFloat(newArrivals[currProd].price).toFixed(2)}`);
  }
});

const arrow = $(".arrow");

$(".leftControl").on("mousemove", (e) => {
  arrow.html("⇠");
  $("body").css("cursor", "none");
  arrow.fadeIn(300);
  arrow.css("left", e.clientX);
  arrow.css("top", e.clientY);
});

$(".leftControl").on("mouseout", () => {
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

$(".rightControl").on("mouseout", () => {
  arrow.hide();
  $("body").css("cursor", "default");
});
