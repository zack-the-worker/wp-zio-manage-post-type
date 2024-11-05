<?php
namespace ZIO_MPT;

class Settings_Page {
    private $option_name = 'zio_mpt_settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_menu_page() {
        add_options_page(
            __('ZIO Manage Post Type', 'zio-manage-post-type'),
            __('ZIO Post Types', 'zio-manage-post-type'),
            'manage_options',
            'zio-manage-post-type',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting(
            'zio_mpt_settings',
            $this->option_name,
            array($this, 'validate_settings')
        );
    }

    public function render_settings_page() {
        check_admin_referer('zio_mpt_create_post_type');
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_POST['submit']) && isset($_POST['post_type_data'])) {
            $this->handle_post_type_creation();
        }

        require_once ZIO_MPT_PLUGIN_DIR . 'templates/settings-page.php';
    }

    private function handle_post_type_creation() {
        check_admin_referer('zio_mpt_create_post_type');

        $post_type_data = wp_unslash(isset($_POST['post_type_data']) ? sanitize_text_field(wp_unslash($_POST['post_type_data'])) : 'post');
        $name = sanitize_key($post_type_data['name']);
        $label = sanitize_text_field($post_type_data['label']);
        
        if (empty($name) || empty($label)) {
            add_settings_error(
                'zio_mpt_messages',
                'zio_mpt_message',
                __('Post type name and label are required.', 'zio-manage-post-type'),
                'error'
            );
            return;
        }
        if (!post_type_exists($name)) {
	        $args = array(
	            'labels' => array(
	                'name' => $label,
	                'singular_name' => $label
	            ),
	            'public' => isset($post_type_data['public']),
	            'show_ui' => true,
	            'show_in_menu' => true,
	            'capability_type' => 'post',
	            'hierarchical' => false,
	            'rewrite' => array('slug' => $name),
	            'supports' => array('title', 'editor', 'comments', 'revisions', 'trackbacks', 'author', 'excerpt', 'page-attributes', 'thumbnail', 'custom-fields', 'post-formats')
	        );
	        // Get existing custom post types from options
	        $custom_post_types = get_option('zio_custom_post_types', []);
	        
	        // Add the new post type to the array
	        $custom_post_types[$name] = $args;

	        // Save back to options
	        update_option('zio_custom_post_types', $custom_post_types);
	        \ZIO_MPT\Post_Type_Manager::register_custom_post_types();
	        register_post_type($name, $args);
	        flush_rewrite_rules();

	        add_settings_error(
	            'zio_mpt_messages',
	            'zio_mpt_message',
	            __('Post type created successfully.', 'zio-manage-post-type'),
	            'success'
	        );
	    }
	    else {
	    	add_settings_error(
	            'zio_mpt_messages',
	            'zio_mpt_message',
	            __('Post type already exists.', 'zio-manage-post-type'),
	            'error'
	        );
	    }
    }

    public function validate_settings($input) {
        // Add validation if needed
        return $input;
    }
}