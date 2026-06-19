# wp-es-pricing-pages

WordPress plugin for EmptySpace Technology interactive pricing pages. One plugin, configured per site — used on both StageStock and VirtualCallboard.

---

## Plugin: ES Pricing Tables (`es-pricing`)

Interactive pricing table with:

- **Monthly / Annual billing toggle** with savings badge
- **"I'm a…" discount dropdown** — previews discounted prices live (actual discount applied via Stripe coupon at signup)
- **3×2 desktop / 2×3 tablet / 1×6 mobile** card grid, with Enterprise spanning full width at the bottom
- Single **Sign Up Now** CTA that opens the signup page in a **Magnific Popup iframe** (auto-falls back to FancyBox if Magnific is unavailable)
- All content editable via the WordPress admin settings page — no code changes needed for each site

The same zip installs on both StageStock and VirtualCallboard. Each site is configured independently via **Settings → ES Pricing** to reflect its own plan names, prices, and wording (e.g. "items" vs. "productions").

---

## Installation

### Option A — GitHub Release (recommended)

1. Go to the [Releases](../../releases) page and download `es-pricing.zip` from the latest release.
2. In WordPress Admin → **Plugins → Add New → Upload Plugin**, upload the zip.
3. Activate **ES Pricing Tables**.

### Option B — Manual build

```bash
git clone https://github.com/EmptySpace-Technology-LLC/wp-es-pricing-pages.git
cd wp-es-pricing-pages
zip -r es-pricing.zip es-pricing/
```

Upload the resulting `es-pricing.zip` via WordPress.

---

## Usage

Add the shortcode to any page or WP Bakery Raw HTML element:

```
[es_pricing]
```

---

## Settings

Go to **WordPress Admin → Settings → ES Pricing** to configure the table for this site:

| Section | Editable fields |
|---|---|
| **Plans** | Name, tagline, monthly price, annual price/month, annual total, item limit, features (one per line) |
| **Discount Options** | Dropdown label, discount %, and the note shown when selected |
| **CTA & General** | Sign-up URL, button text, button sub-text, annual badge label ("Save 10%") |

The **Free** and **Enterprise** plan slots have no price fields — Free is always $0 and Enterprise displays "Contact Us." Their names, taglines, and features are still editable.

**Per-site examples:**
- StageStock: item limit = "Up to 500 items", CTA URL = stagestock.com signup
- VirtualCallboard: item limit = "Up to 3 productions", CTA URL = virtualcallboard.com signup

---

## Releasing a new version

1. Update the `Version:` field in `es-pricing/es-pricing.php`.
2. Commit and push.
3. Tag the commit and push the tag:

```bash
git tag v1.1.0
git push origin v1.1.0
```

GitHub Actions will build `es-pricing.zip` and attach it to a new GitHub Release automatically. The zip is always named `es-pricing.zip` (no version suffix) so WordPress sites can update by re-uploading without reconfiguring anything.

---

## Development

The plugin has no build step and no npm/composer dependencies. Edit files directly:

```
es-pricing/
├── es-pricing.php       ← plugin registration, shortcode, admin settings page
└── assets/
    ├── pricing.css      ← front-end styles (.es-* scoped, no theme conflicts)
    ├── pricing.js       ← toggle, discount, card render, modal init
    └── admin.css        ← WP admin settings page styles
```

**Plan data flow:** PHP reads settings from the WordPress options table (`es_pricing_v1`) and passes plan data to JavaScript via `wp_localize_script` as `window.esPricingData.plans`. The discount dropdown is rendered by PHP; JS reads the selected value from the DOM.

**Modal:** `pricing.js` auto-detects Magnific Popup (bundled in the Agile theme) then falls back to FancyBox. No configuration needed.
