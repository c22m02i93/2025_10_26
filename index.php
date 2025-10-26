<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 * @version 6.3.0
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

get_header();

$hero_post    = null;

$slider_query = new WP_Query([
  'post_type'      => 'home_slider',
  'posts_per_page' => 1,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
]);

$slider_slides = [];

if ($slider_query->have_posts()) {
  while ($slider_query->have_posts()) {
    $slider_query->the_post();

    if (!$hero_post) {
      $hero_post = get_post();
    }

    $slides = get_post_meta(get_the_ID(), 'home_slider_slides', true);

    if (is_array($slides) && !empty($slides)) {
      foreach ($slides as $attachment_id) {
        $attachment_id = (int) $attachment_id;

        if (!$attachment_id) {
          continue;
        }

        $image_url = wp_get_attachment_image_url($attachment_id, 'full');

        if (!$image_url) {
          continue;
        }

        $slider_slides[] = [
          'url'    => $image_url,
          'srcset' => wp_get_attachment_image_srcset($attachment_id, 'full'),
          'sizes'  => wp_get_attachment_image_sizes($attachment_id, 'full'),
          'alt'    => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
          'id'     => $attachment_id,
        ];
      }
    }

    break;
  }

  wp_reset_postdata();
}

$hero_image = $slider_slides[0] ?? [];
$hero_title = __('Житие Александра Невского', 'bootscore');
$hero_text  = '';

if ($hero_post instanceof WP_Post) {
  $excerpt   = has_excerpt($hero_post->ID) ? get_the_excerpt($hero_post) : wp_strip_all_tags($hero_post->post_content);
  $hero_text = wp_trim_words($excerpt, 28, '…');
}

$slider_has_loop = count($slider_slides) > 1;
?>

  <div id="content" class="site-content">
    <div id="primary" class="content-area">
      <?php do_action('bootscore_after_primary_open', 'index'); ?>

      <main id="main" class="site-main">
        <?php if (is_front_page()) : ?>
          <?php if (!empty($slider_slides)) : ?>
            <?php
            $slider_classes = ['swiper', 'hram-hero-slider__swiper'];

            if (!$slider_has_loop) {
              $slider_classes[] = 'hram-hero-slider__swiper--single';
            }

            $slider_autoplay_delay = 6500;
            ?>

            <section class="hram-hero-slider" aria-label="<?= esc_attr__('Основные истории прихода', 'bootscore'); ?>">
              <div class="hram-hero-slider__inner">
                <div
                  class="<?= esc_attr(implode(' ', $slider_classes)); ?>"
                  data-slider-loop="<?= $slider_has_loop ? 'true' : 'false'; ?>"
                  data-slider-autoplay="<?= $slider_has_loop ? (int) $slider_autoplay_delay : 0; ?>"
                >
                  <div class="swiper-wrapper">
                    <?php foreach ($slider_slides as $slide) : ?>
                      <div class="swiper-slide">
                        <figure class="hram-hero-slider__figure" data-swiper-parallax-scale="1.08" data-swiper-parallax-duration="1200">
                          <img
                            class="hram-hero-slider__image"
                            src="<?= esc_url($slide['url']); ?>"
                            <?php if (!empty($slide['srcset'])) : ?>srcset="<?= esc_attr($slide['srcset']); ?>"<?php endif; ?>
                            <?php if (!empty($slide['sizes'])) : ?>sizes="<?= esc_attr($slide['sizes']); ?>"<?php endif; ?>
                            alt="<?= esc_attr($slide['alt']); ?>"
                            loading="eager"
                            data-swiper-parallax="25%"
                          >
                        </figure>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <div class="hram-hero-slider__overlay" data-parallax-scroll="28">
                    <div class="hram-hero-slider__content" data-swiper-parallax="-120">
                      <div class="hram-hero-slider__content-inner" data-parallax-scroll="16">
                        <h1 class="hram-hero-slider__title"><?= esc_html($hero_title); ?></h1>

                        <?php if (!empty($hero_text)) : ?>
                          <p class="hram-hero-slider__description"><?= esc_html($hero_text); ?></p>
                        <?php endif; ?>

                        <div class="hram-hero-slider__actions">
                          <a class="hram-button hram-hero-slider__button" href="#" role="button"><?= esc_html__('Житие Святого Благоверного Князя Александра Невского', 'bootscore'); ?></a>
                        </div>
                      </div>
                    </div>
                  </div>

                  <?php if ($slider_has_loop) : ?>
                    <div class="hram-hero-slider__pagination swiper-pagination" aria-hidden="true"></div>
                  <?php endif; ?>
                </div>
              </div>
            </section>
          <?php else : ?>
            <section class="hram-hero">
              <div class="hram-hero__inner container">
                <?php if (!empty($hero_image['url'])) : ?>
                  <div class="hram-hero__media">
                    <figure class="hram-hero__figure">
                      <img
                        class="hram-hero__image"
                        src="<?= esc_url($hero_image['url']); ?>"
                        <?php if (!empty($hero_image['srcset'])) : ?>srcset="<?= esc_attr($hero_image['srcset']); ?>"<?php endif; ?>
                        <?php if (!empty($hero_image['sizes'])) : ?>sizes="<?= esc_attr($hero_image['sizes']); ?>"<?php endif; ?>
                        alt="<?= esc_attr($hero_image['alt']); ?>"
                        loading="eager"
                      >
                  </figure>
                </div>
              <?php endif; ?>

              <div class="hram-hero__content">
                <h1 class="hram-hero__title"><?= esc_html($hero_title); ?></h1>

                <?php if (!empty($hero_text)) : ?>
                  <p class="hram-hero__description"><?= esc_html($hero_text); ?></p>
                <?php endif; ?>

                <div class="hram-hero__actions">
                  <a class="hram-button" href="#" role="button"><?= esc_html__('Читать житие', 'bootscore'); ?></a>
                  <a class="hram-button" href="#" role="button"><?= esc_html__('Смотреть видео', 'bootscore'); ?></a>
                </div>
              </div>
              </div>
            </section>
          <?php endif; ?>
        <?php else : ?>
          <section class="hram-hero">
            <div class="hram-hero__inner container">
              <?php if (!empty($hero_image['url'])) : ?>
                <div class="hram-hero__media">
                  <figure class="hram-hero__figure">
                    <img
                      class="hram-hero__image"
                      src="<?= esc_url($hero_image['url']); ?>"
                      <?php if (!empty($hero_image['srcset'])) : ?>srcset="<?= esc_attr($hero_image['srcset']); ?>"<?php endif; ?>
                      <?php if (!empty($hero_image['sizes'])) : ?>sizes="<?= esc_attr($hero_image['sizes']); ?>"<?php endif; ?>
                      alt="<?= esc_attr($hero_image['alt']); ?>"
                      loading="eager"
                    >
                </figure>
              </div>
            <?php endif; ?>

            <div class="hram-hero__content">
              <h1 class="hram-hero__title"><?= esc_html($hero_title); ?></h1>

              <?php if (!empty($hero_text)) : ?>
                <p class="hram-hero__description"><?= esc_html($hero_text); ?></p>
              <?php endif; ?>

              <div class="hram-hero__actions">
                <a class="hram-button" href="#" role="button"><?= esc_html__('Читать житие', 'bootscore'); ?></a>
                <a class="hram-button" href="#" role="button"><?= esc_html__('Смотреть видео', 'bootscore'); ?></a>
              </div>
            </div>
            </div>
          </section>
        <?php endif; ?>
      </main><!-- #main -->
    </div><!-- #primary -->
  </div><!-- #content -->
<?php
get_footer();
