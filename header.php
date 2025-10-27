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

  <?php
  $header_classes = array('site-header', 'hram-header');

  if (is_front_page()) {
    $header_classes[] = 'hram-header--home';
  }
  ?>

  <header id="masthead" class="<?= esc_attr(implode(' ', array_map('sanitize_html_class', $header_classes))); ?>">

    <!-- Благословение -->
    <div class="hram-header__blessing">
      <div class="hram-header__blessing-inner">
        <span><?= esc_html__('По благословению Высокопреосвященнейшего Лонгина митрополита Симбирского и Новоспасского', 'bootscore'); ?></span>
      </div>
    </div>

    <!-- Основная панель -->
    <div class="hram-header__main">
      <div class="hram-header__main-inner">

        <div class="hram-header__identity">
          <?php get_template_part('template-parts/logo', null, array('class' => 'hram-header__identity-link')); ?>

          <div class="hram-header__identity-text">
            <div class="hram-header__identity-heading">
              <span><?= esc_html__('ХРАМ ВО ИМЯ СВЯТОГО ПРЕПОДОБНОГО ВЕЛИКОГО КНЯЗЯ', 'bootscore'); ?></span>
              <span><?= esc_html__('АЛЕКСАНДРА НЕВСКОГО', 'bootscore'); ?></span>
            </div>
            <div class="hram-header__identity-line hram-header__identity-line--accent">
              <?= esc_html__('СИМБИРСКАЯ ЕПАРХИЯ РУССКОЙ ПРАВОСЛАВНОЙ ЦЕРКВИ', 'bootscore'); ?>
            </div>
          </div>
        </div>

        <div class="hram-header__contacts" role="navigation" aria-label="<?= esc_attr__('Социальные сети и контакты', 'bootscore'); ?>">
          <?php
          hram_social_links(
            array(
              'container'  => false,
              'link_class' => 'hram-header__contact'
            )
          );
          ?>
        </div>
      </div>
    </div>

    <?php if (has_nav_menu('main-menu')) : ?>
      <nav class="hram-header__nav-bar" aria-label="<?= esc_attr__('Основное меню', 'bootscore'); ?>">
        <div class="hram-header__nav-bar-inner">
          <?php get_template_part('template-parts/header/main-menu'); ?>
        </div>
      </nav>
    <?php endif; ?>

  </header><!-- #masthead -->

  <?php do_action('bootscore_after_masthead'); ?>
