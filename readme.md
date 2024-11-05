# Change Post Type Plugin

A custom WordPress plugin that allows you to modify the `post_type` of selected posts directly from the post list page. Additionally, this plugin provides a settings page to manage existing post types and add new ones.

## Features

1. **Filter Posts by Post Type:** 
   - Adds a dropdown filter to the post list page to display posts by specific `post_type`.

2. **Bulk Change Post Type:** 
   - Adds a "Change Post Type" option in the bulk actions dropdown for selected posts.
   - When applied, a popup appears to choose a new `post_type` for the selected posts.

3. **Manage Post Types:** 
   - A settings page displays existing `post_type` entries and allows creating new custom `post_type` entries without modifying theme code.

## Installation

1. Download the plugin files and create a folder named `zio-manage-post-type` in your `wp-content/plugins/` directory.
2. Upload the files to the `wp-content/plugins/zio-manage-post-type` directory.
3. Activate the plugin from the WordPress **Plugins** dashboard.

## Usage

### 1. Filter Posts by `post_type`

   - Go to **Posts** > **All Posts**.
   - Use the dropdown filter at the top to filter posts by specific `post_type`.

### 2. Bulk Change Post Type

   - Select one or more posts from the post list.
   - Choose **Change Post Type** from the bulk actions dropdown.
   - Click **Apply**.
   - In the popup modal, select the target `post_type` and confirm.

### 3. Manage Post Types

   - Go to **Settings** > **ZIO Post Types**.
   - View the list of existing post types.
   - Use the form at the bottom of the page to add a new `post_type` by providing a name, label.

## Files Structure

```plaintext
zio-manage-post-type/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── includes/
│   ├── class-post-type-manager.php
│   └── class-settings-page.php
├── templates/
│   └── settings-page.php
├── index.php
└── zio-manage-post-type.php
