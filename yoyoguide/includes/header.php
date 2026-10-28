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
$headerDate = new DateTimeImmutable('now', new DateTimeZone('Asia/Kolkata'));
$trendingFeed = get_blog_feed(['limit' => 5]);
$trendingPosts = $trendingFeed['blogs'];
$marqueeItems = array_values(array_filter(array_map(fn($post) => (string)($post['title'] ?? ''), $trendingPosts)));
?><!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="google-site-verification" content="hrLoxVkTKEkeOJBL5otvMbC-aE_DV7bpzj731FiaEog" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<script nonce="<?= e_attr(CSP_NONCE) ?>">(function(){var t;try{t=localStorage.getItem('yg-theme');}catch(e){}if(t==='dark'||(t!=='light'&&window.matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.setAttribute('data-theme','dark');})();</script>
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
<link rel="icon" type="image/x-icon" href="/favicon.ico?v=<?= filemtime(__DIR__ . '/../favicon.ico') ?>">
<link rel="icon" type="image/png" sizes="64x64" href="/assets/images/favicon.png?v=<?= filemtime(__DIR__ . '/../assets/images/favicon.png') ?>">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">

<?php if ($structuredData): ?>
<script type="application/ld+json" nonce="<?= e_attr(CSP_NONCE) ?>"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>

<div class="topbar">
  <div class="wrap topbar-inner">
    <span class="topbar-date"><span aria-hidden="true">&#9635;</span> <?= e($headerDate->format('D, j M Y')) ?></span>
    <div class="topbar-social">
      <a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.5h2.5l.5-3h-3V8.5c0-.9.25-1.5 1.5-1.5H16.5V4.3c-.27-.04-1.2-.13-2.28-.13-2.26 0-3.8 1.38-3.8 3.9V10.5h-2.5v3h2.5V21h3z"/></svg></a>
      <a href="#" aria-label="Twitter"><svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M22 5.9c-.7.3-1.5.5-2.3.6a4 4 0 0 0 1.75-2.2c-.8.45-1.6.78-2.5.96A3.98 3.98 0 0 0 12 8.5c0 .3.03.6.1.9-3.3-.17-6.2-1.75-8.15-4.15a4 4 0 0 0 1.24 5.3c-.65-.02-1.27-.2-1.8-.5v.05a4 4 0 0 0 3.2 3.9c-.6.16-1.22.18-1.83.07a4 4 0 0 0 3.72 2.77A8 8 0 0 1 2 18.4a11.3 11.3 0 0 0 6.1 1.8c7.3 0 11.3-6.05 11.3-11.3v-.5c.8-.55 1.45-1.25 2-2.05-.7.32-1.5.53-2.3.62z"/></svg></a>
      <button type="button" class="theme-toggle" id="themeToggle" role="switch" aria-checked="false" aria-label="Toggle dark mode">
        <span class="theme-toggle-icon theme-toggle-sun" aria-hidden="true">&#9728;</span>
        <span class="theme-toggle-track"><span class="theme-toggle-knob"></span></span>
        <span class="theme-toggle-icon theme-toggle-moon" aria-hidden="true">&#9789;</span>
      </button>
    </div>
  </div>
</div>

<header class="site-header">
  <div class="wrap header-inner">
    <a class="logo logo--travel logo--asset" href="/" aria-label="<?= e_attr(site_name()) ?>">
      <img class="logo-img" src="/assets/images/yoyoguidelogo.png" alt="<?= e_attr(site_name()) ?>" width="512" height="512">
    </a>
    <div class="header-title-block" aria-label="Site introduction">
      <div class="header-intro"><span>YOUR GUIDE TO WHAT MATTERS</span><span>Explore. Learn. Stay updated.</span><span>Fresh stories, useful ideas and trusted updates from <?= e(site_name()) ?>.</span></div>
      <img class="header-banner" src="/assets/images/ban.png" alt="Discover what matters — advertise with YoyoGuide" width="340" height="112">
    </div>
    <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="siteNav" aria-label="Toggle navigation menu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <nav class="site-nav" id="siteNav">
    <div class="wrap nav-wrap">
      <ul id="primaryNavList">
        <li><a href="/"<?= current_request_path() === '/' ? ' aria-current="page"' : '' ?>>Home</a></li>
        <?php foreach ($navCategories as $cat): ?>
          <li><a href="<?= e_attr($cat['url']) ?>"<?= str_starts_with(current_request_path(), $cat['url']) ? ' aria-current="page"' : '' ?>><?= e($cat['name']) ?></a></li>
        <?php endforeach; ?>
        <li class="nav-more" id="navMore" hidden>
          <button type="button" class="nav-more-toggle" id="navMoreToggle" aria-haspopup="true" aria-expanded="false">Other <span aria-hidden="true">&#9662;</span></button>
          <ul class="nav-more-menu" id="navMoreMenu"></ul>
        </li>
      </ul>
      <div class="nav-search">
        <button type="button" class="nav-search-toggle" id="navSearchToggle" aria-haspopup="true" aria-expanded="false" aria-label="Search">&#128269;</button>
        <form class="nav-search-form" id="navSearchForm" action="/search/" method="get" role="search">
          <input type="search" name="q" placeholder="Search Yoyoguide..." maxlength="80" aria-label="Search articles" value="<?= isset($_GET['q']) ? e_attr($_GET['q']) : '' ?>">
          <button type="submit" aria-label="Submit search">Go</button>
        </form>
      </div>
    </div>
  </nav>
</header>

<div class="travel-marquee" aria-label="News updates">
  <div class="wrap travel-marquee-inner">
    <span class="marquee-badge">Flash News</span>
    <div class="marquee-window">
      <div class="marquee-track">
        <?php for ($loop = 0; $loop < 2; $loop++): ?>
          <?php foreach ($marqueeItems as $item): ?>
            <span class="marquee-item"><?= e($item) ?></span>
          <?php endforeach; ?>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</div>

<main id="main-content">
