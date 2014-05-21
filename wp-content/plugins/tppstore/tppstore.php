<?php
/**
 * @package TPP Store
 * @version 0.1
 */
/*
Plugin Name: TPP Store
Description: The Photography parlour Store
Author: Lee Parsons
Version: 0.1
*/
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('TPP_STORE_PLUGIN_DIR', __DIR__ . '/');

define('TPP_STORE_PLUGIN_URL', plugins_url( '' , __FILE__));


if (is_admin()) {
    require TPP_STORE_PLUGIN_DIR . 'admin/loader.php';
    require TPP_STORE_PLUGIN_DIR. 'admin/admin_hooks.php';
    register_activation_hook(__FILE__, 'installTppStore');
} elseif (is_main_site()) {
    require TPP_STORE_PLUGIN_DIR . 'site/loader.php';
}