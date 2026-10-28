<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}
$footerCategories = array_slice(navigation_categories(), 0, 5);

$footerLatestFeed = get_blog_feed(['limit' => 3]);
$footerLatestPosts = array_slice($footerLatestFeed['blogs'] ?? [], 0, 3);
?>
</main>

<footer class="site-footer">
  <div class="wrap footer-cta-wrap">
    <section class="footer-cta" aria-label="Discover more stories">
      <div>
        <span class="footer-cta-kicker">Never miss a story</span>
        <h2>Make your next read<br>your best discovery.</h2>
        <p>Fresh travel ideas, useful guides and inspiring stories, all in one place.</p>
      </div>
      <div class="footer-cta-actions">
        <a class="footer-cta-primary" href="/">Explore stories <span aria-hidden="true">&#8594;</span></a>
        <a class="footer-cta-secondary" href="/search/">Search articles <span aria-hidden="true">&#8594;</span></a>
      </div>
    </section>
  </div>
  <div class="wrap footer-main">
    <div class="footer-brand">
      <a class="logo logo--image logo--on-dark" href="/"><img src="<?= e_attr(site_logo()) ?>" alt="<?= e_attr(site_name()) ?>"></a>
      <p class="footer-about">
        <?= e(site_name()) ?> brings together travel inspiration, useful guides and daily updates from business,
        technology, lifestyle, education, sports and more.
      </p>
      <div class="footer-social">
        <a href="#" aria-label="Facebook">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.5h2.5l.5-3h-3V8.5c0-.9.25-1.5 1.5-1.5H16.5V4.3c-.27-.04-1.2-.13-2.28-.13-2.26 0-3.8 1.38-3.8 3.9V10.5h-2.5v3h2.5V21h3z"/></svg>
        </a>
        <a href="#" aria-label="Instagram">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12 2.2c2.7 0 3 0 4.1.06 1 .05 1.6.2 2 .35.5.2.9.4 1.3.8.4.4.6.8.8 1.3.15.4.3 1 .35 2 .06 1.1.06 1.4.06 4.1s0 3-.06 4.1c-.05 1-.2 1.6-.35 2-.2.5-.4.9-.8 1.3-.4.4-.8.6-1.3.8-.4.15-1 .3-2 .35-1.1.06-1.4.06-4.1.06s-3 0-4.1-.06c-1-.05-1.6-.2-2-.35-.5-.2-.9-.4-1.3-.8-.4-.4-.6-.8-.8-1.3-.15-.4-.3-1-.35-2C2.2 15 2.2 14.7 2.2 12s0-3 .06-4.1c.05-1 .2-1.6.35-2 .2-.5.4-.9.8-1.3.4-.4.8-.6 1.3-.8.4-.15 1-.3 2-.35C9 2.2 9.3 2.2 12 2.2zm0 1.8c-2.65 0-2.96 0-4 .06-.86.04-1.33.18-1.64.3-.4.16-.7.34-1 .64-.3.3-.48.6-.64 1-.12.31-.26.78-.3 1.64C4.36 8.7 4.36 9 4.36 12s0 3.3.06 4.36c.04.86.18 1.33.3 1.64.16.4.34.7.64 1 .3.3.6.48 1 .64.31.12.78.26 1.64.3 1.04.06 1.35.06 4 .06s2.96 0 4-.06c.86-.04 1.33-.18 1.64-.3.4-.16.7-.34 1-.64.3-.3.48-.6.64-1 .12-.31.26-.78.3-1.64.06-1.04.06-1.35.06-4s0-2.96-.06-4c-.04-.86-.18-1.33-.3-1.64a2.7 2.7 0 0 0-.64-1 2.7 2.7 0 0 0-1-.64c-.31-.12-.78-.26-1.64-.3-1.04-.06-1.35-.06-4-.06zm0 3.5a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9zm0 1.8a2.7 2.7 0 1 0 0 5.4 2.7 2.7 0 0 0 0-5.4zm4.7-2a1.05 1.05 0 1 1 0 2.1 1.05 1.05 0 0 1 0-2.1z"/></svg>
        </a>
        <a href="#" aria-label="X">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M16.9 4h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.3-5.6 6.3H1.3l7.3-8.3L1 4h6.5l4.4 5.7L16.9 4Zm-1.1 16.2h1.7L6.6 5.7H4.8l11 14.5Z"/></svg>
        </a>
        <a href="#" aria-label="YouTube">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M21.6 7.2s-.2-1.5-.8-2.1c-.8-.8-1.7-.8-2.1-.9C15.9 4 12 4 12 4s-3.9 0-6.7.2c-.4.1-1.3.1-2.1.9-.6.6-.8 2.1-.8 2.1S2.2 9 2.2 10.7v1.6C2.2 14 2.4 15.8 2.4 15.8s.2 1.5.8 2.1c.8.8 1.8.8 2.3.9 1.7.15 6.5.2 6.5.2s3.9 0 6.7-.2c.4-.1 1.3-.1 2.1-.9.6-.6.8-2.1.8-2.1s.2-1.7.2-3.5v-1.6c0-1.7-.2-3.5-.2-3.5zM9.9 14.6V8.9l5.4 2.85-5.4 2.85z"/></svg>
        </a>
      </div>
    </div>

    <div class="footer-panel footer-panel--categories">
      <h2>Explore Categories</h2>
      <nav class="footer-category-cloud" aria-label="Footer categories">
        <?php foreach ($footerCategories as $cat): ?>
          <a href="<?= e_attr(category_url($cat['slug'])) ?>"><?= e($cat['name']) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>

<div class="footer-panel footer-panel--latest">
  <h2>Recent Blogs</h2>

  <div class="footer-recent-list">
    <?php foreach ($footerLatestPosts as $post): ?>
      <a class="footer-recent-item"
         href="<?= e_attr(blog_url($post['slug'] ?? '')) ?>">
        <?php $image = featured_image($post); ?>

        <?php if ($image): ?>
          <img
            src="<?= e_attr($image) ?>"
            alt="<?= e_attr($post['featuredImageAlt'] ?? $post['title'] ?? '') ?>"
            width="64"
            height="64"
            loading="lazy">
        <?php else: ?>
          <span class="footer-recent-placeholder" aria-hidden="true">B</span>
        <?php endif; ?>

        <span class="footer-recent-text">
          <?php if (!empty($post['category']['name'])): ?>
            <small><?= e($post['category']['name']) ?></small>
          <?php endif; ?>
          <strong><?= e($post['title'] ?? '') ?></strong>
        </span>
      </a>
    <?php endforeach; ?>
  </div>

  <a class="footer-recent-view-all" href="/search/">
    View All <span aria-hidden="true">&rarr;</span>
  </a>
</div>
  </div>

  <div class="wrap footer-bottom">
    <span>&copy; <?= e(date('Y')) ?> <?= e(site_name()) ?>. All rights reserved.</span>
    <span>Designed for daily reading across every category.</span>
  </div>
</footer>

<button id="backToTop" class="back-to-top" type="button" aria-label="Back to top">&uarr;</button>

<script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../assets/js/app.js') ?>" defer></script>
</body>
</html>
