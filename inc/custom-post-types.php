<?php

// Exit if accessed directly
\defined('ABSPATH') || exit;

/**
 * Register custom post types and related meta.
 */

add_action('init', 'hram_register_home_slider_post_type');
/**
 * Registers the "Слайдер на главной" custom post type.
 *
 * @return void
 */
function hram_register_home_slider_post_type() {
    $labels = [
        'name'                  => __('Слайдер на главной', 'bootscore'),
        'singular_name'         => __('Слайд', 'bootscore'),
        'menu_name'             => __('Слайдер на главной', 'bootscore'),
        'name_admin_bar'        => __('Слайд', 'bootscore'),
        'add_new'               => __('Добавить слайд', 'bootscore'),
        'add_new_item'          => __('Добавить новый слайд', 'bootscore'),
        'new_item'              => __('Новый слайд', 'bootscore'),
        'edit_item'             => __('Редактировать слайд', 'bootscore'),
        'view_item'             => __('Просмотреть слайд', 'bootscore'),
        'all_items'             => __('Все слайды', 'bootscore'),
        'search_items'          => __('Искать слайды', 'bootscore'),
        'not_found'             => __('Слайды не найдены.', 'bootscore'),
        'not_found_in_trash'    => __('В корзине слайдов не найдено.', 'bootscore'),
        'featured_image'        => __('Изображение слайда', 'bootscore'),
        'set_featured_image'    => __('Задать изображение слайда', 'bootscore'),
        'remove_featured_image' => __('Удалить изображение слайда', 'bootscore'),
        'use_featured_image'    => __('Использовать как изображение слайда', 'bootscore'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'supports'           => ['title'],
        'menu_icon'          => 'dashicons-images-alt2',
        'rewrite'            => [
            'slug'       => 'home-slider',
            'with_front' => false,
        ],
        'has_archive'        => false,
        'publicly_queryable' => false,
        'exclude_from_search'=> true,
    ];

    register_post_type('home_slider', $args);
}

add_action('init', 'hram_register_home_slider_meta');
/**
 * Registers slides meta for the home slider post type.
 *
 * @return void
 */
function hram_register_home_slider_meta() {
    $meta_args = [
        'type'              => 'array',
        'single'            => true,
        'show_in_rest'      => [
            'schema' => [
                'type'  => 'array',
                'items' => [
                    'type' => 'integer',
                ],
            ],
        ],
        'sanitize_callback' => 'hram_sanitize_home_slider_slides',
        'auth_callback'     => 'bootscore_service_meta_cap_check',
    ];

    register_post_meta('home_slider', 'home_slider_slides', $meta_args);
}

add_filter('use_block_editor_for_post_type', 'hram_disable_home_slider_block_editor', 10, 2);
/**
 * Disables the block editor for the home slider post type.
 *
 * @param bool   $use_block_editor Whether the block editor should be used.
 * @param string $post_type        Current post type.
 *
 * @return bool
 */
function hram_disable_home_slider_block_editor($use_block_editor, $post_type) {
    if ('home_slider' === $post_type) {
        return false;
    }

    return $use_block_editor;
}

/**
 * Sanitizes an array of attachment IDs for the slider.
 *
 * @param mixed $value Raw meta value.
 *
 * @return array
 */
function hram_sanitize_home_slider_slides($value) {
    if (!is_array($value)) {
        return [];
    }

    $sanitized = array_map('absint', $value);
    $sanitized = array_filter($sanitized);

    return array_values(array_unique($sanitized));
}

add_action('add_meta_boxes', 'hram_home_slider_add_meta_box');
/**
 * Adds the slides meta box for the home slider post type.
 *
 * @return void
 */
function hram_home_slider_add_meta_box() {
    add_meta_box(
        'hram-home-slider-slides',
        __('Слайды', 'bootscore'),
        'hram_home_slider_slides_metabox',
        'home_slider',
        'normal',
        'high'
    );
}

/**
 * Renders the slides meta box for the home slider post type.
 *
 * @param WP_Post $post Current post object.
 *
 * @return void
 */
function hram_home_slider_slides_metabox($post) {
    wp_nonce_field('hram_home_slider_slides_nonce', 'hram_home_slider_slides_nonce_field');

    $slides = get_post_meta($post->ID, 'home_slider_slides', true);

    if (!is_array($slides)) {
        $slides = array_filter(array_map('absint', explode(',', (string) $slides)));
    }

    $slides = array_values(array_unique(array_filter(array_map('absint', (array) $slides))));
    ?>
    <div class="hram-home-slider-metabox">
        <p><?php esc_html_e('Выберите изображения для слайдера. Можно менять порядок перетаскиванием.', 'bootscore'); ?></p>
        <div id="hram-home-slider-slides" class="hram-home-slider-slides">
            <?php foreach ($slides as $attachment_id) :
                $thumbnail = wp_get_attachment_image($attachment_id, 'thumbnail', false, [
                    'alt' => esc_attr(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)),
                ]);

                if (!$thumbnail) {
                    continue;
                }
                ?>
                <div class="hram-home-slider-slide" data-attachment-id="<?php echo esc_attr($attachment_id); ?>">
                    <div class="hram-home-slider-slide__thumbnail">
                        <?php echo wp_kses_post($thumbnail); ?>
                    </div>
                    <button type="button" class="button-link-delete hram-home-slider-slide__remove" aria-label="<?php esc_attr_e('Удалить слайд', 'bootscore'); ?>">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button button-secondary" id="hram-home-slider-add">
            <?php esc_html_e('Добавить изображения', 'bootscore'); ?>
        </button>
        <input type="hidden" id="hram-home-slider-input" name="hram_home_slider_slides" value="<?php echo esc_attr(implode(',', $slides)); ?>">
    </div>
    <?php
}

add_action('save_post_home_slider', 'hram_home_slider_save_meta', 10, 2);
/**
 * Saves the slider slides meta when the post is saved.
 *
 * @param int     $post_id Saved post ID.
 * @param WP_Post $post    Current post object.
 *
 * @return void
 */
function hram_home_slider_save_meta($post_id, $post) {
    if (!isset($_POST['hram_home_slider_slides_nonce_field']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['hram_home_slider_slides_nonce_field'])), 'hram_home_slider_slides_nonce')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['hram_home_slider_slides'])) {
        delete_post_meta($post_id, 'home_slider_slides');
        return;
    }

    $raw_ids = sanitize_text_field(wp_unslash($_POST['hram_home_slider_slides']));
    $ids     = array_filter(array_map('absint', explode(',', $raw_ids)));
    $ids     = array_values(array_unique($ids));

    if (empty($ids)) {
        delete_post_meta($post_id, 'home_slider_slides');
        return;
    }

    update_post_meta($post_id, 'home_slider_slides', $ids);
}

add_action('admin_enqueue_scripts', 'hram_home_slider_admin_assets');
/**
 * Enqueues scripts and styles for the home slider meta box.
 *
 * @param string $hook_suffix Current admin page hook.
 *
 * @return void
 */
function hram_home_slider_admin_assets($hook_suffix) {
    $screen = get_current_screen();

    if (!$screen || 'home_slider' !== $screen->post_type || !in_array($hook_suffix, ['post.php', 'post-new.php'], true)) {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_style(
        'hram-home-slider-admin',
        get_template_directory_uri() . '/assets/css/admin-home-slider.css',
        [],
        file_exists(get_template_directory() . '/assets/css/admin-home-slider.css') ? filemtime(get_template_directory() . '/assets/css/admin-home-slider.css') : false
    );

    wp_enqueue_script(
        'hram-home-slider-admin',
        get_template_directory_uri() . '/assets/js/home-slider-admin.js',
        ['jquery', 'jquery-ui-sortable'],
        file_exists(get_template_directory() . '/assets/js/home-slider-admin.js') ? filemtime(get_template_directory() . '/assets/js/home-slider-admin.js') : false,
        true
    );

    wp_localize_script(
        'hram-home-slider-admin',
        'hramHomeSliderAdmin',
        [
            'i18n' => [
                'frameTitle'  => __('Выберите изображения для слайдера', 'bootscore'),
                'frameButton' => __('Добавить в слайдер', 'bootscore'),
                'removeSlide' => __('Удалить слайд', 'bootscore'),
            ],
        ]
    );
}

add_action('init', 'bootscore_register_service_post_type');
/**
 * Registers the "Богослужение" custom post type.
 *
 * @return void
 */
function bootscore_register_service_post_type() {
    $labels = [
        'name'                  => __('Богослужения', 'bootscore'),
        'singular_name'         => __('Богослужение', 'bootscore'),
        'menu_name'             => __('Богослужения', 'bootscore'),
        'name_admin_bar'        => __('Богослужение', 'bootscore'),
        'add_new'               => __('Добавить новое', 'bootscore'),
        'add_new_item'          => __('Добавить богослужение', 'bootscore'),
        'new_item'              => __('Новое богослужение', 'bootscore'),
        'edit_item'             => __('Редактировать богослужение', 'bootscore'),
        'view_item'             => __('Просмотреть богослужение', 'bootscore'),
        'all_items'             => __('Все богослужения', 'bootscore'),
        'search_items'          => __('Искать богослужения', 'bootscore'),
        'parent_item_colon'     => __('Родительское богослужение:', 'bootscore'),
        'not_found'             => __('Богослужений не найдено.', 'bootscore'),
        'not_found_in_trash'    => __('В корзине богослужений не найдено.', 'bootscore'),
        'featured_image'        => __('Изображение богослужения', 'bootscore'),
        'set_featured_image'    => __('Задать изображение богослужения', 'bootscore'),
        'remove_featured_image' => __('Удалить изображение богослужения', 'bootscore'),
        'use_featured_image'    => __('Использовать как изображение богослужения', 'bootscore'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'show_in_rest'       => true,
        'rewrite'            => [
            'slug'       => 'bogosluzhenie',
            'with_front' => false,
        ],
        'menu_icon'          => 'dashicons-groups',
        'supports'           => ['title', 'editor', 'excerpt', 'thumbnail'],
    ];

    register_post_type('service', $args);
}

add_filter('use_block_editor_for_post_type', 'bootscore_disable_service_block_editor', 10, 2);
/**
 * Forces the classic editor for the богослужение post type.
 *
 * @param bool   $use_block_editor Whether the block editor is enabled.
 * @param string $post_type        Current post type.
 *
 * @return bool
 */
function bootscore_disable_service_block_editor($use_block_editor, $post_type) {
    if ('service' === $post_type) {
        return false;
    }

    return $use_block_editor;
}

add_action('init', 'bootscore_register_clergy_post_type');
/**
 * Registers the "Духовенство" custom post type.
 *
 * @return void
 */
function bootscore_register_clergy_post_type() {
    $labels = [
        'name'                  => __('Духовенство', 'bootscore'),
        'singular_name'         => __('Духовенство', 'bootscore'),
        'menu_name'             => __('Духовенство', 'bootscore'),
        'name_admin_bar'        => __('Духовенство', 'bootscore'),
        'add_new'               => __('Добавить нового', 'bootscore'),
        'add_new_item'          => __('Добавить священнослужителя', 'bootscore'),
        'new_item'              => __('Новый священнослужитель', 'bootscore'),
        'edit_item'             => __('Редактировать священнослужителя', 'bootscore'),
        'view_item'             => __('Просмотреть священнослужителя', 'bootscore'),
        'all_items'             => __('Все священнослужители', 'bootscore'),
        'search_items'          => __('Искать священнослужителей', 'bootscore'),
        'parent_item_colon'     => __('Родительский элемент духовенства:', 'bootscore'),
        'not_found'             => __('Священнослужителей не найдено.', 'bootscore'),
        'not_found_in_trash'    => __('В корзине священнослужителей не найдено.', 'bootscore'),
        'featured_image'        => __('Изображение священнослужителя', 'bootscore'),
        'set_featured_image'    => __('Задать изображение священнослужителя', 'bootscore'),
        'remove_featured_image' => __('Удалить изображение священнослужителя', 'bootscore'),
        'use_featured_image'    => __('Использовать как изображение священнослужителя', 'bootscore'),
    ];

    $args = [
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'rewrite'      => [
            'slug'       => 'dukhovenstvo',
            'with_front' => false,
        ],
        'menu_icon'    => 'dashicons-admin-users',
        'supports'     => ['title', 'editor', 'excerpt', 'thumbnail'],
    ];

    register_post_type('clergy', $args);
}

add_filter('use_block_editor_for_post_type', 'bootscore_disable_clergy_block_editor', 10, 2);
/**
 * Forces the classic editor for the духовенство post type.
 *
 * @param bool   $use_block_editor Whether the block editor is enabled.
 * @param string $post_type        Current post type.
 *
 * @return bool
 */
function bootscore_disable_clergy_block_editor($use_block_editor, $post_type) {
    if ('clergy' === $post_type) {
        return false;
    }

    return $use_block_editor;
}

add_action('init', 'bootscore_register_relic_post_type');
/**
 * Registers the "Святыни" custom post type.
 *
 * @return void
 */
function bootscore_register_relic_post_type() {
    $labels = [
        'name'                  => __('Святыни', 'bootscore'),
        'singular_name'         => __('Святыня', 'bootscore'),
        'menu_name'             => __('Святыни', 'bootscore'),
        'name_admin_bar'        => __('Святыня', 'bootscore'),
        'add_new'               => __('Добавить новую', 'bootscore'),
        'add_new_item'          => __('Добавить святыню', 'bootscore'),
        'new_item'              => __('Новая святыня', 'bootscore'),
        'edit_item'             => __('Редактировать святыню', 'bootscore'),
        'view_item'             => __('Просмотреть святыню', 'bootscore'),
        'all_items'             => __('Все святыни', 'bootscore'),
        'search_items'          => __('Искать святыни', 'bootscore'),
        'parent_item_colon'     => __('Родительская святыня:', 'bootscore'),
        'not_found'             => __('Святыни не найдены.', 'bootscore'),
        'not_found_in_trash'    => __('В корзине святыни не найдены.', 'bootscore'),
        'featured_image'        => __('Изображение святыни', 'bootscore'),
        'set_featured_image'    => __('Задать изображение святыни', 'bootscore'),
        'remove_featured_image' => __('Удалить изображение святыни', 'bootscore'),
        'use_featured_image'    => __('Использовать как изображение святыни', 'bootscore'),
    ];

    $args = [
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => true,
        'show_in_rest' => true,
        'rewrite'      => [
            'slug'       => 'svyatyni',
            'with_front' => false,
        ],
        'menu_icon'    => 'dashicons-shield-alt',
        'supports'     => ['title', 'editor', 'excerpt', 'thumbnail'],
    ];

    register_post_type('relic', $args);
}

add_filter('use_block_editor_for_post_type', 'bootscore_disable_relic_block_editor', 10, 2);
/**
 * Forces the classic editor for the святыни post type.
 *
 * @param bool   $use_block_editor Whether the block editor is enabled.
 * @param string $post_type        Current post type.
 *
 * @return bool
 */
function bootscore_disable_relic_block_editor($use_block_editor, $post_type) {
    if ('relic' === $post_type) {
        return false;
    }

    return $use_block_editor;
}

add_action('init', 'bootscore_register_service_meta');
/**
 * Registers date and time meta fields for богослужение posts.
 *
 * @return void
 */
function bootscore_register_service_meta() {
    $meta_args = [
        'type'              => 'string',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => 'bootscore_service_meta_cap_check',
    ];

    register_post_meta('service', 'service_date', $meta_args);
    register_post_meta('service', 'service_time', $meta_args);
}

add_action('init', 'bootscore_register_clergy_meta');
/**
 * Registers number meta field for духовенство posts.
 *
 * @return void
 */
function bootscore_register_clergy_meta() {
    $meta_args = [
        'type'              => 'integer',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'absint',
        'auth_callback'     => 'bootscore_service_meta_cap_check',
    ];

    register_post_meta('clergy', 'clergy_number', $meta_args);
}

add_action('init', 'bootscore_register_relic_meta');
/**
 * Registers number meta field for святыни posts.
 *
 * @return void
 */
function bootscore_register_relic_meta() {
    $meta_args = [
        'type'              => 'integer',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'absint',
        'auth_callback'     => 'bootscore_service_meta_cap_check',
    ];

    register_post_meta('relic', 'relic_number', $meta_args);
}

/**
 * Limits meta update capability to users who can edit the post.
 *
 * @param bool   $allowed Whether the user can modify the meta.
 * @param string $meta_key Meta key.
 * @param int    $post_id Post ID.
 *
 * @return bool
 */
function bootscore_service_meta_cap_check($allowed, $meta_key, $post_id, $user_id = 0, $cap = '', $caps = []) {
    return current_user_can('edit_post', $post_id);
}

add_action('add_meta_boxes', 'bootscore_service_add_meta_boxes');
/**
 * Adds meta boxes for the богослужение post type.
 *
 * @return void
 */
function bootscore_service_add_meta_boxes() {
    add_meta_box(
        'bootscore-service-datetime',
        __('Дата и время богослужения', 'bootscore'),
        'bootscore_service_datetime_metabox',
        'service',
        'side'
    );
}

add_action('add_meta_boxes', 'bootscore_clergy_add_meta_boxes');
/**
 * Adds meta boxes for the духовенство post type.
 *
 * @return void
 */
function bootscore_clergy_add_meta_boxes() {
    add_meta_box(
        'bootscore-clergy-number',
        __('Номер священнослужителя', 'bootscore'),
        'bootscore_clergy_number_metabox',
        'clergy',
        'side'
    );
}

add_action('add_meta_boxes', 'bootscore_relic_add_meta_boxes');
/**
 * Adds meta boxes for the святыни post type.
 *
 * @return void
 */
function bootscore_relic_add_meta_boxes() {
    add_meta_box(
        'bootscore-relic-number',
        __('Номер святыни', 'bootscore'),
        'bootscore_relic_number_metabox',
        'relic',
        'side'
    );
}

/**
 * Renders the богослужение date and time meta box.
 *
 * @param WP_Post $post Current post object.
 *
 * @return void
 */
function bootscore_service_datetime_metabox($post) {
    wp_nonce_field('bootscore_service_datetime_nonce', 'bootscore_service_datetime_nonce_field');

    $date_value = get_post_meta($post->ID, 'service_date', true);
    $time_value = get_post_meta($post->ID, 'service_time', true);
    ?>
    <p>
        <label for="bootscore-service-date"><?php esc_html_e('Дата', 'bootscore'); ?></label>
        <input type="date" id="bootscore-service-date" name="bootscore_service_date" value="<?php echo esc_attr($date_value); ?>" class="widefat" />
    </p>
    <p>
        <label for="bootscore-service-time"><?php esc_html_e('Время', 'bootscore'); ?></label>
        <input type="time" id="bootscore-service-time" name="bootscore_service_time" value="<?php echo esc_attr($time_value); ?>" class="widefat" />
    </p>
    <?php
}

/**
 * Renders the духовенство number meta box.
 *
 * @param WP_Post $post Current post object.
 *
 * @return void
 */
function bootscore_clergy_number_metabox($post) {
    wp_nonce_field('bootscore_clergy_number_nonce', 'bootscore_clergy_number_nonce_field');

    $number_value = get_post_meta($post->ID, 'clergy_number', true);
    ?>
    <p>
        <label for="bootscore-clergy-number"><?php esc_html_e('Номер', 'bootscore'); ?></label>
        <input type="number" id="bootscore-clergy-number" name="bootscore_clergy_number" value="<?php echo esc_attr($number_value); ?>" class="widefat" min="0" step="1" />
    </p>
    <?php
}

/**
 * Renders the святыни number meta box.
 *
 * @param WP_Post $post Current post object.
 *
 * @return void
 */
function bootscore_relic_number_metabox($post) {
    wp_nonce_field('bootscore_relic_number_nonce', 'bootscore_relic_number_nonce_field');

    $number_value = get_post_meta($post->ID, 'relic_number', true);
    ?>
    <p>
        <label for="bootscore-relic-number"><?php esc_html_e('Номер', 'bootscore'); ?></label>
        <input type="number" id="bootscore-relic-number" name="bootscore_relic_number" value="<?php echo esc_attr($number_value); ?>" class="widefat" min="0" step="1" />
    </p>
    <?php
}

add_action('save_post_service', 'bootscore_service_save_meta', 10, 2);
/**
 * Handles saving the богослужение meta box fields.
 *
 * @param int     $post_id Saved post ID.
 * @param WP_Post $post Post object.
 *
 * @return void
 */
function bootscore_service_save_meta($post_id, $post) {
    if (!isset($_POST['bootscore_service_datetime_nonce_field']) ||
        !wp_verify_nonce($_POST['bootscore_service_datetime_nonce_field'], 'bootscore_service_datetime_nonce')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['bootscore_service_date'])) {
        update_post_meta($post_id, 'service_date', sanitize_text_field($_POST['bootscore_service_date']));
    }

    if (isset($_POST['bootscore_service_time'])) {
        update_post_meta($post_id, 'service_time', sanitize_text_field($_POST['bootscore_service_time']));
    }
}

add_action('save_post_clergy', 'bootscore_clergy_save_meta', 10, 2);
/**
 * Handles saving the духовенство meta box field.
 *
 * @param int     $post_id Saved post ID.
 * @param WP_Post $post Post object.
 *
 * @return void
 */
function bootscore_clergy_save_meta($post_id, $post) {
    if (!isset($_POST['bootscore_clergy_number_nonce_field']) ||
        !wp_verify_nonce($_POST['bootscore_clergy_number_nonce_field'], 'bootscore_clergy_number_nonce')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['bootscore_clergy_number'])) {
        update_post_meta($post_id, 'clergy_number', absint($_POST['bootscore_clergy_number']));
    }
}

add_action('save_post_relic', 'bootscore_relic_save_meta', 10, 2);
/**
 * Handles saving the святыни meta box field.
 *
 * @param int     $post_id Saved post ID.
 * @param WP_Post $post Post object.
 *
 * @return void
 */
function bootscore_relic_save_meta($post_id, $post) {
    if (!isset($_POST['bootscore_relic_number_nonce_field']) ||
        !wp_verify_nonce($_POST['bootscore_relic_number_nonce_field'], 'bootscore_relic_number_nonce')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['bootscore_relic_number'])) {
        update_post_meta($post_id, 'relic_number', absint($_POST['bootscore_relic_number']));
    }
}