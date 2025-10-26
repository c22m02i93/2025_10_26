<?php

/**
 * Enqueue styles & scripts
 *
 * @package Bootscore 
 * @version 6.0.3
 */


// Exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Enqueue scripts and styles
 */
function bootscore_scripts() {

  // Get modification time. Enqueue files with modification date to prevent browser from loading cached scripts and styles when file content changes.
  $modificated_bootscoreCss   = (file_exists(get_template_directory() . '/assets/css/main.css')) ? date('YmdHi', filemtime(get_template_directory() . '/assets/css/main.css')) : 1;
  $modificated_styleCss       = date('YmdHi', filemtime(get_stylesheet_directory() . '/style.css'));
  $modificated_fontawesomeCss = date('YmdHi', filemtime(get_template_directory() . '/assets/fontawesome/css/all.min.css'));
  $modificated_bootstrapJs    = date('YmdHi', filemtime(get_template_directory() . '/assets/js/lib/bootstrap.bundle.min.js'));
  $modificated_themeJs        = date('YmdHi', filemtime(get_template_directory() . '/assets/js/theme.js'));
  $service_schedule_css       = (file_exists(get_template_directory() . '/assets/css/service-schedule.css')) ? date('YmdHi', filemtime(get_template_directory() . '/assets/css/service-schedule.css')) : null;
  $service_schedule_js        = (file_exists(get_template_directory() . '/assets/js/service-schedule.js')) ? date('YmdHi', filemtime(get_template_directory() . '/assets/js/service-schedule.js')) : null;

  // Bootscore
  require_once 'scss-compiler.php';
  bootscore_compile_scss();
  wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main.css', array(), $modificated_bootscoreCss);

  // Style CSS
  wp_enqueue_style('bootscore-style', get_stylesheet_uri(), array(), $modificated_styleCss);

  // Fontawesome
  if (apply_filters('bootscore/load_fontawesome', true)) {
    wp_enqueue_style('fontawesome', get_template_directory_uri() . '/assets/fontawesome/css/all.min.css', array(), $modificated_fontawesomeCss);
  }

	  // Vendor libraries
  wp_enqueue_style(
    'swiper',
    'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css',
    array(),
    '10.3.1'
  );

  wp_enqueue_style(
    'owl-carousel',
    'https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/assets/owl.carousel.min.css',
    array(),
    '2.3.4'
  );

  wp_enqueue_style(
    'owl-theme-default',
    'https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/assets/owl.theme.default.min.css',
    array('owl-carousel'),
    '2.3.4'
  );

  wp_enqueue_style(
    'aos',
    'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css',
    array(),
    '2.3.4'
  );
	
  // Bootstrap JS
  wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/js/lib/bootstrap.bundle.min.js', array(), $modificated_bootstrapJs, true);

	wp_enqueue_script(
    'swiper',
    'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js',
    array(),
    '10.3.1',
    true
  );

  wp_enqueue_script(
    'owl-carousel',
    'https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js',
    array('jquery'),
    '2.3.4',
    true
  );

  wp_enqueue_script(
    'aos',
    'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js',
    array(),
    '2.3.4',
    true
  );

  wp_add_inline_script('aos', 'document.addEventListener("DOMContentLoaded", function() { if (typeof AOS !== "undefined") { AOS.init(); } });');
	
  // Theme JS
  if ($service_schedule_css) {
    wp_register_style('service-schedule', get_template_directory_uri() . '/assets/css/service-schedule.css', array(), $service_schedule_css);
  }

  if ($service_schedule_js) {
    wp_register_script('service-schedule', get_template_directory_uri() . '/assets/js/service-schedule.js', array('swiper'), $service_schedule_js, true);
  }

  wp_enqueue_script('bootscore-script', get_template_directory_uri() . '/assets/js/theme.js', array('jquery'), $modificated_themeJs, true);

  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
}

add_action('wp_enqueue_scripts', 'bootscore_scripts');


/**
 * Register editor styles.
 */
function bootscore_add_editor_styles() {
  // Add support for editor styles and main.css for the editor
  add_theme_support('editor-styles');
  add_editor_style('assets/css/main.css');
}
add_action('after_setup_theme', 'bootscore_add_editor_styles');


/**
 * Enqueue styles for block editor and Pattern Library.
 */
function bootscore_enqueue_editor_and_pattern_library_styles($hook_suffix) {
  $screen = get_current_screen();
  
  // Enqueue editor.css only in the block editor
  if ($screen && $screen->is_block_editor) {
    wp_enqueue_style('editor-style', get_stylesheet_directory_uri() . '/assets/css/editor.css');
  }

  // Enqueue main.css only in the Pattern Library
  if ('appearance_page_edit-wp-patterns' === $hook_suffix) {
    wp_enqueue_style('bootscore-pattern-library-styles', get_stylesheet_directory_uri() . '/assets/css/main.css');
  }
}
add_action('admin_enqueue_scripts', 'bootscore_enqueue_editor_and_pattern_library_styles');


/**
 * Preload Font Awesome
 */
add_filter('style_loader_tag', 'bootscore_fa_preload');

function bootscore_fa_preload($tag) {

  $tag = preg_replace("/id='fontawesome-css'/", "id='fontawesome-css' onload=\"if(media!='all')media='all'\"", $tag);

  return $tag;
}
