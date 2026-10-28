(function () {
  'use strict';
  var saved = null;
  try { saved = localStorage.getItem('hits9-theme-v2'); } catch (_) {}
  var dark = saved !== 'light';
  document.documentElement.dataset.theme = dark ? 'dark' : 'light';
  document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
})();
