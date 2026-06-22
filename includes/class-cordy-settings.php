<?php

class Cordy_Settings
{
    const OPTION_HEADER_IMAGE = "cordy_pdf_header_image";
    const OPTION_FOOTER_IMAGE = "cordy_pdf_footer_image";
    const OPTION_TAB_DEFAULT = "cordy_pdf_tab_default";

    public function __construct()
    {
        add_action("admin_menu", [$this, "add_menu_page"]);
        add_action("admin_init", [$this, "register_settings"]);
        add_action("admin_enqueue_scripts", [$this, "enqueue_scripts"]);
    }

    public function add_menu_page()
    {
        add_menu_page(
            "Datasheet PDF",
            "Datasheet PDF",
            "manage_options",
            "datasheet-pdf-generator-for-woocommerce",
            [$this, "render_page"],
            "dashicons-media-document",
            58,
        );
    }

    public function register_settings()
    {
        register_setting("cordy_pdf_general", self::OPTION_HEADER_IMAGE, [
            "sanitize_callback" => "esc_url_raw",
        ]);
        register_setting("cordy_pdf_general", self::OPTION_FOOTER_IMAGE, [
            "sanitize_callback" => "esc_url_raw",
        ]);
        register_setting("cordy_pdf_general", self::OPTION_TAB_DEFAULT, [
            "sanitize_callback" => "absint",
        ]);
    }

    public function enqueue_scripts($hook)
    {
        if ($hook !== "toplevel_page_datasheet-pdf-generator-for-woocommerce") {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            "cordy-pdf-settings",
            plugin_dir_url(DATASHEET_PDF_GENERATOR_FILE) .
                "admin/js/settings.js",
            ["jquery"],
            "1.0",
            true,
        );

        wp_enqueue_style(
            "datasheet-pdf-generator-admin-style",
            plugin_dir_url(DATASHEET_PDF_GENERATOR_FILE) .
                "admin/css/style.css",
            [],
            "1.13",
        );
    }

    public function render_page()
    {
        if (!current_user_can("manage_options")) {
            return;
        } ?>
        <div class="wrap">
            <h1>Datasheet PDF</h1>
            <div class="cordy-settings-panel">
                <?php $this->render_general_tab(); ?>
            </div>
        </div>
        <?php
    }

    private function render_general_tab()
    {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields("cordy_pdf_general"); ?>

            <h2>Branding</h2>
            <p>Set the header and footer images printed on every generated PDF.</p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Header Image</th>
                    <td><?php $this->field_image(
                        self::OPTION_HEADER_IMAGE,
                        "PNG recommended. 300 dpi print-ready: 2480 × 472 px — 150 dpi digital: 1240 × 236 px.",
                    ); ?></td>
                </tr>
                <tr>
                    <th scope="row">Footer Image</th>
                    <td><?php $this->field_image(
                        self::OPTION_FOOTER_IMAGE,
                        "PNG recommended. 300 dpi print-ready: 2480 × 236 px — 150 dpi digital: 1240 × 118 px.",
                    ); ?></td>
                </tr>
            </table>

            <h2>Product Page Tab</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Show PDF Tab by Default</th>
                    <td>
                        <?php $this->field_checkbox(
                            self::OPTION_TAB_DEFAULT,
                            'Show a "Product Datasheet" download tab on product pages by default (can be overridden per product)',
                        ); ?>
                    </td>
                </tr>
            </table>

            <?php submit_button("Save Settings"); ?>
        </form>
        <?php
    }

    private function field_image($option, $hint = "")
    {
        $value = get_option($option, ""); ?>
        <div class="cordy-image-field">
            <input type="text"
                   id="<?php echo esc_attr($option); ?>"
                   name="<?php echo esc_attr($option); ?>"
                   value="<?php echo esc_url($value); ?>"
                   class="regular-text" />
            <button type="button"
                    class="button cordy-upload-btn"
                    data-target="<?php echo esc_attr(
                        $option,
                    ); ?>">Select Image</button>
            <?php if ($value): ?>
            <div class="cordy-image-preview">
                <img src="<?php echo esc_url($value); ?>" alt="" />
            </div>
            <?php endif; ?>
            <?php if ($hint): ?>
            <p class="description cordy-field-hint"><?php echo esc_html(
                $hint,
            ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    private function field_checkbox($option, $label)
    {
        $value = get_option($option, 0); ?>
        <label>
            <input type="checkbox"
                   name="<?php echo esc_attr($option); ?>"
                   value="1"
                   <?php checked(1, $value); ?> />
            <?php echo esc_html($label); ?>
        </label>
        <?php
    }
}
