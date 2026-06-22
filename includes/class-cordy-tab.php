<?php

class Cordy_Tab
{
    const META_SHOW_TAB = "cordy_pdf_show_tab";

    public function __construct()
    {
        add_filter("woocommerce_product_tabs", [$this, "register_tab"]);
    }

    /**
     * Add the PDF download tab after Additional Information (priority 20).
     */
    public function register_tab($tabs)
    {
        $product_id = get_the_ID();
        if (!$product_id) {
            return $tabs;
        }

        // No PDF generated yet — nothing to link to.
        $pdf_path = get_post_meta(
            $product_id,
            Cordy_PDF_Generator::PATH_META_KEY,
            true,
        );
        if (!$pdf_path) {
            return $tabs;
        }

        // Per-product override; fall back to the global default when not yet set.
        $meta = get_post_meta($product_id, self::META_SHOW_TAB, true);
        $show =
            $meta !== ""
                ? (bool) $meta
                : (bool) get_option(Cordy_Settings::OPTION_TAB_DEFAULT, 1);

        if (!$show) {
            return $tabs;
        }

        $tabs["cordy_pdf"] = [
            "title" => __(
                "Product Datasheet",
                "datasheet-pdf-generator-for-woocommerce",
            ),
            "priority" => 25,
            "callback" => [$this, "render_tab"],
        ];

        return $tabs;
    }

    /**
     * Render the tab content — a single download button.
     */
    public function render_tab()
    {
        $product_id = get_the_ID();
        $pdf_path = get_post_meta(
            $product_id,
            Cordy_PDF_Generator::PATH_META_KEY,
            true,
        );
        if (!$pdf_path) {
            return;
        }

        $upload = wp_upload_dir();
        $pdf_url = trailingslashit($upload["baseurl"]) . $pdf_path;
        ?>
        <div class="cordy-pdf-tab-content">
            <div class="wp-block-button is-style-fill">
                <a href="<?php echo esc_url($pdf_url); ?>"
                   class="wp-block-button__link wp-element-button"
                   download>
                    <?php esc_html_e(
                        "Download Product Datasheet",
                        "datasheet-pdf-generator-for-woocommerce",
                    ); ?>
                </a>
            </div>
        </div>
        <?php
    }
}
