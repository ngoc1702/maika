<?php
/** White partner logo strip with a fixed red emblem and draggable marquee. */
add_action('genesis_after_header', 'maika_home_partners', 30);
function maika_home_partners() {
    if (!is_front_page() || !function_exists('get_field')) {
        return;
    }
    $front = (int) get_option('page_on_front');
    $enabled = get_field('maika_partners_enabled', $front);
    if ($enabled !== null && !$enabled) {
        return;
    }
    $logos = get_field('maika_partner_logos', $front);
    $logos = is_array($logos) ? array_values(array_filter(array_map('absint', $logos), 'wp_attachment_is_image')) : array();
    $section_bg = wp_get_attachment_image_url((int) get_field('maika_partners_bg', $front), 'full') ?: get_stylesheet_directory_uri() . '/images/hero-pattern-small.png';
    echo '<section class="maika-partners" aria-label="Đối tác của chúng tôi" style="--maika-section-background:url(' . esc_url($section_bg) . ')"><div class="wrap"><div class="maika-partners-strip">';
    $emblem = (int) get_field('maika_partners_emblem', $front);
    echo '<div class="maika-partners-emblem">';
    if ($emblem && wp_attachment_is_image($emblem)) {
        echo wp_get_attachment_image($emblem, 'medium', false, array('class' => 'maika-partners-emblem-image', 'alt' => '', 'loading' => 'lazy'));
    } else {
        echo '<i class="fa-regular fa-handshake" aria-hidden="true"></i>';
    }
    echo '<span class="screen-reader-text">Đối tác</span></div>';
    echo '<div class="maika-partners-viewport" data-maika-partners tabindex="0" role="group" aria-label="Logo đối tác. Kéo hoặc vuốt để xem, dùng phím mũi tên để di chuyển."><div class="maika-partners-track"><div class="maika-partners-group">';
    if ($logos) {
        foreach ($logos as $logo) {
            echo '<div class="maika-partner-logo">' . wp_get_attachment_image($logo, 'medium', false, array('loading' => 'lazy', 'draggable' => 'false', 'sizes' => '(max-width: 599px) 140px, 184px')) . '</div>';
        }
    } else {
        for ($index = 1; $index <= 6; $index++) {
            echo '<div class="maika-partner-logo maika-partner-placeholder"><span>LOGO <b>' . esc_html(sprintf('%02d', $index)) . '</b></span></div>';
        }
    }
    echo '</div></div></div></div></div></section>';
}
add_action('wp_enqueue_scripts', function () {
    if (is_front_page()) {
        $path = get_stylesheet_directory() . '/custom/partners-slider.js';
        wp_enqueue_script('maika-partners-slider', get_stylesheet_directory_uri() . '/custom/partners-slider.js', array(), filemtime($path), true);
    }
});
