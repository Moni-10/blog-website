<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = strtolower(trim((string)($_GET['slug'] ?? '')));
$slug = preg_replace('/[^a-z0-9-]/', '', $slug);
$page = max(1, (int)($_GET['page'] ?? 1));

if ($slug === '') {
    safe_redirect('/');
}

$category = find_category($slug);
if (!$category) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$feed = get_blog_feed(['category' => $slug, 'page' => $page, 'limit' => BLOGS_PER_PAGE]);
$blogs = $feed['blogs'];
$pagination = $feed['pagination'];

$pageTitle = $category['name'] . ' — ' . site_name();
$pageDescription = 'Browse the latest ' . $category['name'] . ' articles and updates from ' . site_name() . '.';
$canonicalUrl = 'https://' . SITE_DOMAIN . category_url($slug) . ($page > 1 ? 'page/' . $page . '/' : '');
$ogImage = $blogs ? featured_image($blogs[0]) : '';
$sidebarExclude = null;

require __DIR__ . '/includes/header.php';
?>

<div class="wrap content-layout">
  <div class="content-main">
    <header class="list-page-head">
      <h1><?= e($category['name']) ?></h1>
      <p><?= e($pageDescription) ?></p>
    </header>
    <?php if (!$feed['ok']): ?>
      <div class="empty-state"><h2>Articles could not be loaded</h2><p>Please try again shortly.</p></div>
    <?php elseif (!$blogs): ?>
      <div class="empty-state"><h2>No articles in this category yet</h2><p>Check back soon — new stories are published regularly.</p></div>
    <?php else: ?>
      <div class="post-grid post-grid--3">
        <?php foreach ($blogs as $blog) { render_blog_card($blog); } ?>
      </div>
      <?= render_pagination((int)$pagination['page'], (int)$pagination['pages'], category_url($slug)) ?>
    <?php endif; ?>
  </div>
  <?php require __DIR__ . '/includes/sidebar.php'; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
