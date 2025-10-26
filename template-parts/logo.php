<?php
/**
 * Site logo component.
 */

defined('ABSPATH') || exit();

$logo_classes = array('site-logo');

if (!empty($args['class'])) {
    if (is_array($args['class'])) {
        $extra_classes = $args['class'];
    } else {
        $extra_classes = preg_split('/\s+/', (string) $args['class']);
    }

    foreach ($extra_classes as $class) {
        $sanitized = sanitize_html_class($class);
        if (!empty($sanitized)) {
            $logo_classes[] = $sanitized;
        }
    }
}

$logo_classes = array_unique(array_filter($logo_classes));
$logo_url      = esc_url(get_template_directory_uri() . '/assets/images/logo.svg');
?>
<a href="<?php echo esc_url(home_url()); ?>" class="<?php echo esc_attr(implode(' ', $logo_classes)); ?>">
  <img src="<?php echo $logo_url; ?>" alt="Храм во имя Святого Благоверного Князя Александра Невского" loading="lazy">
</a>
