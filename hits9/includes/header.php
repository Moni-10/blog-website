<?php
if (!defined('APP_BOOTSTRAPPED')) {
    http_response_code(403);
    exit('Forbidden');
}

$pageTitle = $pageTitle ?? site_name();
$pageDescription = $pageDescription ?? site_tagline();
$canonicalUrl = $canonicalUrl ?? ('https://' . SITE_DOMAIN . $_SERVER['REQUEST_URI']);
$ogImage = $ogImage ?? '';
$ogType = $ogType ?? 'website';
$robotsMeta = $robotsMeta ?? 'index,follow';
$structuredData = $structuredData ?? null;

$navCategories = get_navigation_categories();
$currentPath = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') ?: '/';
?><!doctype html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="google-site-verification" content="S-zVY5tresIDYiyI8S2o2pjGLdwmz1_usKSEG2ezikk" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="/assets/js/theme.js?v=<?= filemtime(__DIR__ . '/../assets/js/theme.js') ?>"></script>
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e_attr($pageDescription) ?>">
<meta name="robots" content="<?= e_attr($robotsMeta) ?>">
<link rel="canonical" href="<?= e_attr($canonicalUrl) ?>">

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
<link rel="icon" href="<?= e_attr(site_favicon()) ?>">

<?php if ($structuredData): ?>
<script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>

<div class="topbar">
  <div class="wrap topbar-inner">
    
    <span class="topbar-date">
      <?= e(date('l, F j, Y')) ?>
    </span>

    <marquee class="topbar-tagline" direction="left" scrollamount="5">
      <?php foreach ($navCategories as $index => $cat): ?>
        <?= $index > 0 ? ' &nbsp; | &nbsp; ' : '' ?><a href="<?= e_attr($cat['url']) ?>"><?= e($cat['name']) ?></a>
      <?php endforeach; ?>
    </marquee>

  </div>
</div>

<header class="site-header">
  <div class="wrap header-inner">
    <div class="header-brand">
      <a class="logo logo--image" href="/"><img src="/assets/images/hits9-logo.png" alt="<?= e_attr(site_name()) ?>"></a>
    </div>
    <a class="header-explore" href="/search/">Find your next read <span aria-hidden="true">↗</span></a>
    <button type="button" class="theme-toggle" id="themeToggle" role="switch" aria-checked="true" aria-label="Use light mode" title="Use light mode"><svg class="theme-moon" viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20.5 14A9 9 0 0 1 10 3.5 9 9 0 1 0 20.5 14Z"/></svg><svg class="theme-sun" viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M5 5l1.5 1.5m11 11L19 19M5 19l1.5-1.5m11-11L19 5"/></svg></button>
    <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="siteNav" aria-label="Toggle navigation menu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <nav class="site-nav" id="siteNav">
    <div class="wrap nav-wrap">
      <ul id="primaryNavList">
        <li><a class="<?= $currentPath === '/' ? 'active' : '' ?>" href="/">Home</a></li>
        <?php foreach ($navCategories as $cat): $catHref = $cat['url']; ?>
          <li><a class="<?= rtrim($catHref, '/') === $currentPath ? 'active' : '' ?>" href="<?= e_attr($catHref) ?>"><?= e($cat['name']) ?></a></li>
        <?php endforeach; ?>
        <li class="nav-more" id="navMore" hidden>
          <button type="button" class="nav-more-toggle" id="navMoreToggle" aria-haspopup="true" aria-expanded="false" aria-controls="navMoreMenu">Other <span aria-hidden="true">&#9662;</span></button>
          <ul class="nav-more-menu" id="navMoreMenu"></ul>
        </li>
      </ul>
      <form class="header-search" action="/search/" method="get" role="search">
        <input type="search" name="q" placeholder="Search for" maxlength="80" aria-label="Search articles" value="<?= isset($_GET['q']) ? e_attr($_GET['q']) : '' ?>">
        <button type="submit" aria-label="Search"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="10" cy="10" r="6"/><path d="m15 15 5 5"/></svg></button>
      </form>
    </div>
  </nav>
</header>

<main id="main-content">
