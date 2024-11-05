<?php
/**
 * Plugin Name: ZIO Manage Post Type
 * Description: Allow users to manage and change post types
 * Version: 1.1.0
 * Author: Zack The Worker
 * Author URI: https://github.com/zack-the-worker/wp-zio-manage-post-type
 * Text Domain: zio-manage-post-type
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ZIO_MPT_VERSION', '1.1.0');
define('ZIO_MPT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ZIO_MPT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ZIO_TEXT_DOMAIN', 'zio-manage-post-type');
// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'ZIO_MPT\\';
    $base_dir = ZIO_MPT_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . 'class-' . str_replace('\\', '/', strtolower($relative_class)) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize plugin
function zio_mpt_init() {
    require_once ZIO_MPT_PLUGIN_DIR . 'includes/class-post-type-manager.php';
    require_once ZIO_MPT_PLUGIN_DIR . 'includes/class-settings-page.php';
    
    $post_type_manager = new ZIO_MPT\Post_Type_Manager();
    $settings_page = new ZIO_MPT\Settings_Page();
}
add_action('plugins_loaded', 'zio_mpt_init');

// Activation hook
register_activation_hook(__FILE__, 'zio_mpt_activate');
function zio_mpt_activate() {
    // Activation tasks if needed
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'zio_mpt_deactivate');
function zio_mpt_deactivate() {
    // Cleanup tasks if needed
    flush_rewrite_rules();
}