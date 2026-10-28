<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}
$footerCategories = array_slice(get_navigation_categories(), 0, 5);
$footerRecent = get_blog_feed(['limit' => 5])['blogs'];
?></main>

<footer class="site-footer">
  <div class="wrap footer-grid"><div class="footer-col footer-brand"><a class="logo logo--image footer-logo" href="/"><img src="/assets/images/hits9-logo.png" alt="<?= e_attr(site_name()) ?>" loading="lazy"></a><p class="footer-description"><?= e(site_tagline() ?: (site_name() . ' brings you fresh stories and useful ideas across technology, business, lifestyle and more. Discover something new every day.')) ?></p><span class="footer-brand-label">MADE FOR CURIOUS MINDS</span></div>
    <div class="footer-col">
      <h4>Recent Posts</h4>
      <ul>
        <?php foreach ($footerRecent as $post): ?>
          <li><a href="<?= e_attr(blog_url($post['slug'] ?? '')) ?>"><?= e($post['title'] ?? '') ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Categories</h4>
      <ul class="footer-topic-list">
        <?php foreach ($footerCategories as $cat): ?>
          <li><a href="<?= e_attr($cat['url']) ?>"><?= e($cat['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Explore</h4>
      <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/search/">Search</a></li>
        <li><a href="/sitemap.xml">Sitemap</a></li>
      </ul>
    </div>
  </div>
  <div class="wrap footer-bottom">
    <a class="back-to-top" href="#">Back to top ↑</a><span>&copy; Copyright <?= e(date('Y')) ?>, All Rights Reserved &nbsp;|&nbsp; <a href="/"><?= e(site_name()) ?></a></span>
  </div>
</footer>

<script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../assets/js/app.js') ?>" defer></script>
</body>
</html>
