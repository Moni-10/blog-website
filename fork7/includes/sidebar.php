<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}
/** @var string|null $sidebarExclude slug to exclude from Recent Posts (e.g. the blog currently being viewed) */
$sidebarExclude = $sidebarExclude ?? null;
$recentFeed = get_blog_feed(['limit' => RECENT_POSTS_COUNT + 1]);
$recentPosts = array_values(array_filter($recentFeed['blogs'], fn($b) => ($b['slug'] ?? '') !== $sidebarExclude));
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
    <h3>Recent Posts</h3>
    <?php foreach ($recentPosts as $post): $img = featured_image($post); ?>
      <a class="recent-post" href="<?= e_attr(blog_url($post['slug'])) ?>">
        <?php if ($img): ?><img src="<?= e_attr($img) ?>" alt="<?= e_attr($post['featuredImageAlt'] ?? $post['title'] ?? '') ?>" loading="lazy" width="72" height="54"><?php else: ?><span class="recent-post-noimg" aria-hidden="true"><?= e(first_character(site_name())) ?></span><?php endif; ?>
        <span>
          <strong><?= e($post['title'] ?? '') ?></strong>
          <small><?= e(format_date($post['publishedAt'] ?? $post['createdAt'] ?? null, 'M j, Y')) ?></small>
        </span>
      </a>
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
