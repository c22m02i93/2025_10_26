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
  <?php $theme_logo_url = esc_url(get_template_directory_uri() . '/assets/images/logo.svg'); ?>
  <link rel="icon" href="<?php echo $theme_logo_url; ?>" type="image/svg+xml">
  <meta property="og:image" content="<?php echo $theme_logo_url; ?>">
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
        <span><?= esc_html__('По благословению Высокопреосвященнейшего Лонгина митрополита Симбирского и Новоспасского', 'bootscore'); ?></span>
      </div>
    <?php endif; ?>

    <!-- Мобильная панель -->
    <?php
    $mobile_bar_classes = 'hram-header__mobile-bar container-fluid px-3 d-lg-none';

    if (!$is_home_header) :
    ?>
      <div class="<?php echo esc_attr($mobile_bar_classes); ?>">
        <?php get_template_part('template-parts/logo', null, array('class' => 'hram-header__mobile-logo')); ?>
        <button class="hram-header__toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-navbar" aria-controls="offcanvas-navbar" aria-label="Меню">
          <span class="hram-header__toggler-line"></span>
          <span class="hram-header__toggler-line"></span>
          <span class="hram-header__toggler-line"></span>
        </button>
      </div>
    <?php endif; ?>

    <!-- Основная панель -->
    <?php
    $main_bar_classes = 'hram-header__main container-fluid px-3 px-lg-5';
    if (!$is_home_header) {
      $main_bar_classes .= ' d-none d-lg-flex';
    }

    $has_main_menu = has_nav_menu('main-menu');
    ?>
    <div class="<?php echo esc_attr($main_bar_classes); ?>">
      <?php if ($is_home_header) : ?>
        <div class="hram-header__main-inner">
          <div class="hram-header__top">
            <div class="hram-header__identity">
              <div class="hram-header__identity-text">
                <span class="hram-header__identity-blessing"><?= esc_html__('По благословению Высокопреосвященнейшего Лонгина, митрополита Симбирского и Новоспасского', 'bootscore'); ?></span>
                <span class="hram-header__identity-heading">
                  <span><?= esc_html__('ХРАМ ВО ИМЯ СВЯТОГО ПРЕПОДОБНОГО ВЕЛИКОГО КНЯЗЯ', 'bootscore'); ?></span>
                  <span><?= esc_html__('АЛЕКСАНДРА НЕВСКОГО', 'bootscore'); ?></span>
                </span>
                <span class="hram-header__identity-line hram-header__identity-line--accent"><?= esc_html__('СИМБИРСКАЯ ЕПАРХИЯ РУССКОЙ ПРАВОСЛАВНОЙ ЦЕРКВИ', 'bootscore'); ?></span>
              </div>
              <?php get_template_part('template-parts/logo', null, array('class' => 'hram-header__identity-link')); ?>
            </div>
            <div class="hram-header__contacts hram-social-links hram-header__contacts--home">
              <?php hram_social_links(array('container' => false)); ?>
            </div>
          </div>
          <?php if ($has_main_menu) : ?>
            <nav class="hram-header__nav" aria-label="<?= esc_attr__('Основное меню', 'bootscore'); ?>">
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
            </nav>
          <?php endif; ?>
        </div>
      <?php else : ?>
        <div class="hram-header__identity">
          <?php get_template_part('template-parts/logo', null, array('class' => 'hram-header__identity-link')); ?>

          <div class="hram-header__identity-text">
            <span class="hram-header__identity-heading">
              <span><?= esc_html__('ХРАМ ВО ИМЯ СВЯТОГО ПРЕПОДОБНОГО ВЕЛИКОГО КНЯЗЯ', 'bootscore'); ?></span>
              <span><?= esc_html__('АЛЕКСАНДРА НЕВСКОГО', 'bootscore'); ?></span>
            </span>
            <span class="hram-header__identity-line hram-header__identity-line--accent"><?= esc_html__('СИМБИРСКАЯ ЕПАРХИЯ РУССКОЙ ПРАВОСЛАВНОЙ ЦЕРКВИ', 'bootscore'); ?></span>
          </div>
        </div>

        <div class="hram-header__contacts hram-social-links hram-social-links--end">
          <?php hram_social_links(array('container' => false)); ?>
          <a href="tel:+78422000000" class="hram-header__contact hram-header__contact--phone" aria-label="Позвонить">
            <i class="fa-solid fa-phone" aria-hidden="true"></i>
            <span>+7 (8422) 00-00-00</span>
          </a>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($is_home_header && $has_main_menu) : ?>
      <!-- Меню выводится в шапке -->
    <?php endif; ?>

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
