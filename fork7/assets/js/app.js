(function () {
  "use strict";
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
 * Footer "Explore topics": show the first 5 categories, with a
 * Read More / Read Less toggle for the rest. Without JS all links stay visible.
 */
(function () {
  "use strict";
  var button = document.getElementById("footerTopicsToggle");
  var nav = document.getElementById("footerTopicsNav");
  if (!button || !nav) return;
  var extras = Array.prototype.slice.call(nav.querySelectorAll("[data-topic-extra]"));
  if (!extras.length) return;
  function setExpanded(expanded) {
    extras.forEach(function (link) { link.hidden = !expanded; });
    button.textContent = expanded ? "Read Less" : "Read More";
    button.setAttribute("aria-expanded", expanded ? "true" : "false");
  }
  button.addEventListener("click", function () {
    setExpanded(button.getAttribute("aria-expanded") !== "true");
  });
  setExpanded(false);
  button.hidden = false;
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

/**
 * Back-to-top button: appears after scrolling past one viewport height,
 * scrolls smoothly to the top on click.
 */
(function () {
  "use strict";
  var button = document.getElementById("backToTop");
  if (!button) return;

  var toggleVisibility = function () {
    button.classList.toggle("visible", window.scrollY > window.innerHeight * 0.6);
  };
  window.addEventListener("scroll", toggleVisibility, { passive: true });
  toggleVisibility();

  button.addEventListener("click", function () {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
})();

/**
 * Nav search toggle: click to open a small dropdown search form, focus
 * the input, close on outside click or Escape.
 */
(function () {
  "use strict";
  var toggle = document.getElementById("navSearchToggle");
  var form = document.getElementById("navSearchForm");
  if (!toggle || !form) return;

  function close() {
    form.classList.remove("open");
    toggle.setAttribute("aria-expanded", "false");
  }

  toggle.addEventListener("click", function (event) {
    event.stopPropagation();
    var isOpen = form.classList.toggle("open");
    toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    if (isOpen) {
      var input = form.querySelector("input");
      if (input) input.focus();
    }
  });
  document.addEventListener("click", function (event) {
    if (!form.contains(event.target) && event.target !== toggle) close();
  });
  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") close();
  });
})();

/**
 * Trending headline ticker: shows one headline at a time with prev/next
 * controls to cycle through the latest posts.
 */
(function () {
  "use strict";
  var track = document.getElementById("trendingTrack");
  var prevBtn = document.getElementById("trendPrev");
  var nextBtn = document.getElementById("trendNext");
  if (!track) return;

  var items = Array.prototype.slice.call(track.querySelectorAll(".trending-item"));
  if (items.length < 2) return;

  var index = items.findIndex(function (el) { return el.classList.contains("active"); });
  if (index < 0) index = 0;

  function show(newIndex) {
    items[index].classList.remove("active");
    index = (newIndex + items.length) % items.length;
    items[index].classList.add("active");
  }

  if (prevBtn) prevBtn.addEventListener("click", function () { show(index - 1); });
  if (nextBtn) nextBtn.addEventListener("click", function () { show(index + 1); });
})();

/**
 * Dark/light mode toggle: persists choice in localStorage and applies it
 * via a data-theme attribute on <html>, read synchronously in <head> to
 * avoid a flash of the wrong theme on load.
 */
(function () {
  "use strict";
  var toggle = document.getElementById("themeToggle");
  if (!toggle) return;
  var root = document.documentElement;

  function isDark() {
    return root.getAttribute("data-theme") === "dark";
  }
  function sync() {
    toggle.setAttribute("aria-checked", isDark() ? "true" : "false");
    toggle.setAttribute("aria-label", isDark() ? "Use light mode" : "Use dark mode");
  }
  sync();

  toggle.addEventListener("click", function () {
    var dark = !isDark();
    if (dark) {
      root.setAttribute("data-theme", "dark");
    } else {
      root.removeAttribute("data-theme");
    }
    try { localStorage.setItem("fork7-theme", dark ? "dark" : "light"); } catch (e) {}
    sync();
  });
})();

/**
 * Newsletter demo form: keep it client-side until a real mailing provider is
 * connected, without using inline event handlers that would weaken CSP.
 */
(function () {
  "use strict";
  var forms = document.querySelectorAll(".newsletter-form");
  if (!forms.length) return;

  Array.prototype.forEach.call(forms, function (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();
    });
  });
})();

(function () {
  var content = document.querySelector('.article-content'), toc = document.getElementById('articleToc');
  if (content && toc) {
    content.querySelectorAll('h2, h3').forEach(function(heading, i) {
      if (!heading.id) heading.id = 'story-section-' + i;
      var li = document.createElement('li'), a = document.createElement('a');
      a.href = '#' + heading.id; a.textContent = heading.textContent; li.appendChild(a); toc.querySelector('ol').appendChild(li);
    });
    toc.hidden = !toc.querySelector('li');
  }
  var progress = document.getElementById('readingProgress');
  if (progress && content) {
    function update() { var top = content.getBoundingClientRect().top + window.scrollY; var total = Math.max(1, content.offsetHeight - window.innerHeight); progress.style.width = Math.max(0, Math.min(100, (window.scrollY - top) / total * 100)) + '%'; }
    window.addEventListener('scroll', update, {passive:true}); window.addEventListener('resize', update); update();
  }
  var share = document.getElementById('shareStory'), status = document.getElementById('shareStatus');
  if (share) share.addEventListener('click', async function() {
    try { if (navigator.share) await navigator.share({title:document.title,url:location.href}); else if (navigator.clipboard) { await navigator.clipboard.writeText(location.href); status.textContent = 'Link copied'; } else status.textContent = 'Copy the link from your address bar'; }
    catch(e) { if (e.name !== 'AbortError') status.textContent = 'Copy the link from your address bar'; }
  });
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

