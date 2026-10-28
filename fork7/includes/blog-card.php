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
    ?>
    <article class="post-card post-card--<?= e_attr($size) ?>">
      <a class="post-card-thumb" href="<?= e_attr(blog_url($blog['slug'] ?? '')) ?>">
        <?php if ($img): ?>
          <img src="<?= e_attr($img) ?>" alt="<?= e_attr($blog['featuredImageAlt'] ?? $blog['title'] ?? '') ?>" loading="lazy" width="400" height="260">
        <?php else: ?>
          <span class="post-card-placeholder" aria-hidden="true"><?= e(first_character(site_name())) ?></span>
        <?php endif; ?>
      </a>
      <div class="post-card-body">
        <?php if ($category): ?><a class="post-card-category" href="<?= e_attr(category_url($category['slug'])) ?>"><span><?= e($category['name']) ?></span></a><?php endif; ?>
        <h3 class="post-card-title"><a href="<?= e_attr(blog_url($blog['slug'] ?? '')) ?>"><?= e($blog['title'] ?? '') ?></a></h3>
        <p class="post-card-excerpt"><?= e(str_replace("\u{00A0}", ' ', excerpt_text($blog, $size === 'compact' ? 90 : 140))) ?></p>
        <div class="post-card-meta">
          <span class="post-card-author-avatar" aria-hidden="true"><?= e(first_character($blog['authorName'] ?? site_name())) ?></span>
          <span><?= e($blog['authorName'] ?? site_name()) ?></span>
          <span aria-hidden="true">&middot;</span><span><?= reading_minutes($blog) ?> min read</span>
          <time datetime="<?= e_attr($blog['publishedAt'] ?? $blog['createdAt'] ?? '') ?>"><?= e($date) ?></time>
        </div>
      </div>
    </article>
    <?php
}
