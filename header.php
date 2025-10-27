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

  <?php
  $hram_header_classes = ['site-header', 'hram-header'];

  if (is_front_page()) {
    $hram_header_classes[] = 'hram-header--home';
  }
  ?>

  <header id="masthead" class="<?= esc_attr(implode(' ', $hram_header_classes)); ?>">

    <div class="hram-header__glass">
      <div class="hram-header__container">
        <div class="hram-header__top">
          <div class="hram-header__brand">
            <a class="hram-header__logo" href="<?= esc_url(home_url()); ?>">
              <img src="<?= esc_url(get_template_directory_uri() . '/assets/images/logo.svg'); ?>" alt="<?php bloginfo('name'); ?> Logo" loading="lazy">
            </a>
          </div>

          <div class="hram-header__title-group">
            <span class="hram-header__title-blessing">ПО БЛАГОСЛОВЕНИЮ ВЫСОКОПРЕОСВЯЩЕННЕЙШЕГО ЛОНГИНА МИТРОПОЛИТА СИМБИРСКОГО И НОВОСПАССКОГО</span>
            <span class="hram-header__title">
              <span class="hram-header__title-line">ХРАМ ВО ИМЯ СВЯТОГО БЛАГОВЕРНОГО</span>
              <span class="hram-header__title-line">ВЕЛИКОГО КНЯЗЯ АЛЕКСАНДРА НЕВСКОГО</span>
            </span>
            <span class="hram-header__subtitle">Симбирская Епархия Русской Православной Церкви</span>
          </div>

          <div class="hram-header__socials hram-header__socials--desktop" role="group" aria-label="Социальные сети">
            <a href="https://vk.com" class="hram-header__social-link" target="_blank" rel="noopener" aria-label="ВКонтакте">
              <i class="fa-brands fa-vk" aria-hidden="true"></i>
            </a>
            <a href="https://t.me" class="hram-header__social-link" target="_blank" rel="noopener" aria-label="Telegram">
              <i class="fa-brands fa-telegram-plane" aria-hidden="true"></i>
            </a>
            <a href="tel:+78422000000" class="hram-header__social-link" aria-label="Позвонить">
              <i class="fa-solid fa-phone" aria-hidden="true"></i>
            </a>
            <a href="https://wa.me/78422000000" class="hram-header__social-link" target="_blank" rel="noopener" aria-label="Мессенджер">
              <i class="fa-solid fa-comments" aria-hidden="true"></i>
            </a>
          </div>

          <div class="hram-header__actions">
            <button class="hram-header__toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-navbar" aria-controls="offcanvas-navbar" aria-label="Меню">
              <span class="hram-header__toggler-line"></span>
              <span class="hram-header__toggler-line"></span>
              <span class="hram-header__toggler-line"></span>
            </button>
          </div>
        </div>

        <nav class="hram-header__nav" aria-label="<?= esc_attr__('Основное меню', 'bootscore'); ?>">
          <div class="hram-header__nav-inner">
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
        </nav>
      </div>
    </div>

    <div class="offcanvas offcanvas-start hram-header__offcanvas" tabindex="-1" id="offcanvas-navbar">
      <div class="offcanvas-header">
        <span class="h5 offcanvas-title"><?= __('Меню', 'bootscore'); ?></span>
        <div class="hram-header__offcanvas-tools">
          <div class="hram-header__socials" role="group" aria-label="Социальные сети">
            <a href="https://vk.com" class="hram-header__social-link" target="_blank" rel="noopener" aria-label="ВКонтакте">
              <i class="fa-brands fa-vk" aria-hidden="true"></i>
            </a>
            <a href="https://t.me" class="hram-header__social-link" target="_blank" rel="noopener" aria-label="Telegram">
              <i class="fa-brands fa-telegram-plane" aria-hidden="true"></i>
            </a>
            <a href="tel:+78422000000" class="hram-header__social-link" aria-label="Позвонить">
              <i class="fa-solid fa-phone" aria-hidden="true"></i>
            </a>
            <a href="https://wa.me/78422000000" class="hram-header__social-link" target="_blank" rel="noopener" aria-label="Мессенджер">
              <i class="fa-solid fa-comments" aria-hidden="true"></i>
            </a>
          </div>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="<?= esc_attr__('Закрыть', 'bootscore'); ?>"></button>
        </div>
      </div>
      <div class="offcanvas-body">
        <?php get_template_part('template-parts/header/main-menu'); ?>
      </div>
    </div>

  </header><!-- #masthead -->

  <?php do_action('bootscore_after_masthead'); ?>
