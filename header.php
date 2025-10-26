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

  <header id="masthead" class="site-header hram-header">

    <!-- Благословение -->
    <div class="hram-header__blessing text-center py-2">
      <span><?= esc_html__('По благословению Высокопреосвященнейшего Лонгина, митрополита Симбирского и Новоспасского', 'bootscore'); ?></span>
    </div>

    <!-- Основная панель -->
    <div class="hram-header__main container-fluid px-3 px-lg-5 py-3 d-flex align-items-center flex-wrap justify-content-between">

      <!-- Логотип -->
      <div class="hram-header__logo d-flex align-items-center">
        <?php get_template_part('template-parts/logo', null, array('class' => 'hram-header__identity-link')); ?>
      </div>

      <!-- Название храма -->
      <div class="hram-header__identity-text flex-grow-1 ps-lg-4 text-center text-lg-start">
        <div class="hram-header__identity-heading">
          <div class="fw-bold h5 mb-0"><?= esc_html__('ХРАМ ВО ИМЯ СВЯТОГО ПРЕПОДОБНОГО ВЕЛИКОГО КНЯЗЯ', 'bootscore'); ?></div>
          <div class="fw-bold h4 text-uppercase mb-0"><?= esc_html__('АЛЕКСАНДРА НЕВСКОГО', 'bootscore'); ?></div>
        </div>
        <div class="hram-header__identity-line hram-header__identity-line--accent small text-muted">
          <?= esc_html__('СИМБИРСКАЯ ЕПАРХИЯ РУССКОЙ ПРАВОСЛАВНОЙ ЦЕРКВИ', 'bootscore'); ?>
        </div>
      </div>

    </div>

    <!-- Соцсети и контакты -->
    <div class="hram-header__contacts container-fluid px-3 px-lg-5 pb-3 d-flex align-items-center gap-3 flex-wrap justify-content-start">
      <?php hram_social_links(array('container' => false)); ?>
      <a href="tel:+78422000000" class="hram-header__contact hram-header__contact--phone d-flex align-items-center text-decoration-none">
        <i class="fa-solid fa-phone me-2" aria-hidden="true"></i>
        <span>+7 (8422) 00-00-00</span>
      </a>
    </div>

  </header><!-- #masthead -->

  <?php do_action('bootscore_after_masthead'); ?>
