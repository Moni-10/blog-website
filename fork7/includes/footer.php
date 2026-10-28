<?php
if (!defined('APP_BOOTSTRAPPED')) { http_response_code(403); exit('Forbidden'); }
$footerCategories = travel_category_nav_items();
?></main>
<footer class="f7-footer">
  <div class="wrap f7-footer-grid">
    <section class="f7-footer-about" aria-labelledby="footerAbout"><a href="/" class="f7-wordmark" aria-label="Fork7 home">fork<span>7</span><span class="f7-wordmark-dot">.</span></a><h3 id="footerAbout">Thoughts. Insights. Stories.</h3><p><?= e(site_tagline() ?: 'A place for fresh perspectives, thoughtful stories and practical ideas. Read something new. See things differently.') ?></p><span class="f7-footer-signature">Made for curious minds.</span></section>
    <section class="f7-footer-topics" aria-labelledby="footerTopics"><h3 id="footerTopics">Explore topics</h3><nav aria-label="Footer categories" id="footerTopicsNav"><?php foreach ($footerCategories as $i => $cat): ?><a href="<?= e_attr($cat['url']) ?>"<?= $i >= 5 ? ' data-topic-extra' : '' ?>><?= e($cat['name']) ?> <span aria-hidden="true">↗</span></a><?php endforeach; ?></nav><?php if (count($footerCategories) > 5): ?><button type="button" class="f7-topics-toggle" id="footerTopicsToggle" aria-controls="footerTopicsNav" aria-expanded="false" hidden>Read More</button><?php endif; ?><?php if (!$footerCategories): ?><a href="/search/">Browse the journal ↗</a><?php endif; ?></section>
    <section class="f7-footer-discover" aria-labelledby="footerDiscover"><h3 id="footerDiscover">Find your next read</h3><p>A topic on your mind? Search the journal and follow your curiosity.</p><form action="/search/" method="get" role="search"><label class="sr-only" for="footerQuery">Search articles</label><input id="footerQuery" name="q" type="search" maxlength="80" placeholder="Search articles…"><button type="submit" aria-label="Search articles">↗</button></form><nav aria-label="Journal links"><a href="/">The homepage</a><a href="/search/">All articles</a><a href="#main-content">Back to reading ↑</a></nav></section>
  </div>
  <div class="wrap f7-footer-bottom"><span>© <?= e(date('Y')) ?> <?= e(site_name()) ?>. All rights reserved.</span><span>THOUGHTS <b>•</b> INSIGHTS <b>•</b> STORIES</span></div>
</footer>
<button id="backToTop" class="back-to-top" type="button" aria-label="Back to top">↑</button>
<script src="/assets/js/app.js?v=<?= filemtime(__DIR__ . '/../assets/js/app.js') ?>" defer></script>
</body></html>
