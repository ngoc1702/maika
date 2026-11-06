<?php
/** Partner reviews, editable in the homepage ACF tabs. */
add_image_size('maika-review-avatar', 160, 160, true);
add_action('genesis_after_header', 'maika_home_reviews', 20);
function maika_home_reviews() {
    if (!is_front_page() || !function_exists('get_field')) {
        return;
    }
    $front = (int) get_option('page_on_front');
    $items = get_field('maika_reviews', $front);
    $items = is_array($items) ? array_values(array_filter($items, function ($item) {
        return !empty($item['quote']) && !empty($item['name']);
    })) : array();
    if (!$items) {
        return;
    }
    $heading = get_field('maika_reviews_heading', $front) ?: 'Nhận xét của đối tác';
    $background = wp_get_attachment_image_url((int) get_field('maika_reviews_bg', $front), 'full') ?: get_stylesheet_directory_uri() . '/images/hero-pattern-small.png';
    echo '<section class="maika-reviews" aria-labelledby="maika-reviews-heading" style="--maika-reviews-background:url(' . esc_url($background) . ')"><div class="wrap">';
    echo '<h2 id="maika-reviews-heading" class="maika-reviews-heading">' . esc_html($heading) . '</h2>';
    echo '<div class="maika-reviews-carousel" data-maika-reviews><div class="maika-reviews-viewport" tabindex="0" role="group" aria-label="Các nhận xét của đối tác">';
    foreach ($items as $item) {
        echo '<article class="maika-review-card"><img class="maika-review-quote-icon maika-review-quote-open" src="' . esc_url(get_stylesheet_directory_uri() . '/images/left-quote.webp') . '" width="44" height="38" alt="" aria-hidden="true">';
        echo '<blockquote class="maika-review-quote"><p>' . nl2br(esc_html($item['quote'])) . '</p></blockquote>';
        echo '<footer class="maika-review-author">';
        $avatar = !empty($item['avatar']) ? (int) $item['avatar'] : 0;
        if ($avatar && wp_attachment_is_image($avatar)) {
            echo wp_get_attachment_image($avatar, 'maika-review-avatar', false, array('class' => 'maika-review-avatar', 'alt' => '', 'loading' => 'lazy'));
        } else {
            echo '<img class="maika-review-avatar" src="' . esc_url(get_stylesheet_directory_uri() . '/images/review-avatar-placeholder.svg') . '" width="72" height="72" alt="" loading="lazy">';
        }
        echo '<div class="maika-review-identity"><h3>' . esc_html($item['name']) . '</h3>';
        if (!empty($item['role'])) {
            echo '<p>' . esc_html($item['role']) . '</p>';
        }
        echo '</div></footer><img class="maika-review-quote-icon maika-review-quote-close" src="' . esc_url(get_stylesheet_directory_uri() . '/images/right-quote.webp') . '" width="44" height="38" alt="" aria-hidden="true"></article>';
    }
    echo '</div><div class="maika-review-controls" hidden>';
    echo '<button type="button" class="maika-review-prev" aria-label="Nhận xét trước"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m14 6-6 6 6 6"/></svg></button>';
    echo '<button type="button" class="maika-review-next" aria-label="Nhận xét tiếp theo"><svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m10 6 6 6-6 6"/></svg></button>';
    echo '<div class="maika-review-dots" role="group" aria-label="Chọn trang nhận xét"></div>';
    echo '<span class="maika-review-status screen-reader-text" aria-live="polite"></span></div></div>';
    echo '</div></section>';
}
add_action('wp_enqueue_scripts', function () {
    if (is_front_page()) {
        $path = get_stylesheet_directory() . '/custom/reviews-slider.js';
        wp_enqueue_script('maika-reviews-slider', get_stylesheet_directory_uri() . '/custom/reviews-slider.js', array(), filemtime($path), true);
    }
});