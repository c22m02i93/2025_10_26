<?php
/**
 * Header template
 * Custom Hram version for Bootscore
 */

defined('ABSPATH') || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

  <a class="skip-link visually-hidden-focusable" href="#primary"><?php esc_html_e('Skip to content', 'bootscore'); ?></a>

  <?php $is_home_header = is_front_page(); ?>

  <header id="masthead" class="site-header hram-header<?php echo $is_home_header ? ' hram-header--home' : ''; ?>">

    <?php if (!$is_home_header) : ?>
      <div class="hram-header__blessing">
        <span>по благословению митрополита Симбирского и Новоспасского Лонгина</span>
      </div>
    <?php endif; ?>

    <!-- Мобильная панель -->
    <?php
    $mobile_bar_classes = 'hram-header__mobile-bar container-fluid px-3 d-lg-none';
    if ($is_home_header) {
      $mobile_bar_classes .= ' hram-header__mobile-bar--solo';
    }
    ?>
    <div class="<?php echo esc_attr($mobile_bar_classes); ?>">
      <a class="hram-header__mobile-logo" href="<?= esc_url(home_url()); ?>">
        <img src="<?= esc_url('http://nevsky-simbirsk.ru/wp-content/uploads/2025/10/hapka-1.svg'); ?>" alt="<?php bloginfo('name'); ?> Logo" loading="lazy">
      </a>
      <?php if (!$is_home_header) : ?>
        <button class="hram-header__toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-navbar" aria-controls="offcanvas-navbar" aria-label="Меню">
          <span class="hram-header__toggler-line"></span>
          <span class="hram-header__toggler-line"></span>
          <span class="hram-header__toggler-line"></span>
        </button>
      <?php endif; ?>
    </div>

    <!-- Основная панель -->
    <?php
    $main_bar_classes = 'hram-header__main container-fluid px-3 px-lg-5 d-none d-lg-flex';
    if ($is_home_header) {
      $main_bar_classes .= ' hram-header__main--solo';
    }
    ?>
    <div class="<?php echo esc_attr($main_bar_classes); ?>">
      <div class="hram-header__identity">
        <a class="hram-header__identity-link" href="<?= esc_url(home_url()); ?>">
          <img src="<?= esc_url('http://nevsky-simbirsk.ru/wp-content/uploads/2025/10/hapka-1.svg'); ?>" alt="<?php bloginfo('name'); ?> Logo" loading="lazy">
        </a>
      </div>

      <?php if (!$is_home_header) : ?>
        <div class="hram-header__contacts hram-social-links hram-social-links--end">
          <?php hram_social_links(array('container' => false)); ?>
          <a href="tel:+78422000000" class="hram-header__contact hram-header__contact--phone" aria-label="Позвонить">
            <i class="fa-solid fa-phone" aria-hidden="true"></i>
            <span>+7 (8422) 00-00-00</span>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <?php if (!$is_home_header) : ?>
      <!-- Разделительная линия -->
      <div class="hram-header__divider container-fluid px-3 px-lg-5">
        <div class="hram-header__divider-line"></div>
      </div>

      <!-- Меню -->
      <div class="hram-header__menu container-fluid px-3 px-lg-5 d-none d-lg-flex">
        <?php
        wp_nav_menu(array(
          'theme_location' => 'main-menu',
          'container'      => false,
          'menu_class'     => 'hram-header__menu-list',
          'fallback_cb'    => '__return_false',
          'depth'          => 2,
          'walker'         => new bootstrap_5_wp_nav_menu_walker(),
        ));
        ?>
      </div>

      <!-- Offcanvas меню -->
      <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas-navbar">
        <div class="offcanvas-header">
          <span class="h5 offcanvas-title"><?= __('Меню', 'bootscore'); ?></span>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
        </div>
        <div class="offcanvas-body">
          <div class="hram-header__offcanvas-contacts d-lg-none hram-social-links">
            <?php hram_social_links(array('container' => false)); ?>
            <a href="tel:+78422000000" class="hram-header__contact hram-header__contact--phone">
              <i class="fa-solid fa-phone" aria-hidden="true"></i>
              <span>+7 (8422) 00-00-00</span>
            </a>
          </div>

          <?php get_template_part('template-parts/header/main-menu'); ?>
        </div>
      </div>
    <?php endif; ?>

  </header><!-- #masthead -->

  <?php do_action('bootscore_after_masthead'); ?>
