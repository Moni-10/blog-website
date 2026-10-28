<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}
/** @var string|null $sidebarExclude slug to exclude from Recent Posts (e.g. the blog currently being viewed) */
$sidebarExclude = $sidebarExclude ?? null;
$sidebarCategory = $sidebarCategory ?? null;
$sidebarExcludeSlugs = $sidebarExcludeSlugs ?? [];
$sidebarParams = ['limit' => RECENT_POSTS_COUNT + count($sidebarExcludeSlugs) + 1];
if ($sidebarCategory) $sidebarParams['category'] = $sidebarCategory;
$recentFeed = get_blog_feed($sidebarParams);
$recentPosts = array_values(array_filter($recentFeed['blogs'], fn($b) => ($b['slug'] ?? '') !== $sidebarExclude && !in_array($b['slug'] ?? '', $sidebarExcludeSlugs, true)));
$recentPosts = array_slice($recentPosts, 0, RECENT_POSTS_COUNT);
$sidebarCategories = travel_category_nav_items();
?>
<aside class="sidebar" aria-label="Sidebar">
  <div class="widget widget-search">
    <h3>Search</h3>
    <form action="/search/" method="get" role="search">
      <input type="search" name="q" placeholder="Search articles…" maxlength="80" aria-label="Search articles" value="<?= isset($_GET['q']) ? e_attr($_GET['q']) : '' ?>">
      <button type="submit">Search</button>
    </form>
  </div>

  <?php if ($recentPosts): ?>
  <div class="widget widget-recent">
    <h3><?= $sidebarCategory ? 'Related Posts' : 'Recent Posts' ?></h3>
    <?php foreach ($recentPosts as $post): $img = featured_image($post); ?>
      <?php render_blog_card($post, 'sidebar'); ?>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if ($sidebarCategories): ?>
  <div class="widget widget-categories">
    <h3>Categories</h3>
    <ul>
      <?php foreach ($sidebarCategories as $cat): ?>
        <li><a href="<?= e_attr($cat['url']) ?>"><?= e($cat['name']) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
</aside>
