<?php
/*
Plugin Name: Complete Link Manager
Description: Easily manage all links in your WordPress posts and pages. Edit, delete, or update links directly from your dashboard.
Version: 1.0.0
Requires at least: 5.2
Requires PHP: 7.4
Text Domain: complete-link-manager
Domain Path: /languages/
Author: Harpalsinh Parmar
Author URI: https://profiles.wordpress.org/developer1998/
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-complete-link-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/complete_link_mgr-ajax-handlers.php';

// Initialize the plugin
function complete_link_mgr_init() {
    new complete_link_mgr_CompleteLinkManager();
}
add_action('plugins_loaded', 'complete_link_mgr_init');

//Activation hook
register_activation_hook(__FILE__, 'complete_link_mgr_plugin_activation');
function complete_link_mgr_plugin_activation() {
    if (!get_option('complete_link_mgr_activation_time')) {
        add_option('complete_link_mgr_activation_time', time());
    }
    flush_rewrite_rules();
}

//Deactivation hook
register_deactivation_hook(__FILE__, 'complete_link_mgr_plugin_deactivation');
function complete_link_mgr_plugin_deactivation() {
    flush_rewrite_rules();
}

//Admin notice
add_action('admin_notices', 'complete_link_mgr_review_notice');
function complete_link_mgr_review_notice() {
    if (get_option('complete_link_mgr_review_notice_dismissed')) {
        return;
    }
    $current_screen = get_current_screen();
    if ($current_screen && $current_screen->base === 'tools_page_complete-link-manager') {
        $activation_time = get_option('complete_link_mgr_activation_time');
        $current_time = time();
        if (isset($activation_time) && ($current_time - $activation_time) >= DAY_IN_SECONDS) {
            echo '<div class="notice notice-info is-dismissible clm-review-notice">
                <p><strong>Complete Link Manager:</strong> Enjoying this plugin? Please consider leaving a positive review or liking it on the WordPress repository. 
                <a href="https://wordpress.org/support/plugin/complete-link-manager/reviews/" target="_blank">Leave a Review</a></p>
            </div>';

        }
    }
}

add_action('wp_ajax_complete_link_mgr_dismiss_review_notice', 'complete_link_mgr_dismiss_review_notice');
function complete_link_mgr_dismiss_review_notice() {
    update_option('complete_link_mgr_review_notice_dismissed', true);
    wp_die();
}

// Add plugin setting page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'complete_link_mgr_add_settings_link');
function complete_link_mgr_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('tools.php?page=complete-link-manager') . '">' . __('Settings', 'complete-link-manager') . '</a>';
    array_unshift($links, $settings_link); 
    return $links;
}

//Add languages
add_action('plugins_loaded', function () {
    load_plugin_textdomain('complete-link-manager', false, dirname(plugin_basename(__FILE__)) . '/languages');
});
?>