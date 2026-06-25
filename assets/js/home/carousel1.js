let curr = 3;
let tx = -300;
const container = $(".newAriv1content");

$(".leftControl").on("click", () => {
  tx = tx === 0 ? 0 : tx + 100;
  container.css("transform", `translateX(${tx}%)`);
});

$(".rightControl").on("click", () => {
  tx = tx === -1900 ? -1900 : tx - 100;
  container.css("transform", `translateX(${tx}%)`);
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
