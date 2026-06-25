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

cardPositions = content
  .find(".card")
  .map((i, el) => el.getBoundingClientRect().left)
  .get();

$(".newArrivals2").on("click", ".hue", function () {
  const thumb = $(this).data("thumb");
  $(this).closest(".card").find("img").attr("src", thumb);
});
