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

$(".collection-grid").on("click", ".product-card__swatch", function (e) {
  e.preventDefault();
  const $card = $(this).closest(".product-card");
  const url = $(this).data("url");
  const image = $(this).data("image");
  const hover = $(this).data("hover");
  if (!url) return;

  $card.find(".product-card__media").attr("href", url);
  $card.find(".product-card__link").attr("href", url);

  const $img = $card.find(".product-card__image");
  $img.attr("src", image);
  $img.removeData("default-src");
  $card.find(".product-card__media").data("hover", hover);

  $card.find(".product-card__swatch").removeClass("product-card__swatch--active");
  $(this).addClass("product-card__swatch--active");
});
