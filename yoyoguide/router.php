<?php
// Dev-only router to emulate the .htaccess rewrite rules when using PHP's
// built-in server (php -S). Not used in production (Apache reads .htaccess).
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (
    preg_match('#^/(includes|cache|logs)/#', $uri) ||
    preg_match('#(^|/)\.#', $uri) ||
    preg_match('#\.(bak|backup|old|orig|save|sql|sqlite|ini|sh|ps1|json|log|md)$#i', $uri)
) {
    http_response_code(403);
    exit('Forbidden');
}
if (preg_match('#^/category/([a-z0-9-]+)/page/([0-9]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1]; $_GET['page'] = $m[2];
    require __DIR__ . '/category.php';
    return true;
}
if (preg_match('#^/category/([a-z0-9-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/category.php';
    return true;
}
if (preg_match('#^/blog/([a-z0-9-]+)/?$#', $uri, $m)) {
    $_GET['slug'] = $m[1];
    require __DIR__ . '/blog.php';
    return true;
}
if (preg_match('#^/search/?$#', $uri)) {
    require __DIR__ . '/search.php';
    return true;
}
if ($uri === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    return true;
}
if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    return true;
}
return false; // let the built-in server serve static files (css/js/robots.txt) directly
