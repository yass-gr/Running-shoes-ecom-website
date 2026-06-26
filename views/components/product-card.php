<?php
$item = $item ?? [];
?>
<a href="?route=product&id=<?= $item["id"] ?>">
  <img src="<?= $item["image"] ?>" alt="<?= $item["name"] ?>">
  <div class="info">
    <p class="name"><?= $item["name"] ?></p>
    <p class="color"><?= $item["color"] ?></p>
    <p class="price">
      <?php if (isset($item["sale_price"])): ?>
        <span style="color:#d32f2f;">$<?= number_format($item["sale_price"]) ?></span>
        <span style="text-decoration:line-through;color:#999;">$<?= number_format($item["price"]) ?></span>
      <?php else: ?>
        $<?= number_format($item["price"]) ?>
      <?php endif; ?>
    </p>
    <div class="swatches">
      <?php foreach ($item["swatches"] as $s): ?>
        <div class="hue" style="background-color: <?= $s["hex"] ?>" data-thumb="<?= $s["thumb"] ?>"></div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php if ($item["badge"]): ?>
    <span class="card__badge card__badge--<?= str_replace(' ', '_', strtolower($item["badge"])) ?>"><?= $item["badge"] ?></span>
  <?php endif; ?>
</a>
