<?php

function redirect_product_to_pdf()
{
    $type = isset($_GET["type"]) ? sanitize_key(wp_unslash($_GET["type"])) : "";

    if (!is_product() || $type !== "pdf") {
        return;
    }

    global $post;
    $uploads = wp_upload_dir();

    // New system: direct path stored in cordy_pdf_path meta (versioned datasheets folder)
    $rel_path = get_post_meta($post->ID, "cordy_pdf_path", true);
    if (!empty($rel_path)) {
        $pdf_url = trailingslashit($uploads["baseurl"]) . $rel_path;
        wp_redirect($pdf_url);
        exit();
    }

    // Legacy fallback: attachment-based approach (current_pdf_file stores attachment ID)
    $pdf_id = get_post_meta($post->ID, "current_pdf_file", true);
    if (!empty($pdf_id)) {
        $pdf_file = get_post_meta($pdf_id, "_wp_attached_file", true);
        if (!empty($pdf_file)) {
            $pdf_url = trailingslashit($uploads["baseurl"]) . $pdf_file;
            wp_redirect($pdf_url);
            exit();
        }
    }
}
add_action("template_redirect", "redirect_product_to_pdf");
