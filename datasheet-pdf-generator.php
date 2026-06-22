<?php
/**
 * Plugin Name: Datasheet PDF Generator for WooCommerce
 * Plugin URI:  https://agentcordy.com/datasheet-pdf-generator/
 * Description: Automatically generates a PDF for each WooCommerce product.
 * Version:     1.0.0
 * Author:      Agent Cordy
 * Author URI:  https://agentcordy.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires Plugins: woocommerce
 * Text Domain: datasheet-pdf-generator-for-woocommerce
 * Domain Path: /languages
 */

if (!defined("WPINC")) {
    die();
}

define("DATASHEET_PDF_GENERATOR_FILE", __FILE__);

require_once plugin_dir_path(__FILE__) . "vendor/autoload.php";

require_once plugin_dir_path(__FILE__) . "includes/class-cordy-settings.php";
require_once plugin_dir_path(__FILE__) .
    "includes/class-cordy-pdf-generator.php";
require_once plugin_dir_path(__FILE__) .
    "includes/class-datasheet-pdf-generator.php";
require_once plugin_dir_path(__FILE__) . "includes/class-cordy-tab.php";
include_once plugin_dir_path(__FILE__) . "includes/pdf-redirect.php";

function run_datasheet_pdf_generator()
{
    new Cordy_Settings();
    new Datasheet_PDF_Generator(__FILE__);
    new Cordy_Tab();
}
run_datasheet_pdf_generator();
