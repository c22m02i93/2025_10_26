<?php
/**
 * Template Name: Слово архипастыря (парсер)
 *
 * @package Bootscore
 */

defined('ABSPATH') || exit;

get_header();

$threshold = bootscore_remote_get_november_threshold();
$sermons   = bootscore_remote_get_posts(
  'https://mitropolia-simbirsk.ru/category/mitropolit/slovo-arhipastyrya',
  [
    'limit'        => 30,
    'threshold'    => $threshold,
    'source_label' => __('Митрополит — Слово архипастыря', 'bootscore'),
  ]
);
?>
  <div id="content" class="site-content <?= apply_filters('bootscore/class/container', 'container', 'page'); ?> <?= apply_filters('bootscore/class/content/spacer', 'pt-4 pb-5', 'page'); ?>">
    <div id="primary" class="content-area">

      <?php do_action('bootscore_after_primary_open', 'page'); ?>

      <div class="row">
        <div class="<?= apply_filters('bootscore/class/main/col', 'col'); ?>">

          <main id="main" class="site-main">
            <?php while (have_posts()) : the_post(); ?>
              <article id="post-<?= get_the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                  <?php do_action('bootscore_before_title', 'page'); ?>
                  <?php the_title('<h1 class="entry-title ' . apply_filters('bootscore/class/entry/title', '', 'page') . '">', '</h1>'); ?>
                  <?php do_action('bootscore_after_title', 'page'); ?>
                  <?php bootscore_post_thumbnail(); ?>
                </header>

                <?php do_action('bootscore_after_featured_image', 'page'); ?>

                <div class="entry-content">
                  <?php if (!empty($sermons)) : ?>
                    <div class="d-grid gap-4">
                      <?php foreach ($sermons as $sermon) : ?>
                        <article class="card horizontal remote-sermon">
                          <div class="row g-0 align-items-stretch">
                            <?php if (!empty($sermon['image_url'])) : ?>
                              <div class="col-lg-4 col-xl-3">
                                <a class="d-block h-100 overflow-hidden" href="<?= esc_url($sermon['link']); ?>" target="_blank" rel="noopener noreferrer external">
                                  <img
                                    class="img-fluid h-100 w-100 object-fit-cover"
                                    src="<?= esc_url($sermon['image_url']); ?>"
                                    alt="<?= esc_attr($sermon['image_alt'] ?? ''); ?>"
                                    loading="lazy"
                                  >
                                </a>
                              </div>
                            <?php endif; ?>

                            <div class="col">
                              <div class="card-body">
                                <p class="meta small mb-2 text-body-secondary">
                                  <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                  <time datetime="<?= esc_attr($sermon['datetime_attr'] ?? ''); ?>">
                                    <?= esc_html($sermon['date_display'] ?? ''); ?>
                                  </time>
                                </p>

                                <h2 class="card-title h4">
                                  <a class="text-body text-decoration-none" href="<?= esc_url($sermon['link']); ?>" target="_blank" rel="noopener noreferrer external">
                                    <?= esc_html($sermon['title']); ?>
                                  </a>
                                </h2>

                                <?php if (!empty($sermon['excerpt'])) : ?>
                                  <p class="card-text"><?= esc_html($sermon['excerpt']); ?></p>
                                <?php endif; ?>

                                <p class="card-text mb-0">
                                  <a class="read-more" href="<?= esc_url($sermon['link']); ?>" target="_blank" rel="noopener noreferrer external">
                                    <?= esc_html__('Читать на mitropolia-simbirsk.ru', 'bootscore'); ?>
                                  </a>
                                </p>
                              </div>
                            </div>
                          </div>
                        </article>
                      <?php endforeach; ?>
                    </div>
                  <?php else : ?>
                    <p class="lead mb-0"><?= esc_html__('Публикаций после 1 ноября пока не найдено.', 'bootscore'); ?></p>
                  <?php endif; ?>
                </div>

                <?php do_action('bootscore_before_entry_footer', 'page'); ?>

                <div class="entry-footer">
                  <?php comments_template(); ?>
                </div>
              </article>
            <?php endwhile; ?>
          </main>

        </div>
        <?php get_sidebar(); ?>
      </div>

    </div>
  </div>
<?php
get_footer();
