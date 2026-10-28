<?php
require __DIR__ . '/includes/bootstrap.php';

$categories = get_categories();
$heroFeed = get_blog_feed(['limit' => 6]);
$heroBlogs = $heroFeed['blogs'];
$hero = $heroBlogs[0] ?? null;

$pageTitle = site_tagline() ? site_name() . ' — ' . site_tagline() : site_name();
$pageDescription = 'Latest headlines and stories from ' . site_name() . ': ' . implode(', ', array_map(fn($c) => $c['name'], array_slice($categories, 0, 6))) . ' and more.';
$canonicalUrl = 'https://' . SITE_DOMAIN . '/';
$ogImage = $hero ? featured_image($hero) : '';

require __DIR__ . '/includes/header.php';
?>

<!-- banner start -->
<!--<section class="homepage-carousel" aria-roledescription="carousel" aria-label="Featured highlights" data-carousel>-->
<!--  <div class="homepage-carousel__viewport">-->
<!--    <div class="homepage-carousel__track">-->
<!--      <article class="homepage-carousel__slide is-active" aria-hidden="false">-->
<!--        <img src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=85" alt="Mountain lake surrounded by forest" width="1800" height="800">-->
<!--        <div class="homepage-carousel__content"><span>EXPLORE MORE</span>-->
<!--          <h2>Stories that take you places.</h2>-->
<!--          <p>Find fresh inspiration for your next great escape.</p><a class="homepage-carousel__button" href="/search/">Explore stories <span aria-hidden="true">→</span></a>-->
<!--        </div>-->
<!--      </article>-->
<!--      <article class="homepage-carousel__slide" aria-hidden="true">-->
<!--        <img src="https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1800&q=85" alt="Bright modern home interior" width="1800" height="800" loading="lazy">-->
<!--        <div class="homepage-carousel__content"><span>MAKE IT YOURS</span>-->
<!--          <h2>Ideas for everyday living.</h2>-->
<!--          <p>Simple discoveries, thoughtful spaces and better routines.</p><a class="homepage-carousel__button" href="/search/?q=lifestyle">Discover lifestyle <span aria-hidden="true">→</span></a>-->
<!--        </div>-->
<!--      </article>-->
<!--      <article class="homepage-carousel__slide" aria-hidden="true">-->
<!--        <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1800&q=85" alt="Notebook, pen and cup of coffee on a desk" width="1800" height="800" loading="lazy">-->
<!--        <div class="homepage-carousel__content"><span>THE WEEKLY EDIT</span>-->
<!--          <h2>Read something worth sharing.</h2>-->
<!--          <p>Practical ideas and new perspectives, selected for you.</p><a class="homepage-carousel__button" href="/search/">Read the latest <span aria-hidden="true">→</span></a>-->
<!--        </div>-->
<!--      </article>-->
<!--    </div>-->
<!--    <button class="homepage-carousel__arrow homepage-carousel__arrow--prev" type="button" aria-label="Previous banner" data-carousel-prev>‹</button>-->
<!--    <button class="homepage-carousel__arrow homepage-carousel__arrow--next" type="button" aria-label="Next banner" data-carousel-next>›</button>-->
<!--    <div class="homepage-carousel__dots" role="tablist" aria-label="Choose banner">-->
<!--      <button type="button" role="tab" aria-label="Show banner 1" aria-selected="true" data-carousel-dot></button>-->
<!--      <button type="button" role="tab" aria-label="Show banner 2" aria-selected="false" data-carousel-dot></button>-->
<!--      <button type="button" role="tab" aria-label="Show banner 3" aria-selected="false" data-carousel-dot></button>-->
<!--    </div>-->
<!--  </div>-->
<!--</section>-->
<!-- banner end -->

<?php if (!$heroFeed['ok']): ?>
  <section class="wrap empty-state">
    <h1>We couldn't load the latest stories</h1>
    <p>Please try again shortly.</p>
  </section>
<?php else: ?>

  <?php foreach ($categories as $cat):
      $section = get_blog_feed(['category' => $cat['slug'], 'limit' => 9]);
      if (!$section['blogs']) continue;
      $categoryProducts = [];
      foreach ($section['blogs'] as $categoryBlog) {
          foreach (($categoryBlog['relatedProducts'] ?? []) as $product) {
              if (!is_array($product) || empty($product['name'])) continue;
              $productKey = (string)($product['_id'] ?? $product['id'] ?? $product['name']);
              $categoryProducts[$productKey] = $product;
          }
      }
  ?>
  <section class="wrap home-section">
    <div class="home-section-head">
      <h2><?= e($cat['name']) ?></h2>
      <a class="view-all" href="<?= e_attr(category_url($cat['slug'])) ?>">View All &rarr;</a>
    </div>
    <div class="category-section-layout">
      <aside class="sidebar category-related" aria-label="Related <?= e_attr($cat['name']) ?> products">
        <h3>Recent Posts</h3>
        <?php foreach (array_slice($section['blogs'], 0, 3) as $recentPost) { render_blog_card($recentPost, 'sidebar'); } ?>
        <?php if ($categoryProducts): ?><h3 class="related-products-heading">Related Products</h3><?php endif; ?>
        <?php foreach ($categoryProducts as $product): ?>
          <article class="product-card related-product-row">
            <div class="related-product-thumb">
              <?php $productImage = safe_url($product['image'] ?? '', ''); if ($productImage): ?><img src="<?= e_attr($productImage) ?>" alt="<?= e_attr($product['name']) ?>" loading="lazy" width="90" height="86"><?php else: ?><span aria-hidden="true"><?= e(first_character($product['name'])) ?></span><?php endif; ?>
            </div>
            <div class="related-product-body">
              <span class="related-product-category"><?= e($cat['name']) ?></span>
              <strong><?= e($product['name']) ?></strong>
              <?php if (!empty($product['shortDescription'])): ?><p><?= e($product['shortDescription']) ?></p><?php endif; ?>
              <?php if (isset($product['price'])): ?><span class="product-price">&#8377;<?= e(number_format((float)$product['price'], 2)) ?></span><?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </aside>
      <div class="post-grid post-grid--3">
        <?php foreach (array_slice($section['blogs'], 0, 3) as $blog) { render_blog_card($blog); } ?>
      </div>
    </div>
  </section>
  <?php endforeach; ?>

<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
