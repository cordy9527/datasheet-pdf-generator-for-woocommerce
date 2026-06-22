=== Datasheet PDF Generator for WooCommerce ===
Contributors: agentcordy
Tags: woocommerce, pdf, datasheet, product, generator
Requires at least: 5.8
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate branded PDF datasheets for WooCommerce products with custom branding, live preview, and downloadable product tabs.

== Description ==

Datasheet PDF Generator for WooCommerce creates professional, branded PDF datasheets for your WooCommerce products. Perfect for B2B stores, technical products, or any business that needs printable product documentation.

= Key Features =

* **Manual PDF Generation** - Generate PDFs on-demand from the product editor
* **Live Preview** - Preview PDFs before saving them
* **Custom Branding** - Add your own header and footer images
* **Product Page Tab** - Automatic download tab on product pages
* **Two-Column Layout** - Professional A4 portrait layout with product details
* **Variable Product Support** - Automatically includes all variations with SKUs
* **Easy Content Editor** - Use the familiar WordPress editor for PDF content

= How It Works =

1. Edit any WooCommerce product
2. Use the PDF Datasheet metabox to add content
3. Click "Generate PDF" to create your datasheet
4. A download tab automatically appears on the product page

= PDF Layout =

Generated PDFs feature:
* Custom header and footer images (full-width branding)
* Two-column layout with product image and details
* SKU/Part number table (includes all variations for variable products)
* Professional A4 portrait format
* Page numbers



= Requirements =

* WordPress 5.8 or higher
* WooCommerce 6.0 or higher
* PHP 7.4 or higher

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Go to Plugins → Add New
3. Search for "Datasheet PDF Generator"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Go to Plugins → Add New → Upload Plugin
3. Choose the ZIP file and click "Install Now"
4. Click "Activate Plugin"

= After Activation =

1. Go to **Datasheet PDF** in the admin menu
2. Upload your header and footer images (optional)
3. Configure default settings
4. Edit any WooCommerce product to generate your first PDF

== Frequently Asked Questions ==

= Do I need to install Composer or any dependencies? =

No. All dependencies are bundled with the plugin. Just install and activate.

= What image sizes should I use for headers and footers? =

For best quality (300 dpi print-ready):
* Header: 2480 × 472 px
* Footer: 2480 × 236 px

For digital PDFs (150 dpi):
* Header: 1240 × 236 px
* Footer: 1240 × 118 px

= Can I customize the PDF content? =

Yes. Each product has a PDF content editor where you can add custom text, images, and formatting using the WordPress editor.

= Does it work with variable products? =

Yes. The plugin automatically generates a table with all variations, their SKUs, and attribute values.

= Where are the PDF files stored? =

PDFs are stored in `wp-content/uploads/datasheets/` with unique filenames to prevent caching issues.

= Can I preview before generating? =

Yes. Click the "Preview PDF" button to see what the PDF will look like without saving it.

= How do I hide the download tab on specific products? =

In the product editor, uncheck "Show PDF tab" in the PDF Datasheet metabox.



== Screenshots ==

1. PDF Datasheet metabox in the product editor
2. Settings page with branding options
3. Generated PDF example with two-column layout
4. Product page download tab
5. Live PDF preview

== Changelog ==

= 1.0.0 =
* Initial release
* Manual PDF generation and preview
* Custom header and footer images
* Product page download tab
* Two-column A4 layout
* Variable product support
* Per-product tab visibility control

== Upgrade Notice ==

= 1.0.0 =
Initial release of Datasheet PDF Generator.
