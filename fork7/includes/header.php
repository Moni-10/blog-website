<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

$pageTitle = $pageTitle ?? site_name();
$pageDescription = $pageDescription ?? site_tagline();
$canonicalUrl = $canonicalUrl ?? site_url(current_request_path());
$ogImage = $ogImage ?? '';
$ogType = $ogType ?? 'website';
$robotsMeta = $robotsMeta ?? 'index,follow';
$structuredData = $structuredData ?? null;

$navCategories = travel_category_nav_items();
?><!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script nonce="<?= e_attr(CSP_NONCE) ?>">(function(){try{var t=localStorage.getItem('fork7-theme');if(t==='dark')document.documentElement.setAttribute('data-theme','dark');}catch(e){}})();</script>
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e_attr($pageDescription) ?>">
<meta name="robots" content="<?= e_attr($robotsMeta) ?>">
<link rel="canonical" href="<?= e_attr($canonicalUrl) ?>">
<meta name="google-site-verification" content="hQ2zuuMMtub4mblqHR4JsmYkGCRMgEnnfWrEU6JzK_g" />
<meta property="og:site_name" content="<?= e_attr(site_name()) ?>">
<meta property="og:type" content="<?= e_attr($ogType) ?>">
<meta property="og:title" content="<?= e_attr($pageTitle) ?>">
<meta property="og:description" content="<?= e_attr($pageDescription) ?>">
<meta property="og:url" content="<?= e_attr($canonicalUrl) ?>">
<?php if ($ogImage): ?><meta property="og:image" content="<?= e_attr($ogImage) ?>"><?php endif; ?>
<meta name="twitter:card" content="<?= $ogImage ? 'summary_large_image' : 'summary' ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
<link rel="stylesheet" href="/assets/css/reference-cards.css?v=<?= filemtime(__DIR__ . '/../assets/css/reference-cards.css') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/images/fork7logo.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/images/fork7logo.png">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/images/fork7logo.png">
<link rel="manifest" href="/site.webmanifest">

<?php if ($structuredData): ?>
<script type="application/ld+json" nonce="<?= e_attr(CSP_NONCE) ?>"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>


<div class="f7-top"><div class="wrap"><span>THOUGHTS <b>•</b> INSIGHTS <b>•</b> STORIES</span><time><?= e((new DateTimeImmutable('now', new DateTimeZone('Asia/Kolkata')))->format('D, d M Y')) ?></time></div></div>
<button type="button" class="theme-toggle f7-top-theme" id="themeToggle" role="switch" aria-checked="false" aria-label="Use dark mode"><span aria-hidden="true">☼</span><i aria-hidden="true"></i><span aria-hidden="true">☾</span></button>
<div class="f7-social-links" aria-label="Social media links"><a href="#" aria-label="Instagram">ig</a><a href="#" aria-label="LinkedIn">in</a><a href="#" aria-label="Facebook">fb</a><a href="#" aria-label="X">x</a></div>
<header class="site-header">
  <nav class="site-nav" id="siteNav" aria-label="Main navigation"><div class="wrap nav-wrap"><button type="button" class="theme-toggle" id="themeToggle" role="switch" aria-checked="false" aria-label="Use dark mode"><span aria-hidden="true">☼ / ☾</span></button><a class="f7-logo f7-nav-logo" href="/" aria-label="<?= e_attr(site_name()) ?> home"><img src="/assets/images/fork7logo.png" alt="<?= e_attr(site_name()) ?>" width="1280" height="1280"></a><ul id="primaryNavList">
    <li><a href="/"<?= current_request_path() === '/' ? ' aria-current="page"' : '' ?>>Home</a></li>
    <?php foreach ($navCategories as $cat): ?><li><a href="<?= e_attr($cat['url']) ?>"<?= str_starts_with(current_request_path(), $cat['url']) ? ' aria-current="page"' : '' ?>><?= e($cat['name']) ?></a></li><?php endforeach; ?>
    <li class="nav-more" id="navMore" hidden><button type="button" class="nav-more-toggle" id="navMoreToggle" aria-haspopup="true" aria-expanded="false" aria-controls="navMoreMenu">Other <span class="nav-more-caret" aria-hidden="true">▾</span></button><ul class="nav-more-menu" id="navMoreMenu"></ul></li>
  </ul><form class="f7-search" action="/search/" method="get" role="search"><input name="q" type="search" maxlength="80" placeholder="Find your next read…" aria-label="Search stories"><button aria-label="Submit search">↗</button></form><button type="button" class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="siteNav" aria-label="Toggle navigation menu"><span></span><span></span><span></span></button></div></nav>
</header>
<main id="main-content">
