# Datasheet PDF Generator

Generates branded PDF datasheets for WooCommerce products. Supports manual generation, live preview, and a product-page download tab.

---

## Requirements

| Dependency | Version |
|---|---|
| WordPress | 5.8+ |
| WooCommerce | 6.0+ |
| PHP | 7.4+ |

No ACF or Composer required — all dependencies are bundled.

---

## Installation

1. Download the plugin ZIP.
2. In WordPress admin go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP and click **Activate**.

That's it. No terminal, no Composer, no extra setup.

---

## Settings

Go to **Datasheet PDF** in the WordPress admin sidebar.

**Branding**

| Field | Description |
|---|---|
| Header Image | Image printed at the top of every PDF page. Select from the media library. |
| Footer Image | Image printed at the bottom of every PDF page. Select from the media library. |

Header and footer images span the full A4 page width (no side margins). Recommended pixel dimensions:

| | 150 dpi *(digital PDF)* | 300 dpi *(print-ready, recommended)* |
|---|---|---|
| **Header** (40 mm tall) | 1240 × 236 px | 2480 × 472 px |
| **Footer** (20 mm tall) | 1240 × 118 px | 2480 × 236 px |

**Product Page Tab**

| Field | Description |
|---|---|
| Show PDF Tab by Default | Whether the **Product Datasheet** download tab is shown on product pages by default. Can be overridden per product. |

---

## Product Metabox

Open any WooCommerce product in the admin editor. The **PDF Datasheet** metabox appears in the main content area.

| Control | Description |
|---|---|
| **PDF Content** | TinyMCE editor for the left-column content of the PDF. |
| **Import Product Description** | Copies the WooCommerce product description into the PDF content editor. |
| **Show PDF tab** | Toggle whether the **Product Datasheet** download tab appears on this product's page. Defaults to the global setting. |
| **Preview PDF** | Generates the PDF in memory and opens it in a new browser tab. Does not save anything. |
| **Generate PDF** | Builds and saves the PDF to disk and shows the file link. |
| **Current PDF** | Link to the last generated PDF file. |

---

## PDF Layout

Generated PDFs are A4 portrait with a two-column layout and 10 mm left/right content padding.

### Columns

| Column | Width | Content |
|---|---|---|
| Left | 52% | PDF Content editor text, preceded by the product title |
| Right | 42% | Product image, then SKU / Part # table |

For **variable products** the SKU table lists every variation with its SKU and attribute values.
For **simple products** only the single SKU row is shown.

### Header & Footer

- **Header** — branding header image, full page width.
- **Footer (text row)** — page number (`Page X of Y`) bottom-right, aligned with the content edge.
- **Footer (image row)** — branding footer image, full page width.



## Product Page Tab

When a PDF has been generated for a product, a **Product Datasheet** tab appears on the product page after the **Additional Information** tab. It contains a single download button.

Visibility is controlled at two levels:

1. **Global default** — **Datasheet PDF → General → Show PDF Tab by Default**.
2. **Per-product override** — the **Show PDF tab** checkbox in the product metabox.

The per-product setting always takes precedence. If it has never been saved for a product, the global default is used.

---

## File Storage

```
wp-content/uploads/datasheets/product-{ID}-{timestamp}.pdf
```

- A new file is created on every generation (timestamp in the name prevents CDN caching stale files).
- Old files are **not** automatically deleted.
- The active file's relative path is stored in post meta.

---

## Post Meta Keys

| Key | Description |
|---|---|
| `cordy_pdf_html_content` | Saved PDF editor HTML |
| `cordy_pdf_path` | Relative path to the current PDF (e.g. `datasheets/product-123-1700000000.pdf`) |
| `cordy_pdf_show_tab` | Per-product tab visibility (`1` = show, `0` = hide, empty = use global default) |

---

## ERP Attribute Handling

The plugin corrects two common artefacts introduced when an ERP syncs product attributes via WooCommerce:

- **Month abbreviations** — size slug `04-jun` is normalised to `04-06`.
- **Missing leading zeros** — `4-06` is zero-padded to `04-06`.

Term names are resolved from WooCommerce taxonomy slugs, preserving the original casing and formatting.

---

## Building from Source

The `vendor/` directory is not committed to the repository. Run `composer install` once before building, or ensure composer is available so the build script installs dependencies automatically.

### Requirements

`bash`, `rsync`, `zip` — available by default on macOS and most Linux systems.
`composer` — required to install mPDF. Either run `composer install` in the repo beforehand, or have it available on `$PATH` so the build script runs it automatically.

### Usage

```bash
./build.sh free    # → dist/datasheet-pdf-generator.<version>.zip
```

### What the build does

| Step | Description |
|---|---|
| Copy files via rsync (respects `.distignore`) | ✓ |
| `composer install --no-dev --optimize-autoloader` | ✓ |
| Package as ZIP in `dist/` | ✓ |

---

## File Structure

```
datasheet-pdf-generator/
├── datasheet-pdf-generator.php              # Plugin entry point
├── composer.json                            # mPDF dependency declaration
├── vendor/                                  # Composer packages — gitignored, run composer install
├── build.sh                                 # Distribution build script
├── .distignore                              # Files excluded from built ZIPs
├── fonts/
│   ├── Inter-Regular.ttf
│   └── Inter-Bold.ttf
├── includes/
│   ├── class-cordy-settings.php             # Admin settings page
│   ├── class-cordy-pdf-generator.php        # Core PDF engine: generate, preview, layout
│   ├── class-datasheet-pdf-generator.php    # Product metabox, AJAX handlers, script enqueue
│   ├── class-cordy-tab.php                  # Frontend product page download tab
│   └── pdf-redirect.php                     # ?type=pdf URL redirect
└── admin/
    ├── js/
    │   ├── admin-scripts.js                 # Metabox JS (preview, generate, import)
    │   └── settings.js                      # Settings page JS (media library uploader)
    └── css/
        ├── style.css                        # Metabox styles
        ├── header.png                       # Fallback header image
        └── footer.png                       # Fallback footer image
```
