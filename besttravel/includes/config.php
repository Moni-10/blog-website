<?php

/**
 * Central configuration. Never accessed directly over HTTP (blocked in .htaccess
 * and guarded below), and never echoes secrets into HTML/JS output.
 */
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

// Production-safe error handling: never leak stack traces or file paths to visitors.
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

// Fallback only, used if the Website record can't be reached. The real name
// and tagline come from the Admin Dashboard's Website record (see site_name()
// and site_tagline() in functions.php), same as categories are data-driven.
define('SITE_NAME_FALLBACK', 'Yoyo Guide');
define('SITE_TAGLINE_FALLBACK', 'Fresh stories, useful guides and smart updates for curious readers.');
define('SITE_LOGO_URL', '/assets/images/yoyoguidelogo.png');
define('SITE_FAVICON_URL', '/assets/images/yoyoguidelogo.png');

// The Node/Express blog API. Public GET endpoints only. This site never sends
// an admin key and never calls an admin-authenticated route.
define('BLOG_API_BASE_URL', rtrim(getenv('BLOG_API_BASE_URL') ?: 'https://api.blog-panel.clickstar.pl', '/'));

// The company website this public site represents, matched against the API's
// per-domain public feed. Override with SITE_DOMAIN env var on a real host.
define('SITE_DOMAIN', getenv('SITE_DOMAIN') ?: preg_replace('/^www\./i', '', strtolower((string)($_SERVER['HTTP_HOST'] ?? 'localhost'))));

define('CACHE_DIR', __DIR__ . '/../cache');
define('CACHE_TTL', 90);       // short cache for listings/feeds
define('CACHE_TTL_LONG', 300); // longer cache for categories (rarely change)
define('BLOGS_PER_PAGE', 9);
define('RECENT_POSTS_COUNT', 5);
define('RELATED_POSTS_COUNT', 3);
