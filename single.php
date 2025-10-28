<?php
/**
 * Template Post Type: post
 *
 * @package Bootscore
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();
?>

  <div id="content" class="site-content site-content--single">
    <div id="primary" class="content-area">

      <?php do_action('bootscore_after_primary_open', 'single'); ?>

      <?php the_breadcrumb(); ?>

      <main id="main" class="site-main single-page">
        <?php if (have_posts()) : ?>
          <?php while (have_posts()) : the_post(); ?>
            <?php
            $current_post_id = get_the_ID();
            $category_ids    = wp_get_post_categories($current_post_id);
            $views_count     = (int) get_post_meta($current_post_id, 'post_views_count', true);

            $tiles_query_args = [
              'post_type'           => 'post',
              'posts_per_page'      => 3,
              'ignore_sticky_posts' => true,
              'post__not_in'        => [$current_post_id],
            ];

            $slider_query_args = [
              'post_type'           => 'post',
              'posts_per_page'      => 6,
              'ignore_sticky_posts' => true,
              'post__not_in'        => [$current_post_id],
              'no_found_rows'       => true,
            ];

            if (!empty($category_ids)) {
              $tiles_query_args['category__in']  = $category_ids;
              $slider_query_args['category__in'] = $category_ids;
            }

            $tiles_query  = new WP_Query($tiles_query_args);
            $slider_query = new WP_Query($slider_query_args);

            $has_related_tiles = $tiles_query->have_posts();
            $has_slider_posts  = $slider_query->have_posts();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>
              <header class="single-article__header">
                <?php bootscore_category_badge(); ?>

                <h1 class="single-article__title"><?= esc_html(get_the_title()); ?></h1>

                <div class="single-article__meta">
                  <span class="single-article__meta-item">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <time datetime="<?= esc_attr(get_the_date('c')); ?>"><?= esc_html(get_the_date('d.m.Y')); ?></time>
                  </span>

                  <span class="single-article__meta-item">
                    <i class="fa-regular fa-user" aria-hidden="true"></i>
                    <span><?= esc_html(get_the_author()); ?></span>
                  </span>

                  <span class="single-article__meta-item">
                    <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    <span><?= esc_html(number_format_i18n(max($views_count, 0))); ?></span>
                  </span>
                </div>

                <?php bootscore_post_thumbnail(); ?>
              </header>

              <div class="single-article__content">
                <?php the_content(); ?>
              </div>

              <footer class="single-article__footer">
                <?php bootscore_tags(); ?>
              </footer>
            </article>

            <?php if ($has_related_tiles || $has_slider_posts) : ?>
              <section class="single-related" aria-label="<?= esc_attr__('Последние новости', 'bootscore'); ?>">
                <div class="single-related__grid">
                  <div class="single-related__news">
                    <h2 class="single-related__heading"><?= esc_html__('Последние новости', 'bootscore'); ?></h2>

                    <?php if ($has_related_tiles) : ?>
                      <div class="single-related__tiles">
                        <?php while ($tiles_query->have_posts()) : $tiles_query->the_post(); ?>
                          <article <?php post_class('news-tile'); ?>>
                            <a class="news-tile__link" href="<?= esc_url(get_permalink()); ?>">
                              <span class="news-tile__title"><?= esc_html(get_the_title()); ?></span>
                            </a>
                          </article>
                        <?php endwhile; ?>
                      </div>
                      <?php wp_reset_postdata(); ?>
                    <?php endif; ?>
                  </div>

                  <aside class="single-related__sidebar">
                    <?php if ($has_slider_posts) : ?>
                      <?php
                      get_template_part('template-parts/components/news-slider', null, [
                        'query'    => $slider_query,
                        'modifier' => 'single',
                        'autoplay' => 5000,
                      ]);
                      ?>
                      <?php wp_reset_postdata(); ?>
                    <?php endif; ?>
                  </aside>
                </div>
              </section>
            <?php endif; ?>
          <?php endwhile; ?>
        <?php endif; ?>
      </main>
    </div>
  </div>

<?php
get_footer();
