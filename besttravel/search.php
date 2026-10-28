<?php
require_once __DIR__ . '/includes/bootstrap.php';

$query = trim((string)($_GET['q'] ?? ''));
$query = mb_substr($query, 0, 80);
$page = max(1, (int)($_GET['page'] ?? 1));

$pageTitle = ($query !== '' ? 'Search results for "' . $query . '"' : 'Search') . ' - ' . site_name();
$pageDescription = 'Search articles on ' . site_name() . '.';
$canonicalUrl = 'https://' . SITE_DOMAIN . '/search/';
$robotsMeta = 'noindex,follow'; // search result pages should not be indexed
$sidebarExclude = null;

$feed = ['ok' => true, 'blogs' => [], 'pagination' => ['page' => 1, 'pages' => 0]];
if ($query !== '') {
    $feed = get_blog_feed(['search' => $query, 'page' => $page, 'limit' => BLOGS_PER_PAGE], 30);
}

require __DIR__ . '/includes/header.php';
?>

<div class="wrap content-layout">
  <div class="content-main">
    <header class="list-page-head"><h1><?= $query !== '' ? 'Results for &ldquo;' . e($query) . '&rdquo;' : 'Search articles' ?></h1></header>
    <?php if ($query === ''): ?>
      <div class="empty-state"><h2>Type something to search</h2><p>Use the search box above to find articles.</p></div>
    <?php elseif (!$feed['ok']): ?>
      <div class="empty-state"><h2>Search is temporarily unavailable</h2><p>Please try again shortly.</p></div>
    <?php elseif (!$feed['blogs']): ?>
      <div class="empty-state"><h2>No articles matched your search</h2><p>Try a different keyword.</p></div>
    <?php else: ?>
      <div class="post-grid post-grid--3">
        <?php foreach ($feed['blogs'] as $blog) { render_blog_card($blog); } ?>
      </div>
      <?= render_pagination((int)$feed['pagination']['page'], (int)$feed['pagination']['pages'], '/search/?q=' . rawurlencode($query) . '&', true) ?>
    <?php endif; ?>
  </div>
  <?php require __DIR__ . '/includes/sidebar.php'; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
