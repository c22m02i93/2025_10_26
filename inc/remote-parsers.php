<?php
/**
 * Remote content parsers and helpers.
 *
 * @package Bootscore
 */

defined('ABSPATH') || exit;

/**
 * Returns start of November for the current site timezone.
 *
 * @return DateTimeImmutable
 */
function bootscore_remote_get_november_threshold(): DateTimeImmutable {
  $timezone = wp_timezone();
  $now      = new DateTimeImmutable('now', $timezone);
  $year     = (int) $now->format('Y');

  return (new DateTimeImmutable(sprintf('first day of November %d', $year), $timezone))->setTime(0, 0);
}

/**
 * Perform a remote GET request with caching and hardened defaults.
 *
 * @param string $url       Remote URL.
 * @param int    $cache_ttl Cache lifetime in seconds.
 *
 * @return array{ok:bool, body:string}
 */
function bootscore_remote_fetch_with_cache(string $url, int $cache_ttl = HOUR_IN_SECONDS): array {
  $cache_key = 'bootscore_remote_' . md5($url);
  $cached    = get_transient($cache_key);

  if (is_array($cached) && isset($cached['ok'], $cached['body'])) {
    return $cached;
  }

  $args = [
    'timeout'     => 20,
    'redirection' => 3,
    'headers'     => [
      'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
      'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
      'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
      'Cache-Control'   => 'no-cache',
      'Pragma'          => 'no-cache',
    ],
  ];

  $response = wp_remote_get($url, apply_filters('bootscore/remote/request_args', $args, $url));

  if (is_wp_error($response)) {
    $result = ['ok' => false, 'body' => ''];
    set_transient($cache_key, $result, MINUTE_IN_SECONDS * 10);

    return $result;
  }

  $code = wp_remote_retrieve_response_code($response);
  $body = wp_remote_retrieve_body($response);

  if (!is_string($body)) {
    $body = '';
  }

  $is_success = $code >= 200 && $code < 300 && $body !== '';
  $ttl        = $is_success ? $cache_ttl : MINUTE_IN_SECONDS * 10;

  $result = [
    'ok'   => $is_success,
    'body' => $is_success ? $body : '',
  ];

  set_transient($cache_key, $result, $ttl);

  return $result;
}

/**
 * Convert a Russian date string to timestamp.
 *
 * @param string $value Date string from remote site.
 *
 * @return int|null Timestamp or null on failure.
 */
function bootscore_remote_parse_russian_date(string $value): ?int {
  $value = trim($value);

  if ($value === '') {
    return null;
  }

  // Try ISO first.
  $iso_try = strtotime($value);

  if ($iso_try !== false) {
    return (int) $iso_try;
  }

  $normalized = mb_strtolower($value, 'UTF-8');
  $normalized = preg_replace('~\s+г(?:\.|ода)?$~u', '', $normalized);

  $months = [
    'января'   => '01',
    'февраля'  => '02',
    'марта'    => '03',
    'апреля'   => '04',
    'мая'      => '05',
    'июня'     => '06',
    'июля'     => '07',
    'августа'  => '08',
    'сентября' => '09',
    'октября'  => '10',
    'ноября'   => '11',
    'декабря'  => '12',
  ];

  foreach ($months as $month_name => $month_number) {
    $normalized = preg_replace('~' . $month_name . '~u', $month_number, $normalized);
  }

  $normalized = preg_replace('~[^0-9\s:\.-]~u', ' ', $normalized);
  $normalized = preg_replace('~\s+~u', ' ', $normalized);
  $normalized = trim($normalized);

  if ($normalized === '') {
    return null;
  }

  $patterns = [
    'd.m.Y H:i',
    'd.m.Y',
    'd m Y H:i',
    'd m Y',
  ];

  foreach ($patterns as $pattern) {
    $format = DateTime::createFromFormat($pattern, $normalized, wp_timezone());

    if ($format instanceof DateTime) {
      return $format->getTimestamp();
    }
  }

  return null;
}

/**
 * Extracts string content from DOM node-like structures.
 *
 * @param mixed $node DOM node.
 *
 * @return string
 */
function bootscore_remote_node_text($node): string {
  if (!is_object($node) || !property_exists($node, 'textContent')) {
    return '';
  }

  $text = trim(preg_replace('~\s+~u', ' ', (string) $node->textContent));

  return $text;
}

/**
 * Helper to retrieve the first node that matches XPath expression.
 *
 * @param mixed  $xpath   XPath instance.
 * @param string $query   XPath query.
 * @param mixed  $context Optional context node.
 *
 * @return object|null
 */
function bootscore_remote_xpath_first($xpath, string $query, $context = null): ?object {
  if (!is_object($xpath) || !method_exists($xpath, 'query')) {
    return null;
  }

  $nodes = $xpath->query($query, $context);

  if (!is_object($nodes) || !property_exists($nodes, 'length') || !method_exists($nodes, 'item') || (int) $nodes->length === 0) {
    return null;
  }

  $node = $nodes->item(0);

  return is_object($node) ? $node : null;
}

/**
 * Parse remote HTML into structured article items.
 *
 * @param string $html  HTML markup.
 * @param array  $args  Additional arguments.
 *
 * @return array<int, array<string, mixed>>
 */
function bootscore_remote_parse_posts(string $html, array $args = []): array {
  $defaults = [
    'limit'        => 12,
    'source_label' => '',
    'source_url'   => '',
    'threshold'    => null,
  ];

  $args  = wp_parse_args($args, $defaults);
  $limit = max(1, (int) $args['limit']);
  $items = [];
  $html  = trim($html);

  if ($html === '') {
    return $items;
  }

  if (!class_exists('DOMDocument')) {
    return $items;
  }

  $dom = new DOMDocument();
  libxml_use_internal_errors(true);

  $loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

  libxml_clear_errors();

  if (!$loaded) {
    return $items;
  }

  $xpath      = new DOMXPath($dom);
  $threshold  = $args['threshold'];
  $thresholds = null;

  if ($threshold instanceof DateTimeInterface) {
    $thresholds = $threshold->getTimestamp();
  } elseif (is_int($threshold)) {
    $thresholds = $threshold;
  }

  $articles = $xpath->query('//article');

  if (!is_object($articles) || !property_exists($articles, 'length') || !method_exists($articles, 'item') || (int) $articles->length === 0) {
    return $items;
  }

  foreach ($articles as $article) {
    if (!is_object($article)) {
      continue;
    }

    $title_node = bootscore_remote_xpath_first($xpath, './/h2[contains(@class,"entry-title")]//a | .//h3[contains(@class,"entry-title")]//a | .//h2//a | .//h3//a', $article);

    if (!is_object($title_node) || !method_exists($title_node, 'getAttribute')) {
      continue;
    }

    $link = trim((string) $title_node->getAttribute('href'));

    $title_text = property_exists($title_node, 'textContent') ? (string) $title_node->textContent : '';
    $title      = trim(preg_replace('~\s+~u', ' ', $title_text));

    if ($link === '' || $title === '') {
      continue;
    }

    $time_node = bootscore_remote_xpath_first($xpath, './/time[@datetime] | .//time | .//*[contains(@class, "post-date")] | .//*[contains(@class, "jeg_meta_date")] | .//*[contains(@class, "elementor-post-date")] | .//*[contains(@class, "archive-item__date")] | .//span[contains(@class, "date")] | .//div[contains(@class, "date")]', $article);

    $timestamp = null;

    if (is_object($time_node) && method_exists($time_node, 'getAttribute')) {
      $datetime_attr = trim((string) $time_node->getAttribute('datetime'));

      if ($datetime_attr !== '') {
        try {
          $date = new DateTimeImmutable($datetime_attr, wp_timezone());
          $timestamp = $date->getTimestamp();
        } catch (Exception $e) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
          $timestamp = null;
        }
      }

      if (!$timestamp) {
        $timestamp = bootscore_remote_parse_russian_date(bootscore_remote_node_text($time_node));
      }
    }

    if (!$timestamp) {
      continue;
    }

    if ($thresholds && $timestamp < $thresholds) {
      continue;
    }

    $image_node = bootscore_remote_xpath_first($xpath, './/img', $article);
    $image_url  = '';
    $image_alt  = '';

    if (is_object($image_node) && method_exists($image_node, 'getAttribute')) {
      $image_url = trim((string) $image_node->getAttribute('src'));

      if ($image_url === '' && method_exists($image_node, 'hasAttributes') && $image_node->hasAttributes()) {
        foreach (['data-src', 'data-lazy-src', 'data-original'] as $attr) {
          $candidate = trim((string) $image_node->getAttribute($attr));

          if ($candidate !== '') {
            $image_url = $candidate;
            break;
          }
        }
      }

      if ($image_url !== '' && strncmp($image_url, '//', 2) === 0) {
        $image_url = 'https:' . $image_url;
      }

      $image_alt = trim((string) $image_node->getAttribute('alt'));
    }

    $excerpt_node = bootscore_remote_xpath_first($xpath, './/div[contains(@class, "entry-summary")]//p | .//div[contains(@class, "entry-content")]//p | .//div[contains(@class, "post-excerpt")]//p | .//div[contains(@class, "elementor-post__excerpt")]//p | .//p', $article);

    $excerpt = '';

    if (is_object($excerpt_node)) {
      $excerpt = wp_trim_words(bootscore_remote_node_text($excerpt_node), 32, '…');
    }

    $category_node = bootscore_remote_xpath_first($xpath, './/a[contains(@rel, "category")] | .//a[contains(@class, "category")] | .//a[contains(@class, "cat-link")] | .//a[contains(@href, "/category/")]', $article);

    $category = [
      'name' => '',
      'url'  => '',
    ];

    if (is_object($category_node)) {
      $category_text      = property_exists($category_node, 'textContent') ? $category_node->textContent : '';
      $category['name']   = trim(preg_replace('~\s+~u', ' ', (string) $category_text));
      $category['url']    = method_exists($category_node, 'getAttribute') ? trim((string) $category_node->getAttribute('href')) : '';
    }

    $items[] = [
      'is_remote'     => true,
      'title'         => $title,
      'link'          => $link,
      'timestamp'     => $timestamp,
      'date_display'  => wp_date('d.m.Y', $timestamp),
      'datetime_attr' => wp_date('c', $timestamp),
      'excerpt'       => $excerpt,
      'category'      => $category,
      'image_url'     => $image_url,
      'image_alt'     => $image_alt,
      'views'         => null,
      'source_label'  => (string) $args['source_label'],
      'source_url'    => $args['source_url'] ? (string) $args['source_url'] : $link,
    ];

    if (count($items) >= $limit) {
      break;
    }
  }

  return $items;
}

/**
 * Normalize local posts into a comparable data structure.
 *
 * @param WP_Query             $query     Source query.
 * @param DateTimeInterface|int|null $threshold Date threshold.
 *
 * @return array<int, array<string, mixed>>
 */
function bootscore_remote_collect_local_posts(WP_Query $query, $threshold = null): array {
  $items = [];

  if (!$query->have_posts()) {
    return $items;
  }

  $threshold_ts = null;

  if ($threshold instanceof DateTimeInterface) {
    $threshold_ts = $threshold->getTimestamp();
  } elseif (is_int($threshold)) {
    $threshold_ts = $threshold;
  }

  while ($query->have_posts()) {
    $query->the_post();

    $timestamp = get_post_timestamp(null, true);

    if ($timestamp === false) {
      continue;
    }

    if ($threshold_ts && $timestamp < $threshold_ts) {
      continue;
    }

    $excerpt = trim(get_the_excerpt());
    $excerpt = $excerpt ? wp_trim_words($excerpt, 32, '…') : '';

    $categories   = get_the_category();
    $primary_term = $categories ? $categories[0] : null;

    $category = [
      'name' => $primary_term instanceof WP_Term ? $primary_term->name : '',
      'url'  => $primary_term instanceof WP_Term ? get_category_link($primary_term->term_id) : '',
    ];

    $image_html = '';

    if (has_post_thumbnail()) {
      $image_html = wp_get_attachment_image(get_post_thumbnail_id(), 'medium_large', false, [
        'class'   => 'front-news-card__image',
        'loading' => 'lazy',
      ]);
    }

    $items[] = [
      'is_remote'     => false,
      'title'         => get_the_title(),
      'link'          => get_permalink(),
      'timestamp'     => $timestamp,
      'date_display'  => get_the_date('d.m.Y'),
      'datetime_attr' => get_the_date('c'),
      'excerpt'       => $excerpt,
      'category'      => $category,
      'image_html'    => $image_html,
      'image_has'     => has_post_thumbnail(),
      'views'         => (int) get_post_meta(get_the_ID(), 'post_views_count', true),
      'source_label'  => '',
      'source_url'    => '',
    ];
  }

  return $items;
}

/**
 * Merge and deduplicate items, ordering by date descending.
 *
 * @param array<int, array<string, mixed>> $local_items  Local posts.
 * @param array<int, array<string, mixed>> $remote_items Remote posts.
 * @param int                              $limit        Maximum number of items.
 *
 * @return array<int, array<string, mixed>>
 */
function bootscore_remote_merge_items(array $local_items, array $remote_items, int $limit = 10): array {
  $combined = array_merge($local_items, $remote_items);

  if (empty($combined)) {
    return [];
  }

  usort($combined, static function (array $a, array $b): int {
    return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
  });

  $seen   = [];
  $result = [];

  foreach ($combined as $item) {
    if (empty($item['link'])) {
      continue;
    }

    $key = strtolower($item['link']);

    if (isset($seen[$key])) {
      continue;
    }

    $seen[$key] = true;
    $result[]   = $item;

    if (count($result) >= $limit) {
      break;
    }
  }

  return $result;
}

/**
 * Render a front news card from normalized data.
 *
 * @param array<string, mixed> $item  Item data.
 * @param array<string, mixed> $args  Additional args.
 *
 * @return string
 */
function bootscore_render_news_card(array $item, array $args = []): string {
  $defaults = [
    'show_views'        => true,
    'show_source_badge' => true,
  ];

  $args = wp_parse_args($args, $defaults);

  $classes = ['front-news-card'];

  if (!empty($item['is_remote'])) {
    $classes[] = 'front-news-card--external';
  }

  $classes = array_unique(array_map('sanitize_html_class', $classes));

  $link_attrs     = '';
  $category_attrs = '';

  if (!empty($item['link'])) {
    $link_attrs = sprintf(' href="%s"', esc_url($item['link']));

    if (!empty($item['is_remote'])) {
      $link_attrs .= ' target="_blank" rel="noopener noreferrer external"';
    }
  }

  if (!empty($item['category']['url'])) {
    $category_attrs = sprintf(' href="%s"', esc_url($item['category']['url']));

    if (!empty($item['is_remote'])) {
      $category_attrs .= ' target="_blank" rel="noopener noreferrer external"';
    }
  }

  $date_display  = $item['date_display'] ?? '';
  $datetime_attr = $item['datetime_attr'] ?? '';
  $excerpt       = $item['excerpt'] ?? '';
  $views         = isset($item['views']) ? (int) $item['views'] : null;
  $source_label  = $item['source_label'] ?? '';

  ob_start();
  ?>
  <article class="<?= esc_attr(implode(' ', $classes)); ?>">
    <a class="front-news-card__media"<?= $link_attrs; ?>>
      <?php if (!empty($item['image_html'])) : ?>
        <?= wp_kses_post($item['image_html']); ?>
      <?php elseif (!empty($item['image_url'])) : ?>
        <img
          class="front-news-card__image"
          src="<?= esc_url($item['image_url']); ?>"
          alt="<?= esc_attr($item['image_alt'] ?? ''); ?>"
          loading="lazy"
        >
      <?php else : ?>
        <span class="front-news-card__placeholder" aria-hidden="true"></span>
      <?php endif; ?>
    </a>

    <div class="front-news-card__content">
      <?php if (!empty($item['category']['name'])) : ?>
        <div class="front-news-card__category">
          <a class="front-news-card__category-link"<?= $category_attrs; ?>>
            <?= esc_html($item['category']['name']); ?>
          </a>
        </div>
      <?php endif; ?>

      <?php if (!empty($item['title'])) : ?>
        <h3 class="front-news-card__title">
          <a class="front-news-card__link"<?= $link_attrs; ?>>
            <?= esc_html($item['title']); ?>
          </a>
        </h3>
      <?php endif; ?>

      <?php if (!empty($excerpt)) : ?>
        <p class="front-news-card__excerpt"><?= esc_html($excerpt); ?></p>
      <?php endif; ?>

      <div class="front-news-card__meta">
        <?php if (!empty($date_display) && !empty($datetime_attr)) : ?>
          <span class="front-news-card__meta-item">
            <i class="fa-regular fa-calendar" aria-hidden="true"></i>
            <time datetime="<?= esc_attr($datetime_attr); ?>"><?= esc_html($date_display); ?></time>
          </span>
        <?php endif; ?>

        <?php if ($args['show_views'] && $views !== null) : ?>
          <span class="front-news-card__meta-item">
            <i class="fa-regular fa-eye" aria-hidden="true"></i>
            <span><?= esc_html(number_format_i18n(max($views, 0))); ?></span>
          </span>
        <?php endif; ?>

        <?php if ($args['show_source_badge'] && !empty($source_label) && !empty($item['is_remote'])) : ?>
          <span class="front-news-card__meta-item">
            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            <span><?= esc_html($source_label); ?></span>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </article>
  <?php

  return trim(ob_get_clean());
}

/**
 * Fetch and parse remote posts.
 *
 * @param string $url  Remote URL.
 * @param array  $args Additional arguments.
 *
 * @return array<int, array<string, mixed>>
 */
function bootscore_remote_get_posts(string $url, array $args = []): array {
  $defaults = [
    'limit'        => 12,
    'threshold'    => null,
    'source_label' => '',
    'source_url'   => '',
    'cache_ttl'    => HOUR_IN_SECONDS,
  ];

  $args = wp_parse_args($args, $defaults);

  $response = bootscore_remote_fetch_with_cache($url, (int) $args['cache_ttl']);

  if (!$response['ok']) {
    return [];
  }

  return bootscore_remote_parse_posts($response['body'], [
    'limit'        => (int) $args['limit'],
    'threshold'    => $args['threshold'],
    'source_label' => $args['source_label'],
    'source_url'   => $args['source_url'] ?: $url,
  ]);
}

/**
 * Collect combined news items for the front page.
 *
 * @param array $args Arguments.
 *
 * @return array<int, array<string, mixed>>
 */
function bootscore_remote_get_frontpage_news(array $args = []): array {
  $defaults = [
    'max_items'    => 4,
    'local_args'   => [],
    'remote_limit' => 24,
    'threshold'    => null,
  ];

  $args = wp_parse_args($args, $defaults);

  $threshold = $args['threshold'];

  if (!$threshold instanceof DateTimeInterface && !is_int($threshold)) {
    $threshold = bootscore_remote_get_november_threshold();
  }

  $threshold_ts = $threshold instanceof DateTimeInterface ? $threshold->getTimestamp() : (int) $threshold;

  $local_query_args = wp_parse_args($args['local_args'], [
    'post_type'           => 'post',
    'posts_per_page'      => 12,
    'category_name'       => 'novosti',
    'ignore_sticky_posts' => true,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'date_query'          => [
      [
        'after'     => wp_date('Y-m-d', $threshold_ts),
        'inclusive' => true,
      ],
    ],
  ]);

  $news_query = new WP_Query($local_query_args);
  $local      = bootscore_remote_collect_local_posts($news_query, $threshold_ts);
  wp_reset_postdata();

  $remote_sources = [
    [
      'url'          => 'https://mitropolia-simbirsk.ru/tag/baryshskaya-eparhiya',
      'source_label' => __('Митрополия — Барышская епархия', 'bootscore'),
    ],
    [
      'url'          => 'https://mitropolia-simbirsk.ru/',
      'source_label' => __('Митрополия — Главная страница', 'bootscore'),
    ],
    [
      'url'          => 'https://mitropolia-simbirsk.ru/category/novosti',
      'source_label' => __('Митрополия — Новости', 'bootscore'),
    ],
    [
      'url'          => 'https://mitropolia-simbirsk.ru/category/sobytiya',
      'source_label' => __('Митрополия — События', 'bootscore'),
    ],
  ];

  $remote_items = [];

  foreach ($remote_sources as $remote_source) {
    $remote_items = array_merge(
      $remote_items,
      bootscore_remote_get_posts($remote_source['url'], [
        'limit'        => (int) $args['remote_limit'],
        'threshold'    => $threshold_ts,
        'source_label' => $remote_source['source_label'],
        'source_url'   => $remote_source['url'],
      ])
    );
  }

  return bootscore_remote_merge_items($local, $remote_items, (int) $args['max_items']);
}
