<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

/* ---------------------------------------------------------------------
 * Output escaping — every dynamic value must pass through one of these
 * before it reaches HTML. Never print raw API data directly.
 * ------------------------------------------------------------------- */

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function e_attr($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Only ever allow http/https absolute URLs or same-site relative paths.
 * Blocks javascript:, data:, vbscript: and any other scheme-based XSS vector,
 * and blocks protocol-relative //host URLs from being used as open redirects.
 */
function safe_url(?string $url, string $fallback = '#'): string
{
    $url = trim((string)$url);
    if ($url === '') {
        return $fallback;
    }
    if ($url[0] === '/' && (!isset($url[1]) || $url[1] !== '/')) {
        // same-site relative path, safe by construction
        return $url;
    }
    $parts = parse_url($url);
    if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
        return $fallback;
    }
    if (!in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
        return $fallback;
    }
    return $url;
}

/**
 * Redirect only to a known-safe, locally constructed path. Never forwards a
 * user-supplied URL, preventing open-redirect abuse.
 */
function safe_redirect(string $localPath): void
{
    if ($localPath === '' || $localPath[0] !== '/') {
        $localPath = '/';
    }
    header('Location: ' . $localPath, true, 302);
    exit;
}

/* ---------------------------------------------------------------------
 * Rich-text sanitizer for blog content coming from the API.
 * Whitelist-based: only known-safe tags/attributes survive, all inline
 * event handlers and script-bearing tags are removed regardless of case
 * or nesting tricks, since we parse via DOMDocument rather than regex.
 * ------------------------------------------------------------------- */

function sanitize_html(?string $html): string
{
    $html = (string)$html;
    if (trim($html) === '') {
        return '';
    }

    $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'span', 'div',
        'h2', 'h3', 'h4', 'h5', 'ul', 'ol', 'li', 'a', 'img', 'blockquote',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'td', 'th', 'figure',
        'figcaption', 'pre', 'code', 'hr',
    ];
    $allowedAttrs = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan'],
    ];

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    // Wrap in a container + force UTF-8 so DOMDocument doesn't mangle entities.
    $doc->loadHTML(
        '<?xml encoding="utf-8" ?><div id="sanitize-root">' . $html . '</div>',
        LIBXML_NOERROR | LIBXML_NOWARNING
    );
    libxml_clear_errors();

    $root = $doc->getElementById('sanitize-root');
    if (!$root) {
        return '';
    }

    $strip = [];
    $walk = function (DOMNode $node) use (&$walk, &$strip, $allowedTags, $allowedAttrs) {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);
                if (!in_array($tag, $allowedTags, true)) {
                    $strip[] = $child; // drop disallowed tags entirely (script, style, iframe, form, ...)
                    continue;
                }
                if ($child->hasAttributes()) {
                    foreach (iterator_to_array($child->attributes) as $attr) {
                        $name = strtolower($attr->name);
                        $allowedForTag = $allowedAttrs[$tag] ?? [];
                        if (str_starts_with($name, 'on') || !in_array($name, $allowedForTag, true)) {
                            $child->removeAttribute($attr->name);
                            continue;
                        }
                        if (in_array($name, ['href', 'src'], true)) {
                            $safe = safe_url($attr->value, '');
                            if ($safe === '') {
                                $child->removeAttribute($attr->name);
                            } else {
                                $child->setAttribute($name, $safe);
                            }
                        }
                    }
                }
                if ($tag === 'a') {
                    $child->setAttribute('rel', 'noopener noreferrer nofollow ugc');
                }
                if ($tag === 'img') {
                    $child->setAttribute('loading', 'lazy');
                }
                $walk($child);
            } elseif (!($child instanceof DOMText) && !($child instanceof DOMComment)) {
                $strip[] = $child;
            }
        }
    };
    $walk($root);
    foreach ($strip as $node) {
        $node->parentNode?->removeChild($node);
    }

    $output = '';
    foreach (iterator_to_array($root->childNodes) as $child) {
        $output .= $doc->saveHTML($child);
    }
    return $output;
}

/* ---------------------------------------------------------------------
 * API client with a small on-disk cache, so a page of blogs is fetched
 * once and reused for a short window instead of hammering the API.
 * ------------------------------------------------------------------- */

function api_get(string $path, array $query = [], int $ttl = CACHE_TTL): ?array
{
    $url = BLOG_API_BASE_URL . $path;
    if ($query) {
        $url .= (str_contains($path, '?') ? '&' : '?') . http_build_query($query);
    }

    $cacheKey = CACHE_DIR . '/' . sha1($url) . '.json';
    if ($ttl > 0 && is_file($cacheKey) && (time() - filemtime($cacheKey)) < $ttl) {
        $cached = @file_get_contents($cacheKey);
        if ($cached !== false) {
            $decoded = json_decode($cached, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
    }

    $body = null;
    $status = 0;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ]);
        $body = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        $context = stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]);
        $body = @file_get_contents($url, false, $context);
        if (isset($http_response_header[0]) && preg_match('/(\d{3})/', $http_response_header[0], $m)) {
            $status = (int)$m[1];
        }
    }

    if ($body === false || $status < 200 || $status >= 300) {
        return null;
    }
    $decoded = json_decode($body, true);
    if (!is_array($decoded)) {
        return null;
    }

    if ($ttl > 0) {
        if (!is_dir(CACHE_DIR)) {
            @mkdir(CACHE_DIR, 0755, true);
        }
        @file_put_contents($cacheKey, json_encode($decoded), LOCK_EX);
    }

    return $decoded;
}

/* ---------------------------------------------------------------------
 * Domain helpers built on top of the existing, unmodified API contract.
 * ------------------------------------------------------------------- */

/**
 * The Website record from the Admin Dashboard for the current domain — the
 * single source of truth for the site's display name, tagline and favicon,
 * so nothing about branding is hard-coded here.
 */
function get_website_config(): ?array
{
    $data = api_get('/api/websites/public/' . rawurlencode(SITE_DOMAIN) . '/config', [], CACHE_TTL_LONG);
    return ($data && !empty($data['success'])) ? $data['website'] : null;
}

function site_name(): string
{
    $website = get_website_config();
    return $website['name'] ?? SITE_NAME_FALLBACK;
}

function site_tagline(): string
{
    $website = get_website_config();
    return $website['description'] ?? '';
}

function site_favicon(): string
{
    $website = get_website_config();
    return safe_url($website['faviconUrl'] ?? '', '/assets/favicon.svg?v=' . filemtime(__DIR__ . '/../assets/favicon.svg'));
}

function normalize_category(array $category): array
{
    return [
        'name' => (string)($category['name'] ?? ''),
        'slug' => (string)($category['slug'] ?? ''),
        'status' => $category['status'] ?? 'active',
    ];
}

function get_categories(): array
{
    static $categories = null;
    if ($categories !== null) {
        return $categories;
    }
    $website = get_website_config();
    if (!$website || empty($website['id'])) {
        return [];
    }
    $data = api_get('/api/blog-categories', ['status' => 'active', 'websiteId' => $website['id']], 0);
    return $categories = is_array($data) ? array_values(array_filter(array_map('normalize_category', $data), fn($cat) => $cat['slug'] !== '' && $cat['name'] !== '')) : [];
}

function find_category(string $slug): ?array
{
    foreach (get_categories() as $category) {
        if (($category['slug'] ?? '') === $slug) {
            return $category;
        }
    }
    return null;
}

/** Navigation follows the active categories assigned to this website. */
function get_navigation_categories(): array
{
    $items = [];
    foreach (get_categories() as $category) {
        $items[] = [
            'name' => $category['name'],
            'slug' => $category['slug'],
            'url' => category_url($category['slug']),
        ];
    }
    return $items;
}

function get_blog_feed(array $params = [], int $ttl = CACHE_TTL): array
{
    $data = api_get('/api/blogs/public/' . rawurlencode(SITE_DOMAIN), $params, $ttl);
    return [
        'website' => $data['website'] ?? null,
        'blogs' => $data['blogs'] ?? [],
        'pagination' => $data['pagination'] ?? ['page' => 1, 'limit' => BLOGS_PER_PAGE, 'total' => 0, 'pages' => 0],
        'ok' => $data !== null,
    ];
}

function get_blog_by_slug(string $slug): array
{
    $data = api_get('/api/blogs/public/' . rawurlencode(SITE_DOMAIN) . '/' . rawurlencode($slug), [], 30);
    return [
        'website' => $data['website'] ?? null,
        'blog' => $data['blog'] ?? null,
        'ok' => $data !== null,
    ];
}

/* ---------------------------------------------------------------------
 * Presentation helpers.
 * ------------------------------------------------------------------- */

function category_url(string $slug): string
{
    return '/category/' . rawurlencode($slug) . '/';
}

function blog_url(string $slug): string
{
    return '/blog/' . rawurlencode($slug) . '/';
}

function excerpt_text(array $blog, int $len = 150): string
{
    $text = trim((string)($blog['excerpt'] ?? ''));
    if ($text === '') {
        $text = trim(strip_tags((string)($blog['content'] ?? '')));
    }
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', str_replace("\xC2\xA0", ' ', $text));
    if (function_exists('mb_strlen') && mb_strlen($text) > $len) {
        return rtrim(mb_substr($text, 0, $len)) . '…';
    }
    return $text;
}

function format_date(?string $iso, string $format = 'F j, Y'): string
{
    if (!$iso) {
        return '';
    }
    $ts = strtotime($iso);
    return $ts ? date($format, $ts) : '';
}

function featured_image(array $blog): string
{
    return (string)($blog['featuredImage'] ?? ($blog['images'][0] ?? ''));
}

function youtube_embed_id(?string $url): ?string
{
    if (!$url) {
        return null;
    }
    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/))([A-Za-z0-9_-]{6,})~', $url, $m)) {
        return $m[1];
    }
    return null;
}

function render_pagination(int $current, int $totalPages, string $baseUrl, bool $queryStyle = false): string
{
    if ($totalPages <= 1) {
        return '';
    }
    $current = max(1, min($current, $totalPages));
    $pageHref = function (int $page) use ($baseUrl, $queryStyle): string {
        if ($queryStyle) {
            return $page === 1 ? $baseUrl : $baseUrl . 'page=' . $page;
        }
        return $page === 1 ? $baseUrl : $baseUrl . 'page/' . $page . '/';
    };
    $links = '';
    $window = 2;
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i !== 1 && $i !== $totalPages && abs($i - $current) > $window) {
            if ($i === 2 || $i === $totalPages - 1) {
                $links .= '<span class="page-ellipsis">…</span>';
            }
            continue;
        }
        $active = $i === $current ? ' class="active"' : '';
        $links .= '<a href="' . e_attr($pageHref($i)) . '"' . $active . '>' . $i . '</a>';
    }
    $prev = $current > 1 ? '<a class="page-nav" href="' . e_attr($pageHref($current - 1)) . '">&larr; Prev</a>' : '';
    $next = $current < $totalPages ? '<a class="page-nav" href="' . e_attr($pageHref($current + 1)) . '">Next &rarr;</a>' : '';
    return '<nav class="pagination" aria-label="Pagination">' . $prev . $links . $next . '</nav>';
}
