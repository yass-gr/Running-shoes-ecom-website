$(".collection-grid").on("mouseenter", ".product-card", function () {
  const $img = $(this).find(".product-card__image");
  const hover = $(this).find(".product-card__media").data("hover");
  if (hover) {
    $img.data("default-src", $img.attr("src")).attr("src", hover);
  }
}).on("mouseleave", ".product-card", function () {
  const $img = $(this).find(".product-card__image");
  const src = $img.data("default-src");
  if (src) {
    $img.attr("src", src);
  }
});
