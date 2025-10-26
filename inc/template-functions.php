<?php

/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Bootscore
 * @version 5.3.3
 */


// Exit if accessed directly
defined('ABSPATH') || exit;


/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function bootscore_body_classes($classes) {
  // Adds a class of hfeed to non-singular pages.
  if (!is_singular()) {
    $classes[] = 'hfeed';
  }

  // Adds a class of no-sidebar when there is no sidebar present.
  if (!is_active_sidebar('sidebar-1')) {
    $classes[] = 'no-sidebar';
  }

  return $classes;
}

add_filter('body_class', 'bootscore_body_classes');


/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function bootscore_pingback_header() {
  if (is_singular() && pings_open()) {
    printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
  }
}

add_action('wp_head', 'bootscore_pingback_header');

/**
 * Returns the configured set of social links.
 *
 * @return array[]
 */
function hram_get_social_links_items() {
  $links = [
    [
      'url'    => 'https://vk.com',
      'label'  => __('ВКонтакте', 'bootscore'),
      'icon'   => 'fa-brands fa-vk',
      'target' => '_blank',
      'rel'    => 'noopener noreferrer',
    ],
    [
      'url'    => 'https://t.me',
      'label'  => __('Telegram', 'bootscore'),
      'icon'   => 'fa-brands fa-telegram-plane',
      'target' => '_blank',
      'rel'    => 'noopener noreferrer',
    ],
    [
      'url'    => 'mailto:info@nevsky-simbirsk.ru',
      'label'  => __('Email', 'bootscore'),
      'icon'   => 'fa-regular fa-envelope',
      'target' => '',
      'rel'    => '',
    ],
  ];

  /**
   * Filters the social links before rendering.
   *
   * @param array $links Default links configuration.
   */
  return apply_filters('hram_social_links_items', $links);
}

/**
 * Builds the social links markup.
 *
 * @param array $args Rendering arguments.
 *
 * @return string Rendered markup.
 */
function hram_get_social_links_markup($args = []) {
  $defaults = [
    'container'        => 'div',
    'container_class'  => 'hram-social-links',
    'link_class'       => 'hram-header__contact',
    'show_labels'      => false,
    'echo'             => true,
  ];

  $args = wp_parse_args($args, $defaults);
  $links = hram_get_social_links_items();

  if (empty($links)) {
    return '';
  }

  $link_markup = '';

  foreach ($links as $link) {
    if (empty($link['url']) || empty($link['icon']) || empty($link['label'])) {
      continue;
    }

    $classes = trim($args['link_class'] . ' ' . ($link['class'] ?? ''));

    $attributes = [
      'href'       => esc_url($link['url']),
      'class'      => trim($classes),
      'aria-label' => $link['label'],
    ];

    if (!empty($link['target'])) {
      $attributes['target'] = $link['target'];
    }

    if (!empty($link['rel'])) {
      $attributes['rel'] = $link['rel'];
    }

    $attribute_string = '';
    foreach ($attributes as $attr_name => $attr_value) {
      if ('' === $attr_value) {
        continue;
      }

      $attribute_string .= sprintf(' %s="%s"', esc_attr($attr_name), esc_attr($attr_value));
    }

    $icon  = sprintf('<i class="%s" aria-hidden="true"></i>', esc_attr($link['icon']));
    $label = '';

    if ($args['show_labels']) {
      $label = sprintf('<span class="hram-social-links__label">%s</span>', esc_html($link['label']));
    }

    $link_markup .= sprintf('<a%s>%s%s</a>', $attribute_string, $icon, $label);
  }

  if ('' === $link_markup) {
    return '';
  }

  if (false === $args['container'] || '' === $args['container']) {
    $output = $link_markup;
  } else {
    $allowed_containers = ['div', 'nav', 'section', 'span'];
    $tag                = strtolower($args['container']);

    if (!in_array($tag, $allowed_containers, true)) {
      $tag = 'div';
    }

    $class_attribute = trim($args['container_class']);
    $output          = sprintf('<%1$s%2$s>%3$s</%1$s>', $tag, $class_attribute ? ' class="' . esc_attr($class_attribute) . '"' : '', $link_markup);
  }

  if (!$args['echo']) {
    return $output;
  }

  echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

  return $output;
}

/**
 * Echoes the social links markup.
 *
 * @param array $args Rendering arguments.
 *
 * @return string
 */
function hram_social_links($args = []) {
  $args['echo'] = true;

  return hram_get_social_links_markup($args);
}

/**
 * Shortcode wrapper for displaying social links in content areas.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string
 */
function hram_social_links_shortcode($atts) {
  $atts = shortcode_atts(
    [
      'class'       => 'hram-social-links',
      'show_labels' => 'false',
    ],
    $atts,
    'hram_social_links'
  );

  $classes = array_filter(array_map('sanitize_html_class', explode(' ', $atts['class'])));

  return hram_get_social_links_markup([
    'container_class' => implode(' ', $classes),
    'show_labels'     => filter_var($atts['show_labels'], FILTER_VALIDATE_BOOLEAN),
    'echo'            => false,
  ]);
}

add_shortcode('hram_social_links', 'hram_social_links_shortcode');

