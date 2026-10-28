<?php
require __DIR__ . '/includes/bootstrap.php';
$categories = get_categories();
$feed = get_blog_feed(['limit' => 9]);
$posts = $feed['blogs'];
$hero = $posts[0] ?? null;
$pageTitle = site_name() . ' — Thoughts, insights & stories';
$pageDescription = site_tagline() ?: 'Fresh perspectives, thoughtful stories and useful ideas from Fork7.';
$canonicalUrl = site_url('/');
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

<?php if (!$feed['ok'] || !$posts): ?>
<section class="wrap empty-state"><h2><?= $feed['ok'] ? 'Your next story is on its way' : 'Stories could not be loaded' ?></h2><p><?= $feed['ok'] ? 'Come back soon for fresh articles.' : 'Please try again shortly.' ?></p></section>
<?php else: ?>
<section class="wrap f7-feature-layout" aria-label="Featured and latest stories">
  <div class="f7-featured"><div class="f7-section-title"><h2>Featured Posts</h2><span>THE EDIT</span></div><div class="f7-feature-columns">
    <div class="f7-lead"><?php render_blog_card($hero); ?><a class="f7-button f7-button-outline" href="/search/">All stories ↗</a></div>
    <div class="f7-feature-stack"><?php foreach (array_slice($posts, 1, 3) as $post) { render_blog_card($post, 'row'); } ?></div>
  </div></div>
  <aside class="f7-trending"><div class="f7-section-title"><h2>Latest Reads</h2></div><ol><?php foreach (array_slice($posts, 0, 4) as $i => $post): ?><li><span class="f7-rank"><?= sprintf('%02d', $i + 1) ?>.</span><div><?php if (!empty($post['category'])): ?><a class="post-card-category" href="<?= e_attr(category_url($post['category']['slug'])) ?>"><?= e($post['category']['name']) ?></a><?php endif; ?><h3><a href="<?= e_attr(blog_url($post['slug'])) ?>"><?= e($post['title']) ?></a></h3><p class="post-card-meta"><?= e(format_date($post['publishedAt'] ?? $post['createdAt'] ?? null, 'M j, Y')) ?> · <?= reading_minutes($post) ?> min read</p></div></li><?php endforeach; ?></ol></aside>
</section>
<?php foreach ($categories as $cat): $section = get_blog_feed(['category' => $cat['slug'], 'limit' => 4]); if (!$section['blogs']) continue; ?>
<section class="wrap home-section"><div class="f7-section-title"><h2><?= e($cat['name']) ?></h2><a href="<?= e_attr(category_url($cat['slug'])) ?>">Explore category ↗</a></div><div class="post-grid post-grid--4"><?php foreach ($section['blogs'] as $post) { render_blog_card($post, 'compact'); } ?></div></section>
<?php endforeach; endif; require __DIR__ . '/includes/footer.php'; ?>
