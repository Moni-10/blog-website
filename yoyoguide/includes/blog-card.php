<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Renders one blog card. Used by the homepage, category template and search
 * results so there is exactly one markup source for a "post preview".
 */
function badge_color(string $slug): string
{
    $palette = ['#2fa84f', '#ef2d5e', '#171a21', '#7b3ff2', '#0a8bd6'];
    return $palette[crc32($slug) % count($palette)];
}

function render_blog_card(array $blog, string $size = 'normal'): void
{
    $img = featured_image($blog);
    $category = $blog['category'] ?? null;
    $date = format_date($blog['publishedAt'] ?? $blog['createdAt'] ?? null);
    $authorName = trim((string)($blog['authorName'] ?? '')) ?: site_name();
    if ($size === 'sidebar'): ?>
    <article class="recent-post-card">
      <a class="recent-post-thumb" href="<?= e_attr(blog_url($blog['slug'] ?? '')) ?>">
        <?php if ($img): ?><img src="<?= e_attr($img) ?>" alt="<?= e_attr($blog['featuredImageAlt'] ?? $blog['title'] ?? '') ?>" loading="lazy" width="90" height="90"><?php else: ?><span aria-hidden="true"><?= e(first_character(site_name())) ?></span><?php endif; ?>
      </a>
      <div class="recent-post-copy">
        <?php if ($category): ?><a class="recent-post-category" href="<?= e_attr(category_url($category['slug'])) ?>"><?= e($category['name']) ?></a><?php endif; ?>
        <h4><a href="<?= e_attr(blog_url($blog['slug'] ?? '')) ?>"><?= e($blog['title'] ?? '') ?></a></h4>
        <div class="recent-post-meta"><span><?= e($authorName) ?></span><span aria-hidden="true">&#9675;</span><time datetime="<?= e_attr($blog['publishedAt'] ?? $blog['createdAt'] ?? '') ?>"><?= e($date) ?></time></div>
      </div>
    </article>
    <?php return; endif; ?>
    <article class="post-card post-card--<?= e_attr($size) ?>">
      <a class="post-card-thumb" href="<?= e_attr(blog_url($blog['slug'] ?? '')) ?>">
        <?php if ($img): ?>
          <img src="<?= e_attr($img) ?>" alt="<?= e_attr($blog['featuredImageAlt'] ?? $blog['title'] ?? '') ?>" loading="lazy" width="400" height="260">
        <?php else: ?>
          <span class="post-card-placeholder" aria-hidden="true"><?= e(first_character(site_name())) ?></span>
        <?php endif; ?>
        <?php if ($category): ?>
          <span class="post-card-badge" style="background:<?= e_attr(badge_color($category['slug'])) ?>"><?= e($category['name']) ?></span>
        <?php endif; ?>
      </a>
      <div class="post-card-body">
        <?php if (!empty($blog['tags']) && is_array($blog['tags'])): ?>
          <div class="post-card-tags"><?php foreach (array_slice($blog['tags'], 0, 3) as $tag): if (!is_string($tag) || $tag === '') continue; ?><a href="/search/?q=<?= e_attr(rawurlencode($tag)) ?>">#<?= e($tag) ?></a> <?php endforeach; ?></div>
        <?php endif; ?>
        <h3 class="post-card-title"><a href="<?= e_attr(blog_url($blog['slug'] ?? '')) ?>"><?= e($blog['title'] ?? '') ?></a></h3>
        <p class="post-card-excerpt"><?= e(excerpt_text($blog, $size === 'compact' ? 90 : 140)) ?></p>
        <div class="post-card-meta">
          <span class="post-card-avatar" aria-hidden="true"><?= e(first_character($authorName)) ?></span>
          <span class="post-card-author"><strong><?= e($authorName) ?></strong><time datetime="<?= e_attr($blog['publishedAt'] ?? $blog['createdAt'] ?? '') ?>"><?= e($date) ?></time></span>
        </div>
        <?php if ($size !== 'sidebar'): ?><a class="post-card-readmore" href="<?= e_attr(blog_url($blog['slug'] ?? '')) ?>">Read More <span aria-hidden="true">&rarr;</span></a><?php endif; ?>
      </div>
    </article>
    <?php
}
