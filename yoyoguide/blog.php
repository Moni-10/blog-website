<?php
require __DIR__ . '/includes/bootstrap.php';

$slug = strtolower(trim((string)($_GET['slug'] ?? '')));
$slug = preg_replace('/[^a-z0-9-]/', '', $slug);

if ($slug === '') {
    safe_redirect('/');
}

$result = get_blog_by_slug($slug);
$blog = $result['blog'];

if (!$result['ok'] || !$blog) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$category = $blog['category'] ?? null;
$publishedIso = $blog['publishedAt'] ?? $blog['createdAt'] ?? null;
$featured = featured_image($blog);

$pageTitle = $blog['metaTitle'] ?: $blog['title'];
$pageDescription = $blog['metaDescription'] ?: excerpt_text($blog, 160);
$canonicalUrl = $blog['canonicalUrl'] ?: ('https://' . SITE_DOMAIN . blog_url($slug));
$ogImage = $blog['ogImage'] ?: $featured;
$ogType = 'article';
$robotsMeta = (($blog['robotsIndex'] ?? 'index') === 'noindex' ? 'noindex' : 'index') . ',' . (($blog['robotsFollow'] ?? 'follow') === 'nofollow' ? 'nofollow' : 'follow');
$sidebarExclude = $slug;
$sidebarCategory = $category['slug'] ?? null;

$structuredData = [
    '@context' => 'https://schema.org',
    '@type' => $blog['schemaType'] ?? 'BlogPosting',
    'headline' => $blog['title'],
    'description' => $pageDescription,
    'datePublished' => $publishedIso,
    'dateModified' => $publishedIso,
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
    'author' => ['@type' => 'Person', 'name' => $blog['authorName'] ?? site_name()],
    'publisher' => ['@type' => 'Organization', 'name' => site_name()],
];
if ($ogImage) {
    $structuredData['image'] = [$ogImage];
}

require __DIR__ . '/includes/header.php';

$relatedBlogs = [];
if ($category) {
    $relatedFeed = get_blog_feed(['category' => $category['slug'], 'limit' => RELATED_POSTS_COUNT + 1]);
    $relatedBlogs = array_slice(array_values(array_filter($relatedFeed['blogs'], fn($b) => ($b['slug'] ?? '') !== $slug)), 0, RELATED_POSTS_COUNT);
}
$youtubeId = youtube_embed_id($blog['youtubeUrl'] ?? null);
$recentArticleFeed = get_blog_feed(['limit' => 4]);
$recentArticlePosts = array_slice(array_values(array_filter($recentArticleFeed['blogs'], fn($post) => ($post['slug'] ?? '') !== $slug)), 0, 3);
?>

<article class="wrap article-layout">
  <div class="content-main">
    <header class="article-header">
      <?php if ($category): ?><a class="post-card-category" href="<?= e_attr(category_url($category['slug'])) ?>"><?= e($category['name']) ?></a><?php endif; ?>
      <h1><?= e($blog['title']) ?></h1>
      <?php if (!empty($blog['excerpt'])): ?><p class="article-excerpt"><?= e($blog['excerpt']) ?></p><?php endif; ?>
      <div class="post-card-meta">
        <span>By <?= e($blog['authorName'] ?? site_name()) ?></span>
        <span aria-hidden="true">&middot;</span>
        <time datetime="<?= e_attr($publishedIso ?? '') ?>"><?= e(format_date($publishedIso)) ?></time>
      </div>
    </header>

    <?php if ($featured): ?>
      <img class="article-featured" src="<?= e_attr($featured) ?>" alt="<?= e_attr($blog['featuredImageAlt'] ?? $blog['title']) ?>" width="960" height="540">
    <?php endif; ?>

    <div class="article-content"><?= sanitize_html($blog['content'] ?? '') ?></div>

    <?php if ($youtubeId): ?>
      <div class="article-video">
        <iframe src="https://www.youtube-nocookie.com/embed/<?= e_attr($youtubeId) ?>" title="Video" allow="accelerometer; encrypted-media; picture-in-picture" allowfullscreen loading="lazy"></iframe>
      </div>
    <?php endif; ?>

    <?php if (!empty($blog['tags']) && is_array($blog['tags'])): ?>
      <div class="article-tags">
        <?php foreach ($blog['tags'] as $tag): if (!$tag) continue; ?>
          <a href="<?= e_attr('/search/?q=' . rawurlencode($tag)) ?>"><?= e($tag) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($blog['relatedProducts']) && is_array($blog['relatedProducts'])): ?>
      <section class="article-products">
        <h2>Related Products</h2>
        <div class="product-grid">
          <?php foreach ($blog['relatedProducts'] as $product): ?>
            <div class="product-card">
              <?php if (!empty($product['image'])): ?><img src="<?= e_attr($product['image']) ?>" alt="<?= e_attr($product['name'] ?? 'Product') ?>" loading="lazy" width="200" height="150"><?php endif; ?>
              <strong><?= e($product['name'] ?? 'Product') ?></strong>
              <?php if (!empty($product['shortDescription'])): ?><p><?= e($product['shortDescription']) ?></p><?php endif; ?>
              <?php if (isset($product['price'])): ?><span class="product-price">₹<?= e(number_format((float)$product['price'], 2)) ?></span><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($relatedBlogs): ?>
      <section class="article-related">
        <h2>Related Articles</h2>
        <div class="post-grid post-grid--3">
          <?php foreach ($relatedBlogs as $related) { render_blog_card($related, 'compact'); } ?>
        </div>
      </section>
    <?php endif; ?>
    <?php if ($recentArticlePosts): ?>
      <section class="article-related">
        <h2>Recent Posts</h2>
        <div class="post-grid post-grid--3">
          <?php foreach ($recentArticlePosts as $recent) { render_blog_card($recent); } ?>
        </div>
      </section>
    <?php endif; ?>
  </div>
  <?php require __DIR__ . '/includes/sidebar.php'; ?>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
