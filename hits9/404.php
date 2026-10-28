<?php
if (!defined('APP_BOOTSTRAPPED')) {
    require __DIR__ . '/includes/bootstrap.php';
}
http_response_code(404);
$pageTitle = 'Page Not Found — ' . site_name();
$pageDescription = 'The page you are looking for could not be found.';
$canonicalUrl = 'https://' . SITE_DOMAIN . '/404/';
$robotsMeta = 'noindex,follow';
require __DIR__ . '/includes/header.php';
?>
<section class="wrap empty-state">
  <h1>404 — Page Not Found</h1>
  <p>The article or category you're looking for doesn't exist or may have moved.</p>
  <a class="view-all" href="/">&larr; Back to Home</a>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
