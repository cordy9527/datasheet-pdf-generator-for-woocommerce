<?php

class Datasheet_PDF_Generator
{
    private $plugin_file;

    public function __construct($plugin_file)
    {
        $this->plugin_file = $plugin_file;
        $this->run();
    }

    public function run()
    {
        add_action("admin_enqueue_scripts", [$this, "enqueue_admin_scripts"]);
        add_action("add_meta_boxes", [$this, "register_metabox"]);
        add_action("save_post_product", [$this, "save_pdf_meta"], 10, 2);
        add_action("wp_ajax_cordy_preview_pdf", [
            $this,
            "preview_pdf_ajax_handler",
        ]);
        add_action("wp_ajax_cordy_generate_pdf", [
            $this,
            "generate_pdf_ajax_handler",
        ]);
        add_action("admin_footer", [$this, "add_spinning_cog_html"]);
    }

    public function enqueue_admin_scripts()
    {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== "product") {
            return;
        }

        wp_enqueue_script(
            "datasheet-pdf-generator-admin-script",
            plugin_dir_url($this->plugin_file) . "admin/js/admin-scripts.js",
            ["jquery"],
            "1.13",
            true,
        );

        wp_localize_script("datasheet-pdf-generator-admin-script", "cordyPdf", [
            "ajaxurl" => admin_url("admin-ajax.php"),
            "nonce" => wp_create_nonce("cordy-pdf-nonce"),
        ]);

        wp_enqueue_style(
            "datasheet-pdf-generator-admin-style",
            plugin_dir_url($this->plugin_file) . "admin/css/style.css",
            [],
            "1.13",
        );
    }

    public function register_metabox()
    {
        add_meta_box(
            "cordy-pdf-datasheet",
            "PDF Datasheet",
            [$this, "render_metabox"],
            "product",
            "normal",
            "default",
        );
    }

    public function render_metabox($post)
    {
        wp_nonce_field("cordy_pdf_meta_save", "cordy_pdf_meta_nonce");

        $saved_content = get_post_meta(
            $post->ID,
            Cordy_PDF_Generator::HTML_META_KEY,
            true,
        );
        $current_pdf = get_post_meta(
            $post->ID,
            Cordy_PDF_Generator::PATH_META_KEY,
            true,
        );

        // Per-product tab toggle: fall back to global default when not yet set.
        $tab_meta = get_post_meta($post->ID, Cordy_Tab::META_SHOW_TAB, true);
        $show_tab =
            $tab_meta !== ""
                ? (bool) $tab_meta
                : (bool) get_option(Cordy_Settings::OPTION_TAB_DEFAULT, 1);

        echo '<div class="cordy-pdf-metabox">';

        // Content editor
        echo "<p><strong>PDF Content</strong></p>";
        wp_editor($saved_content ?: "", "cordy_pdf_content", [
            "textarea_name" => "cordy_pdf_content",
            "media_buttons" => false,
            "textarea_rows" => 12,
            "teeny" => false,
        ]);

        // Tab visibility toggle
        echo '<p style="margin-top:12px;">';
        echo '<label><input type="checkbox" name="cordy_pdf_show_tab" value="1" ' .
            checked(true, $show_tab, false) .
            " /> Show <strong>Product Datasheet</strong> tab on the product page</label>";
        echo "</p>";

        // Buttons
        echo '<div class="cordy-pdf-buttons">';
        echo '<button type="button" id="cordy-import-btn" class="button">Import Product Description</button>';
        echo '<button type="button" id="cordy-preview-btn" class="button">Preview PDF</button>';
        echo '<button type="button" id="cordy-generate-btn" class="button button-primary">Generate PDF</button>';
        echo "</div>";

        // Current PDF status
        if ($current_pdf) {
            $upload = wp_upload_dir();
            $pdf_url = trailingslashit($upload["baseurl"]) . $current_pdf;
            echo '<p class="cordy-pdf-status">Current PDF: <a href="' .
                esc_url($pdf_url) .
                '" target="_blank">' .
                esc_html(basename($current_pdf)) .
                "</a></p>";
        }

        echo "</div>";
    }

    public function save_pdf_meta($post_id, $post)
    {
        $nonce = isset($_POST["cordy_pdf_meta_nonce"])
            ? sanitize_text_field(wp_unslash($_POST["cordy_pdf_meta_nonce"]))
            : "";

        if (!wp_verify_nonce($nonce, "cordy_pdf_meta_save")) {
            return;
        }
        if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
            return;
        }
        if (isset($_POST["cordy_pdf_content"])) {
            update_post_meta(
                $post_id,
                Cordy_PDF_Generator::HTML_META_KEY,
                wp_kses_post(wp_unslash($_POST["cordy_pdf_content"])),
            );
        }
        // Save the tab visibility toggle (unchecked = 0, checked = 1).
        update_post_meta(
            $post_id,
            Cordy_Tab::META_SHOW_TAB,
            isset($_POST["cordy_pdf_show_tab"]) ? 1 : 0,
        );
    }

    public function add_spinning_cog_html()
    {
        // Removed - no longer using spinning loader
    }

    public function preview_pdf_ajax_handler()
    {
        check_ajax_referer("cordy-pdf-nonce", "nonce");

        $html = isset($_POST["pdfContent"])
            ? wp_kses_post(wp_unslash($_POST["pdfContent"]))
            : "";
        $post_id = isset($_POST["post_ID"]) ? absint($_POST["post_ID"]) : 0;
        $title = isset($_POST["pdfTitle"])
            ? sanitize_text_field(wp_unslash($_POST["pdfTitle"]))
            : "";

        if (!strlen($html)) {
            wp_send_json_error("No content provided");
        }

        try {
            $pdf_string = Cordy_PDF_Generator::preview($post_id, $html, $title);
            wp_send_json_success(["pdf" => base64_encode($pdf_string)]);
        } catch (Exception $e) {
            wp_send_json_error("Error generating PDF: " . $e->getMessage());
        }

        exit();
    }

    public function generate_pdf_ajax_handler()
    {
        check_ajax_referer("cordy-pdf-nonce", "nonce");

        $html = isset($_POST["pdfContent"])
            ? wp_kses_post(wp_unslash($_POST["pdfContent"]))
            : "";
        $post_id = isset($_POST["post_ID"]) ? absint($_POST["post_ID"]) : 0;
        $title = isset($_POST["pdfTitle"])
            ? sanitize_text_field(wp_unslash($_POST["pdfTitle"]))
            : "";

        if (!strlen($html)) {
            wp_send_json_error("No content provided");
        }

        try {
            $result = Cordy_PDF_Generator::generate($post_id, $html, $title);

            if (!$result) {
                wp_send_json_error(
                    "PDF generation failed — file could not be written.",
                );
            }

            wp_send_json_success([
                "url" => $result["url"],
                "filename" => $result["filename"],
                "filesize" => $result["filesize"],
            ]);
        } catch (Exception $e) {
            wp_send_json_error("Error generating PDF: " . $e->getMessage());
        }

        exit();
    }
}
