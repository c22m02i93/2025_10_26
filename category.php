<?php
/**
 * Template for category archives.
 *
 * @package Bootscore
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$category_object      = get_queried_object();
$category_title       = '';
$category_description = get_the_archive_description();

if ($category_object instanceof WP_Term) {
  $category_title = $category_object->name;
} else {
  $category_title = get_the_archive_title();
}

$slider_query_args = [
  'post_type'           => 'post',
  'posts_per_page'      => 6,
  'ignore_sticky_posts' => true,
  'no_found_rows'       => true,
];

if ($category_object instanceof WP_Term) {
  $slider_query_args['cat'] = $category_object->term_id;
}

$slider_query = new WP_Query($slider_query_args);
?>

  <div id="content" class="site-content site-content--rubric">
    <div id="primary" class="content-area">

      <?php do_action('bootscore_after_primary_open', 'category'); ?>

      <?php the_breadcrumb(); ?>

      <main id="main" class="site-main rubric-page">
        <header class="rubric-page__header">
          <h1 class="rubric-page__title"><?= esc_html($category_title); ?></h1>

          <?php if (!empty($category_description)) : ?>
            <div class="rubric-page__description"><?= wp_kses_post($category_description); ?></div>
          <?php endif; ?>
        </header>

        <div class="rubric-page__layout">
          <div class="rubric-page__posts">
            <?php if (have_posts()) : ?>
              <div class="rubric-page__posts-grid">
                <?php
                while (have_posts()) :
                  the_post();

                  $excerpt = trim(get_the_excerpt());
                  $excerpt = $excerpt ? wp_trim_words($excerpt, 32, '…') : '';
                  $views   = (int) get_post_meta(get_the_ID(), 'post_views_count', true);
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
                      <?php
                      $post_categories        = get_the_category();
                      $post_primary_category = $post_categories ? $post_categories[0] : null;

                      if ($post_primary_category instanceof WP_Term) :
                        ?>
                        <div class="front-news-card__category">
                          <a class="front-news-card__category-link" href="<?= esc_url(get_category_link($post_primary_category->term_id)); ?>">
                            <?= esc_html($post_primary_category->name); ?>
                          </a>
                        </div>
                      <?php endif; ?>

                      <h2 class="front-news-card__title">
                        <a class="front-news-card__link" href="<?= esc_url(get_permalink()); ?>">
                          <?= esc_html(get_the_title()); ?>
                        </a>
                      </h2>

                      <?php if (!empty($excerpt)) : ?>
                        <p class="front-news-card__excerpt"><?= esc_html($excerpt); ?></p>
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

              <?php
              $pagination_links = paginate_links([
                'type'      => 'array',
                'prev_text' => __('Назад', 'bootscore'),
                'next_text' => __('Вперед', 'bootscore'),
              ]);

              if (!empty($pagination_links)) :
                ?>
                <nav class="rubric-pagination" aria-label="<?= esc_attr__('Навигация по страницам', 'bootscore'); ?>">
                  <ul class="rubric-pagination__list">
                    <?php foreach ($pagination_links as $link) : ?>
                      <li class="rubric-pagination__item"><?= wp_kses_post($link); ?></li>
                    <?php endforeach; ?>
                  </ul>
                </nav>
              <?php endif; ?>
            <?php else : ?>
              <div class="rubric-page__empty">
                <p><?= esc_html__('Записей не найдено.', 'bootscore'); ?></p>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($slider_query->have_posts()) : ?>
            <aside class="rubric-page__sidebar">
              <?php
              $slider_query->rewind_posts();
              get_template_part('template-parts/components/news-slider', null, [
                'query'    => $slider_query,
                'modifier' => 'rubric',
                'autoplay' => 5000,
              ]);

              wp_reset_postdata();
              ?>
            </aside>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

<?php
get_footer();
