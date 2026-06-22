<?php

class Cordy_PDF_Generator
{
    const DATASHEETS_SUBDIR = "datasheets";
    const HTML_META_KEY = "cordy_pdf_html_content";
    const PATH_META_KEY = "cordy_pdf_path";

    /**
     * Returns ['path' => ..., 'url' => ...] for the datasheets directory, creating it if needed.
     */
    public static function get_datasheets_dir()
    {
        $upload = wp_upload_dir();
        $path = $upload["basedir"] . "/" . self::DATASHEETS_SUBDIR;
        $url = $upload["baseurl"] . "/" . self::DATASHEETS_SUBDIR;
        wp_mkdir_p($path);
        return compact("path", "url");
    }

    /**
     * Build and save a versioned PDF to wp-uploads/datasheets/.
     *
     * @param int         $product_id
     * @param string      $html   Raw HTML from the PDF content editor
     * @param string|null $title  Override product title
     *
     * @return array|false ['url', 'filename', 'filesize', 'rel_path'] on success, false on failure.
     */
    public static function generate($product_id, $html, $title = null)
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return false;
        }

        $html = wp_kses_post(stripslashes($html));
        $title = $title ?: $product->get_name();
        $branding = self::get_branding();

        $build = self::build_mpdf($branding);
        $mpdf = $build["mpdf"];
        $title_font = $build["title_font"];

        $mpdf->charset_in = "utf-8";
        $mpdf->WriteHTML(self::get_pdf_css(), \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML(
            self::build_full_html(
                $html,
                $title,
                self::get_sidebar_html($product_id),
                $branding,
                $product_id,
                $title_font,
            ),
        );

        $dir = self::get_datasheets_dir();
        $filename = "product-" . $product_id . "-" . time() . ".pdf";
        $filepath = $dir["path"] . "/" . $filename;
        $rel_path = self::DATASHEETS_SUBDIR . "/" . $filename;

        $mpdf->Output($filepath, "F");

        if (!file_exists($filepath)) {
            return false;
        }

        $filesize = round(filesize($filepath) / 1024, 2) . " KB";

        update_post_meta($product_id, self::PATH_META_KEY, $rel_path);
        update_post_meta($product_id, self::HTML_META_KEY, $html);

        return [
            "url" => $dir["url"] . "/" . $filename,
            "filename" => $filename,
            "filesize" => $filesize,
            "rel_path" => $rel_path,
        ];
    }

    /**
     * Build a PDF and return its raw binary content as a string (for in-browser preview).
     */
    public static function preview($product_id, $html, $title)
    {
        $html = wp_kses_post(stripslashes($html));
        $branding = self::get_branding();

        $build = self::build_mpdf($branding);
        $mpdf = $build["mpdf"];
        $title_font = $build["title_font"];

        $mpdf->charset_in = "utf-8";
        $mpdf->WriteHTML(self::get_pdf_css(), \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML(
            self::build_full_html(
                $html,
                $title,
                self::get_sidebar_html($product_id),
                $branding,
                $product_id,
                $title_font,
            ),
        );

        return $mpdf->Output("", "S");
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private static function get_branding()
    {
        $fallback_header = dirname(__FILE__) . "/../admin/css/header.png";
        $fallback_footer = dirname(__FILE__) . "/../admin/css/footer.png";

        $header_url = get_option(Cordy_Settings::OPTION_HEADER_IMAGE, "");
        $footer_url = get_option(Cordy_Settings::OPTION_FOOTER_IMAGE, "");

        // Convert upload URLs to filesystem paths so mPDF can read them directly.
        $upload = wp_upload_dir();
        $header = $header_url
            ? str_replace($upload["baseurl"], $upload["basedir"], $header_url)
            : (file_exists($fallback_header)
                ? realpath($fallback_header)
                : "");
        $footer = $footer_url
            ? str_replace($upload["baseurl"], $upload["basedir"], $footer_url)
            : (file_exists($fallback_footer)
                ? realpath($fallback_footer)
                : "");

        return [
            "header" => $header,
            "footer" => $footer,
            "header_size" => 40,
            "footer_size" => 20,
        ];
    }

    /**
     * Build and configure an mPDF instance.
     *
     * @return array { mpdf: \Mpdf\Mpdf, title_font: string }
     */
    private static function build_mpdf($branding)
    {
        $fonts_dir = dirname(__FILE__) . "/../fonts/";

        $config_variables = new \Mpdf\Config\ConfigVariables();
        $font_variables = new \Mpdf\Config\FontVariables();
        $config_defaults = $config_variables->getDefaults();
        $font_defaults = $font_variables->getDefaults();

        $font_dirs = array_merge($config_defaults["fontDir"], [$fonts_dir]);
        $font_data = $font_defaults["fontdata"] + [
            "inter" => [
                "R" => "Inter-Regular.ttf",
                "B" => "Inter-Bold.ttf",
                "I" => "Inter-Regular.ttf",
                "BI" => "Inter-Bold.ttf",
            ],
        ];

        $title_font = "inter";
        $body_font = "inter";

        $mpdf = new \Mpdf\Mpdf([
            "mode" => "utf-8",
            "default_font" => $body_font,
            "format" => "A4",
            "margin_left" => 0,
            "margin_right" => 0,
            "margin_top" => (int) $branding["header_size"] + 10, // 40 mm header + 10 mm gap
            "margin_bottom" => (int) $branding["footer_size"] + 10, // 20 mm footer + 10 mm gap
            "margin_header" => 0,
            "margin_footer" => 0,
            "orientation" => "P",
            "fontDir" => $font_dirs,
            "fontdata" => $font_data,
        ]);

        $header_html =
            '<div style="text-align:center;display:block;"><img src="' .
            esc_attr($branding["header"]) .
            '" /></div>';
        $mpdf->SetHTMLHeader($header_html);

        $info_row =
            '<table width="100%" style="margin-bottom:4px;font-size:11px;color:#555;padding-left:10mm;padding-right:10mm;">' .
            "<tr>" .
            '<td style="text-align:right;">Page {PAGENO} of {nbpg}</td>' .
            "</tr>" .
            "</table>";

        $footer_img =
            '<div style="text-align:center;display:block;"><img src="' .
            esc_attr($branding["footer"]) .
            '" /></div>';
        $mpdf->SetHTMLFooter($info_row . $footer_img);

        return ["mpdf" => $mpdf, "title_font" => $title_font];
    }

    private static function get_pdf_css()
    {
        return "h1{font-size:22px;line-height:1.2em;}body,td,th{font-size:12pt;}";
    }

    private static function build_full_html(
        $html,
        $title,
        $sidebar_html,
        $branding,
        $product_id = 0,
        $title_font = "inter",
    ) {
        $html = do_shortcode($html);
        $html = self::style_h1($html, $title_font);

        $title_display = esc_html($title);

        $content =
            '<h1 style="font-family:' .
            esc_attr($title_font) .
            ';font-weight:bold;">' .
            $title_display .
            "</h1>" .
            $html;
        $layout =
            '<div style="display:block;padding-left:10mm;padding-right:10mm;">' .
            '<div style="float:left;width:52%">' .
            $content .
            "</div>" .
            '<div style="float:right;width:42%">' .
            $sidebar_html .
            "</div>" .
            "</div>";

        return $layout;
    }

    private static function style_h1($html, $title_font = "inter")
    {
        if (!strlen(trim($html))) {
            return "";
        }
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, "HTML-ENTITIES", "UTF-8"));
        foreach ($dom->getElementsByTagName("h1") as $el) {
            $existing = $el->hasAttribute("style")
                ? $el->getAttribute("style") . "; "
                : "";
            $el->setAttribute(
                "style",
                $existing . "font-family:" . $title_font . ";font-weight:bold;",
            );
        }
        $body = $dom->getElementsByTagName("body")->item(0);
        if (!$body) {
            return $html;
        }
        $inner = "";
        foreach ($body->childNodes as $node) {
            $inner .= $dom->saveHTML($node);
        }
        return $inner;
    }

    /**
     * Get the display value for a variation attribute.
     */
    private static function resolve_attr_display($taxonomy_or_name, $slug)
    {
        if (!$slug) {
            return $slug;
        }

        $display = $slug;

        if (taxonomy_exists($taxonomy_or_name)) {
            $term = get_term_by("slug", $slug, $taxonomy_or_name);
            $display = $term && !is_wp_error($term) ? $term->name : $slug;
        }

        $display = self::normalize_month_codes($display);

        $display = preg_replace_callback(
            '/^(\d+)-(\d+)$/',
            static function ($m) {
                return str_pad($m[1], 2, "0", STR_PAD_LEFT) .
                    "-" .
                    str_pad($m[2], 2, "0", STR_PAD_LEFT);
            },
            $display,
        );

        return $display;
    }

    /**
     * Convert month abbreviations (ERP artefacts) back to zero-padded numbers.
     */
    private static function normalize_month_codes($value)
    {
        static $map = [
            "jan" => "01",
            "feb" => "02",
            "mar" => "03",
            "apr" => "04",
            "may" => "05",
            "jun" => "06",
            "jul" => "07",
            "aug" => "08",
            "sep" => "09",
            "oct" => "10",
            "nov" => "11",
            "dec" => "12",
        ];
        return preg_replace_callback(
            "/\b(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\b/i",
            static function ($m) use ($map) {
                return $map[strtolower($m[1])];
            },
            $value,
        );
    }

    /**
     * Build the full right-column sidebar: product image + specs table.
     */
    private static function get_sidebar_html($product_id)
    {
        $product = wc_get_product($product_id);
        $html = "";

        $thumbnail_id = get_post_thumbnail_id($product_id);
        if ($thumbnail_id) {
            $img_path = get_attached_file($thumbnail_id);
            if ($img_path && file_exists($img_path)) {
                $html .=
                    '<img src="' .
                    $img_path .
                    '" style="width:100%;margin-bottom:10px;" />';
            }
        }

        if (!$product) {
            return $html;
        }

        $td =
            'style="padding:4px 8px;font-size:13px;border-bottom:1px solid #e0e0e0;"';
        $tl =
            'style="padding:4px 8px;font-size:13px;font-weight:bold;color:#333;border-bottom:1px solid #e0e0e0;white-space:nowrap;"';

        if ($product->is_type("variable")) {
            $variations = $product->get_available_variations();
            $headers = [];
            $rows = "";
            $header_set = false;

            foreach ($variations as $variation) {
                $var_obj = wc_get_product($variation["variation_id"]);
                $var_sku = $var_obj ? $var_obj->get_sku() : "";
                $attr_vals = [];

                foreach ($variation["attributes"] as $attr_key => $attr_val) {
                    $clean = str_replace("attribute_", "", $attr_key);
                    if (!$header_set) {
                        $headers[] = ucfirst(wc_attribute_label($clean));
                    }
                    $attr_vals[] = self::resolve_attr_display(
                        $clean,
                        $attr_val,
                    );
                }
                $header_set = true;

                $rows .=
                    "<tr>" .
                    "<td " .
                    $td .
                    ">" .
                    esc_html($var_sku) .
                    "</td>" .
                    "<td " .
                    $td .
                    ">" .
                    esc_html(implode(" / ", $attr_vals)) .
                    "</td>" .
                    "</tr>";
            }

            if ($rows) {
                $html .= '<table style="width:100%;border-collapse:collapse;">';
                $html .=
                    "<tr>" .
                    "<td " .
                    $tl .
                    ">SKU / Part #</td>" .
                    "<td " .
                    $tl .
                    ">" .
                    esc_html(implode(" / ", $headers)) .
                    "</td>" .
                    "</tr>";
                $html .= $rows;
                $html .= "</table>";
            }
        } else {
            $sku = $product->get_sku();
            if ($sku) {
                $html .= '<table style="width:100%;border-collapse:collapse;">';
                $html .=
                    "<tr><td " .
                    $tl .
                    ">SKU / Part #</td><td " .
                    $td .
                    ">" .
                    esc_html($sku) .
                    "</td></tr>";
                $html .= "</table>";
            }
        }

        return $html;
    }
}
