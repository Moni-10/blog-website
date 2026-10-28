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

$navCategories = navigation_categories();
$tickerCategories = array_slice($navCategories, 0, 12);
?><!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e_attr($pageDescription) ?>">
<meta name="robots" content="<?= e_attr($robotsMeta) ?>">
<link rel="canonical" href="<?= e_attr($canonicalUrl) ?>">
<meta name="google-site-verification" content="d1kfW2LBaCr_4w-fEaWV_5Pja4JyJrSlwuGVQvPk7yM" />
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
<link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg?v=<?= filemtime(__DIR__ . '/../assets/images/favicon.svg') ?>">
<link rel="alternate icon" href="<?= e_attr(site_favicon()) ?>">
<script>try { var savedTheme = localStorage.getItem('site-theme'); if (savedTheme) document.documentElement.dataset.theme = savedTheme; } catch (e) {}</script>

<?php if ($structuredData): ?>
<script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>

<div class="topbar">
  <div class="wrap topbar-inner">
    <div class="topbar-marquee" aria-label="Latest updates">
      <div class="topbar-track">
        <span class="topbar-date">Fresh stories for <?= e(date('F j, Y')) ?></span>
        <?php $tagline = site_tagline(); if ($tagline): ?><span class="topbar-tagline"><?= e($tagline) ?></span><?php endif; ?>
        <span class="topbar-date" aria-hidden="true">Fresh stories for <?= e(date('F j, Y')) ?></span>
        <?php if ($tagline): ?><span class="topbar-tagline" aria-hidden="true"><?= e($tagline) ?></span><?php endif; ?>
      </div>
    </div>
    <a class="topbar-action" href="/">Latest stories <span aria-hidden="true">&#8594;</span></a>
  </div>
</div>

<header class="site-header">
  <div class="wrap header-shell">
  <div class="header-inner">
    <a class="logo logo--image" href="/"><img src="<?= e_attr(site_logo()) ?>" alt="<?= e_attr(site_name()) ?>"></a>
    <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="siteNav" aria-label="Toggle navigation menu">
      <span></span><span></span><span></span>
    </button>
    <div class="header-tools">
      <button class="theme-toggle" id="themeToggle" type="button" aria-label="Switch to dark mode" aria-pressed="false">
        <svg class="theme-icon theme-icon--sun" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>
        <svg class="theme-icon theme-icon--moon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.4 15.1A8.6 8.6 0 0 1 8.9 3.6 8.6 8.6 0 1 0 20.4 15.1Z"></path></svg>
      </button>
      <form class="header-search" action="/search/" method="get" role="search">
        <input type="search" name="q" placeholder="Search articles..." maxlength="80" aria-label="Search articles" value="<?= isset($_GET['q']) ? e_attr($_GET['q']) : '' ?>">
        <button type="submit" aria-label="Search">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
        </button>
      </form>
    </div>
  </div>
  <nav class="site-nav" id="siteNav">
    <div class="nav-wrap">
      <ul id="primaryNavList">
        <li><a href="/">Home</a></li>
        <?php foreach ($navCategories as $cat): ?>
          <li><a href="<?= e_attr(category_url($cat['slug'])) ?>"><?= e($cat['name']) ?></a></li>
        <?php endforeach; ?>
        <li class="nav-more" id="navMore" hidden>
          <button type="button" class="nav-more-toggle" id="navMoreToggle" aria-haspopup="true" aria-expanded="false">Other <span aria-hidden="true">&#9662;</span></button>
          <ul class="nav-more-menu" id="navMoreMenu"></ul>
        </li>
      </ul>
    </div>
  </nav>
  </div>
  <?php if ($tickerCategories): ?>
  <div class="category-ticker" aria-label="Featured categories">
    <div class="wrap ticker-inner">
      <span class="ticker-label">Explore</span>
      <div class="ticker-window">
        <div class="ticker-track">
          <?php for ($loop = 0; $loop < 2; $loop++): ?>
            <?php foreach ($tickerCategories as $cat): ?>
              <a href="<?= e_attr(category_url($cat['slug'])) ?>"><?= e($cat['name']) ?></a>
            <?php endforeach; ?>
          <?php endfor; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</header>

<main id="main-content">
