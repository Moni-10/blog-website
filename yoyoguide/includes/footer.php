<?php
if (!defined('APP_BOOTSTRAPPED')) { http_response_code(403); exit('Forbidden'); }
$footerCategories = travel_category_nav_items();
$footerPosts = get_blog_feed(['limit' => 2])['blogs'];
?></main>
<footer class="site-footer">
  <div class="wrap footer-travel">
    <div class="footer-brand-panel">
      <a class="logo logo--asset footer-logo" href="/" aria-label="<?= e_attr(site_name()) ?>"><img class="logo-img" src="/assets/images/yoyoguidelogo.png" alt="<?= e_attr(site_name()) ?>" width="512" height="512"></a>
      <p><strong><?= e(site_name()) ?></strong> is your place for useful stories, fresh ideas and trusted updates across technology, business, travel, lifestyle and more. <?= e(site_tagline()) ?></p>
    </div>
    <div class="footer-col">
      <h4>Categories</h4>
      <ul><?php foreach (array_slice($footerCategories, 0, 5) as $cat): ?><li><a href="<?= e_attr($cat['url']) ?>"><?= e($cat['name']) ?></a></li><?php endforeach; ?></ul>
      <?php if (count($footerCategories) > 5): ?><details class="footer-more"><summary>More Categories</summary><ul><?php foreach (array_slice($footerCategories, 5) as $cat): ?><li><a href="<?= e_attr($cat['url']) ?>"><?= e($cat['name']) ?></a></li><?php endforeach; ?></ul></details><?php endif; ?>
    </div>
    <div class="footer-col"><h4>Latest Posts</h4><ul><?php foreach ($footerPosts as $post): ?><li><a href="<?= e_attr(blog_url($post['slug'])) ?>"><?= e($post['title']) ?></a></li><?php endforeach; ?></ul></div>
    <div class="footer-col"><h4>Quick Links</h4><ul>
      <li><a href="/about-us/">About Us</a></li><li><a href="/shop/">Shop</a></li><li><a href="/contact/">Contact</a></li><li><a href="/affiliate-disclosure/">Affiliate Disclosure</a></li><li><a href="/privacy-policy/">Privacy Policy</a></li><li><a href="/terms-of-service/">Terms of Service</a></li>
    </ul></div>
  </div>
  <div class="footer-copyright"><div class="wrap footer-bottom">Copyright &copy; <?= e(date('Y')) ?> <?= e(site_name()) ?>. All rights reserved.</div></div>
</footer>
<button id="backToTop" class="back-to-top" type="button" aria-label="Back to top">&uarr;</button>
<script src="/assets/js/app.js" defer></script>
</body></html>
