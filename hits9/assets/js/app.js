(function () {
  'use strict';
  var button = document.getElementById('themeToggle');
  function setTheme(theme) {
    var dark = theme === 'dark';
    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;
    if (button) {
      button.setAttribute('aria-checked', String(dark));
      button.setAttribute('aria-label', dark ? 'Use light mode' : 'Use dark mode');
      button.title = dark ? 'Use light mode' : 'Use dark mode';
    }
  }
  setTheme(document.documentElement.dataset.theme || 'dark');
  if (button) button.addEventListener('click', function () {
    var theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    setTheme(theme);
    try { localStorage.setItem('hits9-theme-v2', theme); } catch (_) {}
  });
})();

(function () {
  "use strict";
  function imageFallback(img) {
    if (img.matches('.hero-thumb img, .post-card-thumb img, .article-featured, .recent-post img') && !img.dataset.fallback) {
      img.dataset.fallback = 'true';
      img.src = '/assets/images/story-placeholder.svg';
    }
  }
  document.addEventListener('error', function (event) {
    if (event.target.tagName === 'IMG') imageFallback(event.target);
  }, true);
  document.querySelectorAll('img').forEach(function (img) {
    if (img.complete && img.naturalWidth === 0) imageFallback(img);
  });
  var toggle = document.getElementById("navToggle");
  var nav = document.getElementById("siteNav");
  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      var isOpen = nav.classList.toggle("open");
      toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
  }
})();

/**
 * Priority+ style overflow nav: category links that don't fit on one line
 * are moved into an "Other" dropdown instead of wrapping, so the header
 * stays a single tidy row no matter how many categories exist.
 */
(function () {
  "use strict";
  var navList = document.getElementById("primaryNavList");
  var moreItem = document.getElementById("navMore");
  var moreToggle = document.getElementById("navMoreToggle");
  var moreMenu = document.getElementById("navMoreMenu");
  if (!navList || !moreItem || !moreToggle || !moreMenu) return;

  var items = Array.prototype.slice.call(navList.children).filter(function (li) {
    return li !== moreItem;
  });
  var desktopQuery = window.matchMedia("(min-width: 701px)");

  function closeMenu() {
    moreItem.classList.remove("open");
    moreMenu.classList.remove("open");
    moreToggle.setAttribute("aria-expanded", "false");
  }

  function layout() {
    // Reset to the original order before measuring, so this is idempotent.
    items.forEach(function (li) {
      navList.insertBefore(li, moreItem);
    });
    moreMenu.innerHTML = "";
    moreItem.hidden = true;
    closeMenu();

    if (!desktopQuery.matches) return; // mobile nav stacks vertically, no overflow handling needed

    if (navList.scrollWidth <= navList.clientWidth) return; // everything already fits

    moreItem.hidden = false;
    var available = navList.clientWidth;

    for (var i = items.length - 1; i >= 1; i--) { // keep "Home" (index 0) pinned
      if (navList.scrollWidth <= available) break;
      moreMenu.insertBefore(items[i], moreMenu.firstChild);
    }

    if (!moreMenu.children.length) moreItem.hidden = true;
  }

  moreToggle.addEventListener("click", function () {
    var isOpen = moreItem.classList.toggle("open");
    moreMenu.classList.toggle("open", isOpen);
    moreToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
  });
  document.addEventListener("click", function (event) {
    if (!moreItem.contains(event.target) && !moreMenu.contains(event.target)) closeMenu();
  });
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") closeMenu();
  });

  var resizeTimer;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(layout, 120);
  });

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(layout);
  }
  layout();
})();

(function () {
  'use strict';
  var header = document.querySelector('.site-header');
  if (!header || !document.querySelector('.article-latest')) return;
  function measureHeader() {
    document.documentElement.style.setProperty('--sticky-header-height', header.getBoundingClientRect().height + 'px');
  }
  measureHeader();
  if (window.ResizeObserver) {
    new ResizeObserver(measureHeader).observe(header);
  } else {
    window.addEventListener('resize', measureHeader);
  }
})();


/** Homepage promotional carousel: automatic rotation, controls and touch swipe. */
(function () {
  "use strict";
  var carousel = document.querySelector("[data-carousel]");
  if (!carousel) return;
  var slides = Array.prototype.slice.call(carousel.querySelectorAll(".homepage-carousel__slide"));
  var dots = Array.prototype.slice.call(carousel.querySelectorAll("[data-carousel-dot]"));
  var previous = carousel.querySelector("[data-carousel-prev]");
  var next = carousel.querySelector("[data-carousel-next]");
  var current = 0, timer, touchStart = 0;
  if (slides.length < 2) return;
  function show(index) {
    current = (index + slides.length) % slides.length;
    slides.forEach(function (slide, i) {
      var active = i === current;
      slide.classList.toggle("is-active", active);
      slide.setAttribute("aria-hidden", active ? "false" : "true");
      dots[i].setAttribute("aria-selected", active ? "true" : "false");
    });
  }
  function restart() { clearInterval(timer); timer = setInterval(function () { show(current + 1); }, 5500); }
  if (previous) previous.addEventListener("click", function () { show(current - 1); restart(); });
  if (next) next.addEventListener("click", function () { show(current + 1); restart(); });
  dots.forEach(function (dot, i) { dot.addEventListener("click", function () { show(i); restart(); }); });
  carousel.addEventListener("mouseenter", function () { clearInterval(timer); });
  carousel.addEventListener("mouseleave", restart);
  carousel.addEventListener("touchstart", function (event) { touchStart = event.changedTouches[0].screenX; }, { passive: true });
  carousel.addEventListener("touchend", function (event) {
    var distance = event.changedTouches[0].screenX - touchStart;
    if (Math.abs(distance) > 45) { show(current + (distance < 0 ? 1 : -1)); restart(); }
  }, { passive: true });
  restart();
})();

