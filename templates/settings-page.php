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
    
    <?php settingsesc_html_errors('zio_mpt_messages'); ?>

    <h2><?php esc_html_e('Existing Post Types', 'zio-manage-post-type'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Name', 'zio-manage-post-type'); ?></th>
                <th><?php esc_html_e('Label', 'zio-manage-post-type'); ?></th>
                <th><?php esc_html_e('Public', 'zio-manage-post-type'); ?></th>
                <th><?php esc_html_e('Hierarchical', 'zio-manage-post-type'); ?></th>
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

    <h2><?php esc_html_e('Create New Post Type', 'zio-manage-post-type'); ?></h2>
    <form method="post" action="">
        <?php wp_nonce_field('zio_mpt_create_post_type'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="post_type_name"><?php esc_html_e('Post Type Name', 'zio-manage-post-type'); ?></label>
                </th>
                <td>
                    <input type="text" id="post_type_name" name="post_type_data[name]" class="regular-text" required />
                    <p class="description"><?php esc_html_e('Lowercase letters and underscores only (e.g. my_post_type)', 'zio-manage-post-type'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="post_type_label"><?php esc_html_e('Label', 'zio-manage-post-type'); ?></label>
                </th>
                <td>
                    <input type="text" id="post_type_label" name="post_type_data[label]" class="regular-text" required />
                    <p class="description"><?php esc_html_e('Human-readable name (e.g. My Post Type)', 'zio-manage-post-type'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="post_type_public"><?php esc_html_e('Public', 'zio-manage-post-type'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="post_type_public" name="post_type_data[public]" value="1" />
                        <?php esc_html_e('Make this post type public', 'zio-manage-post-type'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Create Post Type', 'zio-manage-post-type')); ?>
    </form>
</div>