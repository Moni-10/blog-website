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

// Fallback only, used if the Website record can't be reached — the real name
// and tagline come from the Admin Dashboard's Website record (see site_name()
// and site_tagline() in functions.php), same as categories are data-driven.
define('SITE_NAME_FALLBACK', 'Fork7');

// The Node/Express blog API. Public GET endpoints only — this site never sends
// an admin key and never calls an admin-authenticated route.
$apiDefault = 'https://api.blog-panel.clickstar.pl';
$apiBase = rtrim(trim((string)(getenv('BLOG_API_BASE_URL') ?: $apiDefault)), '/');
$apiParts = parse_url($apiBase);
$apiScheme = strtolower((string)($apiParts['scheme'] ?? ''));
$apiHost = (string)($apiParts['host'] ?? '');
if ($apiHost === '' || !in_array($apiScheme, ['http', 'https'], true)) {
    $apiBase = $apiDefault;
}
define('BLOG_API_BASE_URL', $apiBase);

// The company website this public site represents, matched against the API's
// per-domain public feed. Override with SITE_DOMAIN env var on a real host.
$domainSource = trim((string)(getenv('SITE_DOMAIN') ?: ($_SERVER['HTTP_HOST'] ?? 'localhost')));
if (str_contains($domainSource, '://')) {
    $domainSource = (string)(parse_url($domainSource, PHP_URL_HOST) ?: 'localhost');
}
$domain = strtolower($domainSource);
$domain = preg_replace('/:\d+$/', '', $domain);
$domain = preg_replace('/^www\./i', '', $domain);
$domain = rtrim((string)$domain, '.');
$validDomain = preg_match('/^(localhost|(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)$/', $domain);
if (!$validDomain || strlen($domain) > 253) {
    $domain = 'localhost';
}
define('SITE_DOMAIN', $domain);

define('CACHE_DIR', __DIR__ . '/../cache');
define('CACHE_TTL', 90);       // short cache for listings/feeds
define('CACHE_TTL_LONG', 300); // longer cache for categories (rarely change)
define('BLOGS_PER_PAGE', 9);
define('RECENT_POSTS_COUNT', 5);
define('RELATED_POSTS_COUNT', 3);
