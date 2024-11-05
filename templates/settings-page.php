<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style type="text/css">
	.zio-mpt-text-green td {
		color: green;
		font-weight: bold;
	}
</style>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php settings_errors('zio_mpt_messages'); ?>

    <h2><?php _e('Existing Post Types', ZIO_TEXT_DOMAIN); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Name', ZIO_TEXT_DOMAIN); ?></th>
                <th><?php _e('Label', ZIO_TEXT_DOMAIN); ?></th>
                <th><?php _e('Public', ZIO_TEXT_DOMAIN); ?></th>
                <th><?php _e('Hierarchical', ZIO_TEXT_DOMAIN); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $post_types = get_option('zio_custom_post_types', []);
            $default_post_types = get_post_types(array(), 'object');
            foreach ($default_post_types as $default_post_type) {
            	$post_types[$default_post_type->name] = array(
            		'labels' => array('name' => $default_post_type->label),
            		'public' => $default_post_type->public,
            		'hierarchical' => $default_post_type->hierarchical,
            		'is_default' => true
            	);
            }
            foreach ($post_types as $name => $post_type_args) :
                ?>
                <tr class="<?php echo (!isset($post_type_args['is_default']) ? 'zio-mpt-text-green' : ''); ?>">
                    <td><?php echo esc_html($name); ?></td>
                    <td><?php echo esc_html($post_type_args['labels']['name']); ?></td>
                    <td><?php echo $post_type_args['public'] ? '✓' : '✗'; ?></td>
                    <td><?php echo $post_type_args['hierarchical'] ? '✓' : '✗'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2><?php _e('Create New Post Type', ZIO_TEXT_DOMAIN); ?></h2>
    <form method="post" action="">
        <?php wp_nonce_field('zio_mpt_create_post_type'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="post_type_name"><?php _e('Post Type Name', ZIO_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" id="post_type_name" name="post_type_data[name]" class="regular-text" required />
                    <p class="description"><?php _e('Lowercase letters and underscores only (e.g. my_post_type)', ZIO_TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="post_type_label"><?php _e('Label', ZIO_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <input type="text" id="post_type_label" name="post_type_data[label]" class="regular-text" required />
                    <p class="description"><?php _e('Human-readable name (e.g. My Post Type)', ZIO_TEXT_DOMAIN); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="post_type_public"><?php _e('Public', ZIO_TEXT_DOMAIN); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="post_type_public" name="post_type_data[public]" value="1" />
                        <?php _e('Make this post type public', ZIO_TEXT_DOMAIN); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Create Post Type', ZIO_TEXT_DOMAIN)); ?>
    </form>
</div>