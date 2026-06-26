<div class="filter-overlay" aria-hidden="true"></div>

<div class="filter-panel" role="dialog" aria-label="Filter products" aria-hidden="true">
  <div class="filter-panel__header">
    <button class="filter-panel__close" type="button" aria-label="Close filter">
      <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <span class="filter-panel__title">Collapse Filters</span>
    <span class="filter-panel__count">(<?= $productCount ?> products)</span>
    <button class="filter-panel__clear" type="button">CLEAR ALL</button>
    <button class="filter-panel__apply" type="button">APPLY FILTERS</button>
  </div>

  <div class="filter-panel__body">
    <div class="filter-col filter-col--size">
      <h3 class="filter-col__heading">Size</h3>
      <p class="filter-col__desc">Select your size to narrow results.</p>
      <div class="filter-sizes">
        <?php foreach (["S","M","L","XL","5","5.5","6","6.5","7","7.5","8","8.5","9","9.5","10","10.5","11","11.5","12","12.5","13","13.5","14","15"] as $s): ?>
          <button class="filter-size" type="button" data-filter="size" data-value="<?= $s ?>"><?= $s ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="filter-col filter-col--color">
      <h3 class="filter-col__heading">Color</h3>
      <div class="filter-colors">
        <?php foreach (($availableColors ?? []) as $c): ?>
          <button class="filter-color" type="button" data-filter="color" data-value="<?= $c["label"] ?>">
            <span class="filter-color__swatch <?= ($c["white"] ?? false) ? 'filter-color__swatch--white' : '' ?>" style="background:<?= $c["hex"] ?>"></span>
            <span class="filter-color__label"><?= $c["label"] ?></span>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="filter-col filter-col--price">
      <h3 class="filter-col__heading">Price</h3>
      <div class="filter-checkboxes">
        <?php foreach ([
          ["label" => "Under $75",       "value" => "under75"],
          ["label" => "$76–$100",        "value" => "76-100"],
          ["label" => "$101–$125",       "value" => "101-125"],
          ["label" => "$126–$150",       "value" => "126-150"],
          ["label" => "Over $150",       "value" => "over150", "disabled" => true],
        ] as $p): ?>
          <button class="filter-checkbox <?= ($p["disabled"] ?? false) ? 'filter-checkbox--disabled' : '' ?>" type="button" data-filter="price" data-value="<?= $p["value"] ?>" <?= ($p["disabled"] ?? false) ? 'disabled' : '' ?>>
            <span class="filter-checkbox__box"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span class="filter-checkbox__label"><?= $p["label"] ?></span>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="filter-col filter-col--material">
      <h3 class="filter-col__heading">Material</h3>
      <div class="filter-checkboxes">
        <?php foreach (["Alternative-Leather","Canvas","Cotton","Cozy-Collection","Sugar","Tree"] as $mt): ?>
          <button class="filter-checkbox" type="button" data-filter="material" data-value="<?= $mt ?>">
            <span class="filter-checkbox__box"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></span>
            <span class="filter-checkbox__label"><?= $mt ?></span>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
