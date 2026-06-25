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

function hideArrow() {
  arrow.stop(true).hide();
  $("body").css("cursor", "default");
}

function showArrow(e, symbol) {
  arrow.html(symbol);
  $("body").css("cursor", "none");
  arrow.css("left", e.clientX).css("top", e.clientY);
  arrow.stop(true).fadeIn(0);
}

$(".leftControl").on("mouseenter", (e) => {
  showArrow(e, "⇠");
});
$(".leftControl").on("mousemove", (e) => {
  arrow.css("left", e.clientX).css("top", e.clientY);
});
$(".leftControl").on("mouseleave", hideArrow);

$(".rightControl").on("mouseenter", (e) => {
  showArrow(e, "⇢");
});
$(".rightControl").on("mousemove", (e) => {
  arrow.css("left", e.clientX).css("top", e.clientY);
});
$(".rightControl").on("mouseleave", hideArrow);

$(window).on("blur", hideArrow);
