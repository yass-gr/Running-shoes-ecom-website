let currProd = 3;
let translate = -300;

const container = $(".newAriv1content");
const prodText = $(".pName");

function updateText() {
  const slide = container.children().eq(currProd);
  prodText.html(`${slide.data("name")} - $${slide.data("price")}`);
}

updateText();

$(".leftControl").on("click", () => {
  translate = translate === 0 ? 0 : translate + 100;
  container.attr("style", `transform : translateX(${translate}%)`);
  currProd = (translate * -1) / 100;
  updateText();
});

$(".rightControl").on("click", () => {
  translate = translate === -1900 ? -1900 : translate - 100;
  container.attr("style", `transform :translateX(${translate}%)`);
  currProd = (translate * -1) / 100;
  updateText();
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
