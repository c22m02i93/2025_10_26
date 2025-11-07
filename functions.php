<?php

/**
 * Bootscore functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Bootscore
 * @version 6.2.2
 */


// Exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Update Checker
 * https://github.com/YahnisElsts/plugin-update-checker
 */
require 'inc/update/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/bootscore/bootscore/',
	__FILE__,
	'bootscore'
);

// Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');


/**
 * Load required files
 */
require_once('inc/theme-setup.php');             // Theme setup and custom theme supports
require_once('inc/breadcrumb.php');              // Breadcrumb
require_once('inc/columns.php');                 // Main/sidebar column width and breakpoints
require_once('inc/comments.php');                // Comments
require_once('inc/enable-html.php');             // Enable HTML in category and author description
require_once('inc/enqueue.php');                 // Enqueue scripts and styles
require_once('inc/excerpt.php');                 // Adds excerpt to pages
require_once('inc/fontawesome.php');             // Adds shortcode for inserting Font Awesome icons
require_once('inc/hooks.php');                   // Custom hooks
require_once('inc/navwalker.php');               // Register the Bootstrap 5 navwalker
require_once('inc/navmenu.php');                 // Register the nav menus
require_once('inc/pagination.php');              // Pagination for loop and single posts
require_once('inc/password-protected-form.php'); // Form if post or page is protected by password
require_once('inc/template-tags.php');           // Meta information like author, date, comments, category and tags badges
require_once('inc/template-functions.php');      // Functions which enhance the theme by hooking into WordPress
require_once('inc/widgets.php');                 // Register widget area and disables Gutenberg in widgets
require_once('inc/deprecated.php');              // Fallback functions being dropped in v6
require_once('inc/tinymce-editor.php');          // Fix body margin and font-family in backend if classic editor is used
require_once('inc/custom-post-types.php');       // Custom post types and meta fields
require_once('inc/remote-parsers.php');          // Remote content parsers
// Blocks
// Patterns
require_once('inc/blocks/patterns.php');  // Register pattern category and script to hide wp-block classes

// Widgets
require_once('inc/blocks/block-widget-archives.php');        // Archive block
require_once('inc/blocks/block-widget-calendar.php');        // Calendar block
require_once('inc/blocks/block-widget-categories.php');      // Categories block
require_once('inc/blocks/block-widget-latest-comments.php'); // Latest posts block
require_once('inc/blocks/block-widget-latest-posts.php');    // Latest posts block
require_once('inc/blocks/block-widget-search.php');          // Searchform block

// Contents
require_once('inc/blocks/block-buttons.php'); // Button block
require_once('inc/blocks/block-code.php');    // Code block
require_once('inc/blocks/block-quote.php');   // Quote block
require_once('inc/blocks/block-table.php');   // Table block

// Experimental
// Disable unsupported blocks and patterns
if (apply_filters('bootscore/disable/unsupported/blocks', false)) {
  require_once('inc/blocks/disable-unsupported-blocks.php');
}


/**
 * Disable theme updates
 */
function hram_disable_theme_updates($value) {
  if (!is_object($value) || !isset($value->response) || !is_array($value->response)) {
    return $value;
  }

  $stylesheet = get_stylesheet();

  if (isset($value->response[$stylesheet])) {
    unset($value->response[$stylesheet]);
  }

  return $value;
}
add_filter('site_transient_update_themes', 'hram_disable_theme_updates');
add_filter('auto_update_theme', '__return_false');


/**
 * Use the classic editor for posts
 */
function hram_disable_block_editor_for_posts($use_block_editor, $post_type) {
  if ('post' === $post_type) {
    return false;
  }

  return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'hram_disable_block_editor_for_posts', 10, 2);



/**
 * Load WooCommerce scripts if plugin is activated
 */
if (class_exists('WooCommerce')) {
  require get_template_directory() . '/woocommerce/wc-functions.php';
}


/**
 * Load Jetpack compatibility file
 */
if (defined('JETPACK__VERSION')) {
  require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Replace default site icon markup with theme SVG favicon.
 */
function hram_setup_svg_favicon_support() {
  remove_action('wp_head', 'wp_site_icon', 99);
  remove_action('admin_head', 'wp_site_icon', 99);
  remove_action('login_head', 'wp_site_icon', 99);
}
add_action('after_setup_theme', 'hram_setup_svg_favicon_support', 20);

/**
 * Output SVG favicon links for all contexts.
 */
function hram_output_svg_favicon() {
  $favicon_url = get_template_directory_uri() . '/assets/images/favicon.svg';

  printf("<link rel=\"icon\" type=\"image/svg+xml\" href=\"%s\">\n", esc_url($favicon_url));
  printf("<link rel=\"alternate icon\" type=\"image/svg+xml\" href=\"%s\">\n", esc_url($favicon_url));
}
add_action('wp_head', 'hram_output_svg_favicon', 5);
add_action('admin_head', 'hram_output_svg_favicon', 5);
add_action('login_head', 'hram_output_svg_favicon', 5);
