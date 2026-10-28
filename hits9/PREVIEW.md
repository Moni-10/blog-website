Local preview: http://localhost:8100

Run `powershell -ExecutionPolicy Bypass -File tools/start-preview.ps1` from this directory when the preview is stopped. Requires PHP (the script detects XAMPP PHP).

The preview uses the original live API and original cache behavior. Articles, categories, search results, and website information come from the existing backend. There is no demo API or fixed topic list. Set SITE_DOMAIN to your website record's domain if it differs from hits9.com. BLOG_API_BASE_URL remains configurable as before.

The header theme button switches between light and dark, remembers the choice, and defaults to dark before a choice is made. Unavailable remote images display a local illustration while retaining the article's alt text.

Header navigation includes all requested topics and any additional backend categories. Links that fit stay in the row; the rest move into the Other dropdown. Mobile navigation displays all topics. The footer retains five category links and a short description below its logo; the discovery banner has been removed. Existing categories use their actual backend slugs; topics not yet configured in the backend open live site search. Article content and recent posts remain dynamic. Desktop category grids have three columns, tablet grids have two, and small phone grids have one. The header stays sticky. The brand favicon is used when the backend has no custom favicon. Both themes retain blue and green accents; logo backgrounds appear only in dark mode.
