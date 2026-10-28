<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Forbidden'); }
// Isolated render checks: no live API requests and no demo data in the website.
define('APP_BOOTSTRAPPED', true);
define('CSP_NONCE', 'test');
define('SITE_DOMAIN', 'fork7.test');
define('SITE_NAME_FALLBACK', 'Fork7');
define('CACHE_TTL', 90);
define('CACHE_TTL_LONG', 300);
define('RELATED_POSTS_COUNT', 3);
define('BLOGS_PER_PAGE', 9);
define('RECENT_POSTS_COUNT', 5);
$_SERVER['REQUEST_URI'] = '/';
$fixtureCategory = ['name' => 'Fresh Dynamic Topic', 'slug' => 'fresh-topic'];
$fixtureBlog = ['slug' => 'a-new-perspective', 'title' => 'A new perspective worth exploring', 'category' => $fixtureCategory, 'content' => '<p>A thoughtful opening paragraph.</p><h2>A useful idea</h2><p>More useful words.</p>', 'excerpt' => 'A fresh idea for your day.', 'publishedAt' => '2026-10-04', 'authorName' => 'Fork7 Desk'];
function api_get(string $path, array $query = [], int $ttl = 90): ?array {
    global $fixtureBlog, $fixtureCategory;
    if (str_contains($path, '/config')) return ['success' => true, 'website' => ['id' => 'fork7-id', 'name' => 'Fork7']];
    if ($path === '/api/blog-categories') {
        if (($query['websiteId'] ?? '') !== 'fork7-id' || $ttl !== 0) throw new Exception('Category scope or refresh failed');
        return [$fixtureCategory];
    }
    if (str_ends_with($path, '/a-new-perspective')) return ['blog' => $fixtureBlog];
    return ['blogs' => [$fixtureBlog, array_merge($fixtureBlog, ['slug' => 'second-story'])], 'pagination' => ['page' => 1, 'pages' => 1]];
}
$source = file_get_contents(__DIR__ . '/../includes/functions.php');
$source = preg_replace('/function api_get\(.*?\n\}\s*(?=\/\*)/s', '', $source, 1);
eval(substr($source, 5));
require __DIR__ . '/../includes/blog-card.php';
function render_page(string $file): string {
    $code = file_get_contents(__DIR__ . '/../' . $file);
    $code = str_replace("require __DIR__ . '/includes/bootstrap.php';", '', $code);
    $code = str_replace('__DIR__', var_export(realpath(__DIR__ . '/..'), true), $code);
    ob_start(); eval('?>' . $code); return ob_get_clean();
}
$home = render_page('index.php');
if (!str_contains($home, 'Fresh Dynamic Topic') || !str_contains($home, 'f7-feature-columns')) throw new Exception('Homepage render failed');
$_GET['slug'] = 'a-new-perspective';
$_SERVER['REQUEST_URI'] = '/blog/a-new-perspective/';
$detail = render_page('blog.php');
if (!str_contains($detail, 'articleToc') || !str_contains($detail, 'A useful idea')) throw new Exception('Detail render failed');
echo "PASS: scoped fresh categories, homepage, missing-image fallback, article rendering, and detail controls\n";
