<?php
/**
 * @package Tpp Interviews
 * @version 0.1
 */
/*
Plugin Name: TPP Interviews
Description: The Photography Parlour Interviews, This plugin will setup custom post types so that they can be setup as interviews
Author: Lee Parsons
Version: 0.1
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('TPP_INTERVIEWS_PLUGIN_DIR', __DIR__ . '/');

define('TPP_INTERVIEWS_PLUGIN_URL', plugins_url( '' , __FILE__));

require TPP_INTERVIEWS_PLUGIN_DIR . 'general_hooks.php';

if (is_admin()) {
    require TPP_INTERVIEWS_PLUGIN_DIR . 'admin/installer.php';
    register_activation_hook(__FILE__, 'installTppInterviews');

    require TPP_INTERVIEWS_PLUGIN_DIR . 'admin/hooks.php';

} elseif (is_main_site()) {

}



