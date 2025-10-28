<?php
/**
 * Minimal news slider using existing announcement styles.
 *
 * @package Bootscore
 */

if (!isset($args['query']) || !($args['query'] instanceof WP_Query)) {
  return;
}

/** @var WP_Query $slider_query */
$slider_query = $args['query'];

if (!$slider_query->have_posts()) {
  return;
}

$modifier = isset($args['modifier']) ? (string) $args['modifier'] : '';
$autoplay = isset($args['autoplay']) ? (int) $args['autoplay'] : 0;

$wrapper_classes = ['front-announcements', 'front-announcements--compact'];

if ($modifier !== '') {
  $wrapper_classes[] = 'front-announcements--' . sanitize_html_class($modifier);
}

$loop_slider = $slider_query->post_count > 1;
?>
<div class="<?= esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>" data-announcements>
  <div
    class="front-announcements__slider swiper"
    data-announcements-slider
    data-slider-loop="<?= $loop_slider ? 'true' : 'false'; ?>"
    data-slider-autoplay="<?= $autoplay > 0 ? $autoplay : 0; ?>"
  >
    <div class="swiper-wrapper">
      <?php while ($slider_query->have_posts()) : $slider_query->the_post(); ?>
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
