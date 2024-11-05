<?php
namespace ZIO_MPT;

class Post_Type_Manager {
    public function __construct() {
        add_action('admin_init', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_zio_change_post_type', array($this, 'ajax_change_post_type'));
    }

    public function init() {
    	self::register_custom_post_types();
        // Add post type filter to posts list
        add_action('restrict_manage_posts', array($this, 'add_post_type_filter'));
        
        // Add bulk action
        add_filter('bulk_actions-edit-post', array($this, 'register_bulk_action'));
        add_filter('handle_bulk_actions-edit-post', array($this, 'handle_bulk_action'), 10, 3);
        
        // Apply post type filter
        add_filter('parse_query', array($this, 'filter_posts_by_post_type'));
    }

    public function enqueue_scripts($hook) {
        if ('edit.php' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'zio-mpt-style',
            ZIO_MPT_PLUGIN_URL . 'assets/css/style.css',
            array(),
            ZIO_MPT_VERSION
        );

        wp_enqueue_script(
            'zio-mpt-script',
            ZIO_MPT_PLUGIN_URL . 'assets/js/script.js',
            array('jquery'),
            ZIO_MPT_VERSION,
            true
        );

        wp_localize_script('zio-mpt-script', 'zioMPT', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('zio_mpt_nonce'),
            'post_types' => $this->get_available_post_types(),
            'i18n' => array(
            	'changePostType' => __('Change post type', ZIO_TEXT_DOMAIN),
            	'selectNewType' =>  __('Select New Post Type', ZIO_TEXT_DOMAIN),
            	'cancel' =>  __('Cancel', ZIO_TEXT_DOMAIN),
            	'apply' =>  __('Apply', ZIO_TEXT_DOMAIN),
            	'noPostsSelected' =>  __('No posts selected', ZIO_TEXT_DOMAIN),
            	'processing' =>  __('Processing...', ZIO_TEXT_DOMAIN),
            	'errorOccurred' =>  __('An error occurred', ZIO_TEXT_DOMAIN),
            	'creating' =>  __('Creating...', ZIO_TEXT_DOMAIN),
            	'createPostType' =>  __('Create Post Type', ZIO_TEXT_DOMAIN),
            ),
        ));
    }

    public function get_available_post_types() {
        $post_types = get_option('zio_custom_post_types', []);
        $default_post_types = get_post_types(array('public' => true), 'objects');
        foreach ($default_post_types as $default_post_type) {
        	$post_types[$default_post_type->name] = array(
        		'labels' => array('name' => $default_post_type->label),
        		'public' => $default_post_type->public,
        		'hierarchical' => $default_post_type->hierarchical,
        		'is_default' => true
        	);
        }

        $types = array();
        
        foreach ($post_types as $name => $post_type) {
            $types[$name] = $post_type['labels']['name'];
        }
        
        return $types;
    }

    public function add_post_type_filter() {
        global $typenow;
        if ($typenow == 'post') {
            $post_types = $this->get_available_post_types();
            $current = isset($_GET['post_type_filter']) ? $_GET['post_type_filter'] : '';
            
            echo '<select name="post_type_filter">';
            echo '<option value="">' . __('All Post Types', ZIO_TEXT_DOMAIN) . '</option>';
            
            foreach ($post_types as $name => $label) {
                printf(
                    '<option value="%s" %s>%s</option>',
                    esc_attr($name),
                    selected($current, $name, false),
                    esc_html($label)
                );
            }
            
            echo '</select>';
        }
    }

    public function filter_posts_by_post_type($query) {
        global $pagenow;
        
        if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type_filter'])) {
            $post_type_filter = sanitize_text_field($_GET['post_type_filter']);
            
            if (!empty($post_type_filter)) {
                $query->query_vars['post_type'] = $post_type_filter;
            }
        }
    }

    public function register_bulk_action($bulk_actions) {
        $bulk_actions['change_post_type'] = __('Change post type', ZIO_TEXT_DOMAIN);
        return $bulk_actions;
    }

    public function handle_bulk_action($redirect_to, $doaction, $post_ids) {
        if ($doaction !== 'change_post_type') {
            return $redirect_to;
        }

        return $redirect_to;
    }

    public function ajax_change_post_type() {
        check_ajax_referer('zio_mpt_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Permission denied', ZIO_TEXT_DOMAIN));
        }

        $post_ids = isset($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : array();
        $new_post_type = isset($_POST['new_post_type']) ? sanitize_text_field($_POST['new_post_type']) : '';

        if (empty($post_ids) || empty($new_post_type)) {
            wp_send_json_error(__('Invalid parameters', ZIO_TEXT_DOMAIN));
        }

        foreach ($post_ids as $post_id) {
            if (current_user_can('edit_post', $post_id)) {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_type' => $new_post_type
                ));
            }
        }

        wp_send_json_success();
    }

	// Function to register custom post types from database
	public static function register_custom_post_types() {
	    $custom_post_types = get_option('zio_custom_post_types', []);

	    foreach ($custom_post_types as $name => $args) {
	        register_post_type($name, $args);
	    }
	}
}