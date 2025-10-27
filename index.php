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
                          <div class="hram-hero-slider__actions">
                            <a class="hram-button hram-hero-slider__button" href="#" role="button"><?= esc_html__('Житие Святого Благоверного Князя Александра Невского', 'bootscore'); ?></a>
                          </div>
                        </div>
                      </div>
                  </div>

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

          <?php
          $service_schedule_block = hram_service_schedule_shortcode([
            'limit' => 5,
          ]);

          if (!empty($service_schedule_block)) {
            echo $service_schedule_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          }
          ?>
          <?php
          $announcements_query = new WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => 3,
            'category_name'       => 'obyavleniya',
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
          ]);

          $news_query = new WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => 4,
            'category_name'       => 'novosti',
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
          ]);

          $has_announcements = $announcements_query->have_posts();
          $has_news          = $news_query->have_posts();

          if ($has_announcements) {
            $announcements_query->rewind_posts();
          }

          if ($has_news) {
            $news_query->rewind_posts();
          }

          if ($has_announcements || $has_news) :
            $front_updates_classes = ['front-updates'];

            if (!$has_announcements) {
              $front_updates_classes[] = 'front-updates--no-announcements';
            }

            if (!$has_news) {
              $front_updates_classes[] = 'front-updates--no-news';
            }

            $announcements_loop = $announcements_query->post_count > 1;
            ?>
            <section class="<?= esc_attr(implode(' ', $front_updates_classes)); ?>">
              <div class="front-updates__grid">
                <?php if ($has_news) : ?>
                  <div class="front-updates__news">
                    <?php
                    while ($news_query->have_posts()) :
                      $news_query->the_post();

                      $news_excerpt = trim(get_the_excerpt());
                      $news_excerpt = $news_excerpt ? wp_trim_words($news_excerpt, 32, '…') : '';
                      $views        = (int) get_post_meta(get_the_ID(), 'post_views_count', true);
                      ?>
                      <article <?php post_class('front-news-card'); ?>>
                        <a class="front-news-card__media" href="<?= esc_url(get_permalink()); ?>">
                          <?php if (has_post_thumbnail()) : ?>
                            <?= wp_get_attachment_image(get_post_thumbnail_id(), 'medium_large', false, [
                              'class'   => 'front-news-card__image',
                              'loading' => 'lazy',
                            ]); ?>
                          <?php else : ?>
                            <span class="front-news-card__placeholder" aria-hidden="true"></span>
                          <?php endif; ?>
                        </a>

                        <div class="front-news-card__content">
                          <h3 class="front-news-card__title">
                            <a class="front-news-card__link" href="<?= esc_url(get_permalink()); ?>">
                              <?= esc_html(get_the_title()); ?>
                            </a>
                          </h3>

                          <?php if (!empty($news_excerpt)) : ?>
                            <p class="front-news-card__excerpt"><?= esc_html($news_excerpt); ?></p>
                          <?php endif; ?>

                          <div class="front-news-card__meta">
                            <span class="front-news-card__meta-item">
                              <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                              <time datetime="<?= esc_attr(get_the_date('c')); ?>"><?= esc_html(get_the_date('d.m.Y')); ?></time>
                            </span>

                            <span class="front-news-card__meta-item">
                              <i class="fa-regular fa-eye" aria-hidden="true"></i>
                              <span><?= esc_html(number_format_i18n(max($views, 0))); ?></span>
                            </span>
                          </div>
                        </div>
                      </article>
                    <?php endwhile; ?>
                  </div>
                  <?php wp_reset_postdata(); ?>
                <?php endif; ?>

                <?php if ($has_announcements) : ?>
                  <div class="front-updates__announcements">
                    <div class="front-announcements" data-announcements>
                      <div class="front-announcements__header">
                        <h2 class="front-announcements__title"><?= esc_html__('Объявления', 'bootscore'); ?></h2>

                        <div class="front-announcements__nav">
                          <button class="front-announcements__nav-btn" type="button" data-announcements-prev aria-label="<?= esc_attr__('Предыдущий слайд', 'bootscore'); ?>">
                            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                          </button>
                          <button class="front-announcements__nav-btn" type="button" data-announcements-next aria-label="<?= esc_attr__('Следующий слайд', 'bootscore'); ?>">
                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                          </button>
                        </div>
                      </div>

                      <div
                        class="front-announcements__slider swiper"
                        data-announcements-slider
                        data-slider-loop="<?= $announcements_loop ? 'true' : 'false'; ?>"
                      >
                        <div class="swiper-wrapper">
                          <?php
                          while ($announcements_query->have_posts()) :
                            $announcements_query->the_post();
                            ?>
                            <div class="swiper-slide front-announcements__slide">
                              <a class="front-announcements__card" href="<?= esc_url(get_permalink()); ?>">
                                <span class="front-announcements__media">
                                  <?php if (has_post_thumbnail()) : ?>
                                    <?= wp_get_attachment_image(get_post_thumbnail_id(), 'medium_large', false, [
                                      'class'   => 'front-announcements__image',
                                      'loading' => 'lazy',
                                    ]); ?>
                                  <?php else : ?>
                                    <span class="front-announcements__placeholder" aria-hidden="true"></span>
                                  <?php endif; ?>
                                </span>

                                <span class="front-announcements__slide-title"><?= esc_html(get_the_title()); ?></span>
                              </a>
                            </div>
                          <?php endwhile; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php wp_reset_postdata(); ?>
                <?php endif; ?>

                <div class="front-updates__calendar">
                  <div class="front-calendar">
                    <div class="title-html-my">Сегодня празднуется</div>
                    <?php // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
                    <div class="title-html-my-2">
                      <center><script type="text/javascript" language="Javascript" src="https://script.pravoslavie.ru/icon.php?scale=1.2"></script></center>
                    </div>
                    <div class="sideblock-my"><script language="Javascript" src="https://script.pravoslavie.ru/calendar.php?hrams=0&amp;target=_blank&amp;bold=1&amp;tipikon=1&amp;saints=1&amp;para=1&amp;short=1"></script></div>
                    <?php // phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
                    <div class="cont"><a target="_blank" href="/segodnya-prazdnuetsya/"><?= esc_html__('Перейти к подробному календарю', 'bootscore'); ?></a></div>
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
