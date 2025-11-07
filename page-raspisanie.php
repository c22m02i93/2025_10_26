<?php
/**
 * Template Name: Расписание архипастырских служб
 *
 * @package Bootscore
 */

defined('ABSPATH') || exit;

get_header();

$threshold = bootscore_remote_get_november_threshold();
$services  = bootscore_remote_get_posts(
  'https://mitropolia-simbirsk.ru/category/mitropoliya/arhipastyrskoe-sluzhenie',
  [
    'limit'        => 40,
    'threshold'    => $threshold,
    'source_label' => __('Митрополия — Архипастырское служение', 'bootscore'),
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
                  <?php if (!empty($services)) : ?>
                    <div class="d-grid gap-4">
                      <?php foreach ($services as $service) : ?>
                        <?= bootscore_render_news_card($service, [
                          'show_views'        => false,
                          'show_source_badge' => true,
                        ]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                      <?php endforeach; ?>
                    </div>
                  <?php else : ?>
                    <p class="lead mb-0"><?= esc_html__('Архипастырских служб, опубликованных после 1 ноября, пока нет.', 'bootscore'); ?></p>
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
