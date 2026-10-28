# Deployment Checklist

Everything below is currently set to a **local/demo value**. Update each before this goes live.

## 1. Branding

The site name, tagline and favicon are **not hardcoded** — they're pulled live from this site's **Website record** in the Admin Dashboard (`name`, `description`, `faviconUrl` fields), the same way blog categories are data-driven. To rebrand this site, edit that Website record in the dashboard — no code changes needed.

`SITE_NAME_FALLBACK` in `includes/config.php` is only used if the API is unreachable — it's not the real site name.

If the Website record has no `faviconUrl` set, `assets/favicon.svg` (a generic placeholder) is used instead — upload a real favicon via the dashboard's Website settings to replace it.

## 2. Backend API (`backend/.env`)

| Variable | Current (local dev) | Action |
|---|---|---|
| `MONGODB_URI` | `mongodb://127.0.0.1:27017/mmw_admin_dev` (local Mongo) | Point to your real production database. **If you ever go back to the original Atlas URI, rotate its password first** — it was pasted in plaintext into this chat earlier in the session and should be treated as compromised. |
| `ADMIN_API_KEY` | `admin` | Set a long, random secret (e.g. 32+ random chars). This key protects every create/update/delete/upload admin action. |
| `CORS_ORIGINS` | `http://localhost:3000` | Set to your real admin panel origin(s), comma-separated. |
| `PUBLIC_BASE_URL` | `http://localhost:5014` | Set to the real public HTTPS URL of this API (used to build uploaded-image URLs). |
| `NODE_ENV` | `development` | Set to `production`. |

Also:
- In MongoDB Atlas (or wherever it's hosted): restrict Network Access to your server's IP only — don't leave it open to `0.0.0.0/0`.
- Confirm `GET /health` returns `"database":"connected"` after deploy.

## 3. Website record (Admin Dashboard → Websites)

The public site looks up content by matching the **exact domain** of the incoming request against a Website record's `domain` field.

- Current seeded domain: `mohindramachine.tech` — replace with (or add) the **real production domain** this site will serve, e.g. `abhyudaytimes.com` or whatever the actual domain is.
- Make sure `active` is `true` for that website.
- Fill in the Website's SEO tab (Admin → SEO page): site title, meta description, OG image, Google/Bing verification, GA/GTM IDs — these are currently all empty.

## 4. Public site config (`public-site/includes/config.php`)

| Constant | Current | Action |
|---|---|---|
| `BLOG_API_BASE_URL` | reads `getenv('BLOG_API_BASE_URL')`, falls back to `http://localhost:5014` | Set the `BLOG_API_BASE_URL` environment variable on the real host to the real, HTTPS backend API URL. Never leave it pointing at localhost in production. |
| `SITE_DOMAIN` | reads `getenv('SITE_DOMAIN')`, falls back to the incoming `Host` header | Usually fine to leave auto-detected, but set `SITE_DOMAIN` explicitly if this server ever sits behind a proxy/CDN that changes the `Host` header. |

## 5. Web server setup

- Document root → `public-site/` (not the repo root).
- Enable `mod_rewrite` and `mod_headers` (both required — `.htaccess` uses both).
- Confirm HTTPS is actually terminated here (the `.htaccess` redirects HTTP→HTTPS, but only works if TLS is configured on the server/load balancer).
- PHP requirements: PHP 8+, `curl` extension enabled (falls back to `file_get_contents` if not, but curl is preferred), `dom`/`libxml` extensions enabled (used by the content sanitizer — these ship with PHP by default).
- Make `public-site/cache/` and `public-site/logs/` writable by the web server user.
- `public-site/router.php` is **dev-only** (for `php -S`) — Apache ignores it, but you can delete it from the production copy if you want a cleaner tree.

## 6. Content

- The 13 seeded blogs and category list are **demo content** — replace/remove them from the Admin Dashboard before launch (Blog Library → Delete, or just leave them and add real posts alongside).
- Blog images currently use `picsum.photos` placeholder URLs — re-upload real featured images per post via the Admin Dashboard's blog editor.

## 7. Final smoke test after deploy

- [ ] `https://yourdomain.com/` loads and shows real posts
- [ ] `https://yourdomain.com/category/<slug>/` works for every category
- [ ] `https://yourdomain.com/blog/<slug>/` works and shows correct SEO tags (view source)
- [ ] `https://yourdomain.com/sitemap.xml` and `/robots.txt` load
- [ ] `https://yourdomain.com/includes/config.php` returns 403 (must never be reachable)
- [ ] Security headers present (check via browser devtools → Network → Response Headers, or securityheaders.com)
