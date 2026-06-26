<?php $categories = $categories ?? []; ?>
<?php if (!empty($categories)): ?>
<section class="collection-categories" aria-label="Shop more categories">
  <?php foreach ($categories as $category): ?>
    <article class="collection-category">
      <img src="<?= e($category["image"]) ?>" alt="<?= e($category["title"]) ?>" loading="lazy" />
      <div class="collection-category__content">
        <h2><?= e($category["title"]) ?></h2>
        <a href="?route=<?= e($category["route"] ?? "#") ?>"><?= e($category["cta"]) ?></a>
      </div>
    </article>
  <?php endforeach; ?>
</section>
<?php endif; ?>
