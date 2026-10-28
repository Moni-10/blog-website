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

$pageTitle = ($blog['metaTitle'] ?? '') ?: $blog['title'];
$pageDescription = ($blog['metaDescription'] ?? '') ?: excerpt_text($blog, 160);
$canonicalUrl = ($blog['canonicalUrl'] ?? '') ?: ('https://' . SITE_DOMAIN . blog_url($slug));
$ogImage = ($blog['ogImage'] ?? '') ?: $featured;
$ogType = 'article';
$robotsMeta = (($blog['robotsIndex'] ?? 'index') === 'noindex' ? 'noindex' : 'index') . ',' . (($blog['robotsFollow'] ?? 'follow') === 'nofollow' ? 'nofollow' : 'follow');
$sidebarExclude = $slug;

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
?>

<div class="f7-reading-progress" id="readingProgress" aria-hidden="true"></div>
<div class="wrap f7-breadcrumb"><a href="/">Home</a><span>/</span><?php if ($category): ?><a href="<?= e_attr(category_url($category['slug'])) ?>"><?= e($category['name']) ?></a><span>/</span><?php endif; ?><span>The story</span></div>
<article class="wrap article-layout f7-article">
  <div class="content-main">
    <header class="article-header">
      <span class="eyebrow">THE FORK7 JOURNAL <?php if ($category): ?> / <a href="<?= e_attr(category_url($category['slug'])) ?>"><?= e($category['name']) ?></a><?php endif; ?></span>
      <h1><?= e($blog['title']) ?></h1>
      <?php if (!empty($blog['excerpt'])): ?><p class="article-excerpt"><?= e($blog['excerpt']) ?></p><?php endif; ?>
      <div class="f7-byline"><span class="f7-avatar"><?= e(first_character($blog['authorName'] ?? site_name())) ?></span><div><strong><?= e($blog['authorName'] ?? site_name()) ?></strong><div class="post-card-meta"><time datetime="<?= e_attr($publishedIso ?? '') ?>"><?= e(format_date($publishedIso)) ?></time><span>· <?= reading_minutes($blog) ?> min read</span></div></div><button type="button" class="f7-button f7-button-outline" id="shareStory">Share ↗</button><span id="shareStatus" role="status"></span></div>
    </header>
    <?php if ($featured): ?><figure class="f7-article-cover"><img class="article-featured" src="<?= e_attr($featured) ?>" alt="<?= e_attr($blog['featuredImageAlt'] ?? $blog['title']) ?>" width="960" height="540"><figcaption><?= e($category['name'] ?? 'Stories') ?> <span> / <?= e(site_name()) ?></span></figcaption></figure><?php endif; ?>
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

    <?php if (!empty($blog['faqs']) && is_array($blog['faqs'])): ?>
      <section class="article-faq">
        <h2>Frequently Asked Questions</h2>
        <?php foreach ($blog['faqs'] as $faq): if (empty($faq['question']) || empty($faq['answer'])) continue; ?>
          <details>
            <summary><?= e($faq['question']) ?></summary>
            <p><?= nl2br(e($faq['answer'])) ?></p>
          </details>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

    <?php if (!empty($blog['relatedProducts']) && is_array($blog['relatedProducts'])): ?>
      <section class="article-products">
        <h2>Featured Products</h2>
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
  </div>
  <aside class="f7-reading-rail"><div class="f7-rail-inner"><span class="eyebrow">YOUR READING COMPANION</span><nav id="articleToc" aria-label="On this page" hidden><h3>On this page</h3><ol></ol></nav><div class="f7-rail-note"><span>7 /</span><h3>A different<br>point of view.</h3><p>Keep your curiosity going. There is always another story to discover.</p><a href="/search/">Find your next read ↗</a></div><?php if ($relatedBlogs): ?><h3>Read next</h3><?php foreach (array_slice($relatedBlogs, 0, 2) as $related): ?><a class="f7-rail-story" href="<?= e_attr(blog_url($related['slug'])) ?>"><?= e($related['title']) ?> ↗</a><?php endforeach; endif; ?></div></aside>
  <aside class="detail-sidebar"><section><h3>About Us</h3><p>Welcome to <?= e(site_name()) ?>, a place for useful ideas, thoughtful stories and fresh perspectives.</p><strong>FOLLOW US</strong></section><?php if ($relatedBlogs): ?><section><h3>Most Popular</h3><?php foreach (array_slice($relatedBlogs, 0, 3) as $related): ?><a class="detail-popular-item" href="<?= e_attr(blog_url($related['slug'])) ?>"><?= e($related['title']) ?></a><?php endforeach; ?></section><?php endif; ?><section><h3>Categories</h3><?php foreach (travel_category_nav_items() as $cat): ?><a class="detail-category-item" href="<?= e_attr($cat['url']) ?>"><?= e($cat['name']) ?></a><?php endforeach; ?></section></aside>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
