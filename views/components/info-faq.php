<section class="info-faq" aria-label="Product information and FAQ">
  <div class="info-faq__inner">
    <h2 class="info-faq__title"><?= e($infoTitle ?? "MEN'S SHOES") ?></h2>
    <p class="info-faq__desc"><?= e($infoDesc ?? "Crafted from premium natural materials, our shoes deliver all-day comfort without compromising on style. Each pair is designed to feel as good as it looks, from the first step to the last.") ?></p>

    <div class="info-faq__list">
      <?php foreach ([
        ["q" => "Do Allbirds run true to size?", "a" => "Yes, most Allbirds run true to size. If you're between sizes, we recommend going with the larger size for a more comfortable fit. Our flyknit and wool uppers also stretch slightly over time to conform to your foot."],
        ["q" => "Are Allbirds shoes wide or narrow?", "a" => "Allbirds generally have a medium width fit. Our wool and tree materials are naturally flexible and will mold to your foot shape over time, providing a customized fit for most foot widths."],
        ["q" => "Are Allbirds meant to be worn without socks?", "a" => "Absolutely. Allbirds are designed to be worn with or without socks. Our moisture-wicking natural materials keep your feet comfortable and dry either way."],
        ["q" => "Are Allbirds washable?", "a" => "Yes. Most Allbirds styles are machine washable. Remove the insoles, wash on a gentle cycle with cold water and mild detergent, and air dry. Avoid the dryer to preserve the shape and materials."],
      ] as $faq): ?>
      <div class="info-faq__item">
        <button class="info-faq__question" type="button" aria-expanded="false">
          <span><?= $faq["q"] ?></span>
          <span class="info-faq__icon">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </span>
        </button>
        <div class="info-faq__answer" aria-hidden="true">
          <p><?= $faq["a"] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
