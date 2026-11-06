<?php
/** Homepage layout uses the widgets saved in Primary Sidebar. */
add_action('genesis_after_header', 'maika_home_hero', 10);
function maika_home_hero() {
    if (!is_front_page()) {
        return;
    }
    $front_id = (int) get_option('page_on_front');
    $slides = function_exists('get_field') ? get_field('maika_banner_slides', $front_id) : array();
    $slides = is_array($slides) ? array_values(array_filter(array_map('absint', $slides), function ($id) {
        return wp_attachment_is_image($id);
    })) : array();
    // Keep an existing banner visible until the new gallery has been saved.
    if (!$slides) {
        $legacy = (int) get_post_meta($front_id, 'maika_banner_image', true);
        if ($legacy && wp_attachment_is_image($legacy)) {
            $slides = array($legacy);
        }
    }
    echo '<section class="maika-home-hero wrap" aria-label="Danh mục và banner trang chủ">';
    add_filter('widget_title', 'maika_home_category_title', 10, 3);
    add_filter('dynamic_sidebar_params', 'maika_site_navigation_widget_params');
    add_filter('list_product_cats', 'maika_navigation_category_image', 10, 2);
    dynamic_sidebar('sidebar');
    remove_filter('list_product_cats', 'maika_navigation_category_image', 10);
    remove_filter('dynamic_sidebar_params', 'maika_site_navigation_widget_params');
    remove_filter('widget_title', 'maika_home_category_title', 10);
    echo '<div class="maika-banner">';
    if ($slides) {
        $count = count($slides);
        echo '<div class="maika-slider" role="region" aria-roledescription="carousel" aria-label="Giới thiệu Maika" data-maika-slider>';
        foreach ($slides as $index => $image) {
            $active = 0 === $index;
            echo '<div class="maika-slide" role="group" aria-roledescription="slide" aria-label="' . esc_attr(($index + 1) . ' / ' . $count) . '"' . ($active ? '' : ' hidden') . '>';
            echo wp_get_attachment_image($image, 'full', false, array(
                'class' => 'maika-banner-image',
                'alt' => get_post_meta($image, '_wp_attachment_image_alt', true) ?: get_bloginfo('name'),
                'loading' => $active ? 'eager' : 'lazy',
                'fetchpriority' => $active ? 'high' : 'auto',
                'sizes' => '(max-width: 767px) calc(100vw - 32px), (max-width: 1240px) 75vw, 902px',
            ));
            echo '</div>';
        }
        if ($count > 1) {
            echo '<div class="maika-slider-controls"><div class="maika-slider-dots" role="group" aria-label="Chọn ảnh">';
            foreach ($slides as $index => $image) {
                echo '<button type="button" class="maika-slider-dot" aria-label="Xem ảnh ' . esc_attr($index + 1) . '" aria-pressed="' . (0 === $index ? 'true' : 'false') . '"></button>';
            }
            echo '</div></div>';
        }
        echo '</div>';
    } elseif (current_user_can('edit_post', $front_id)) {
        echo '<div class="maika-banner-empty"><p>Thêm ảnh slide trang chủ</p><a href="' . esc_url(get_edit_post_link($front_id)) . '">Chọn ảnh trong mục Banner trang chủ →</a></div>';
    }
    echo '</div></section>';
}
function maika_home_category_title($title, $instance, $id_base) {
    return 'woocommerce_product_categories' === $id_base && !$title ? 'Danh mục sản phẩm' : $title;
}
add_action('template_redirect', function () {
    if (is_front_page()) {
        remove_action('genesis_sidebar', 'genesis_do_sidebar');
    }
});
add_filter('genesis_pre_get_option_site_layout', function ($layout) {
    return is_front_page() ? 'full-width-content' : $layout;
});
add_action('wp_enqueue_scripts', function () {
    if (is_front_page()) {
        $path = get_stylesheet_directory() . '/custom/home-slider.js';
        wp_enqueue_script('maika-home-slider', get_stylesheet_directory_uri() . '/custom/home-slider.js', array(), filemtime($path), true);
    }
});
add_action('genesis_after_header', 'maika_home_benefits', 15);
function maika_home_benefits() {
    if (!is_front_page() || !function_exists('get_field')) {
        return;
    }
    $items = get_field('maika_home_benefits', (int) get_option('page_on_front'));
    if (!is_array($items) || !$items) {
        return;
    }
    $placeholders = array('fa-boxes-stacked', 'fa-truck-fast', 'fa-screwdriver-wrench', 'fa-shield-halved');
    echo '<section class="maika-benefits wrap" aria-label="Lợi ích mua hàng tại Maika"><ul class="maika-benefits-grid">';
    foreach ($items as $index => $item) {
        if (empty($item['title']) && empty($item['description'])) {
            continue;
        }
        echo '<li class="maika-benefit"><div class="maika-benefit-icon" aria-hidden="true">';
        if (!empty($item['icon'])) {
            echo wp_get_attachment_image((int) $item['icon'], 'full', false, array('alt' => '', 'loading' => 'lazy'));
        } else {
            echo '<i class="fa-solid ' . esc_attr($placeholders[$index % count($placeholders)]) . '"></i>';
        }
        echo '</div><div class="maika-benefit-copy">';
        if (!empty($item['title'])) {
            echo '<h2>' . esc_html($item['title']) . '</h2>';
        }
        if (!empty($item['description'])) {
            echo '<p>' . nl2br(esc_html($item['description'])) . '</p>';
        }
        echo '</div></li>';
    }
    echo '</ul></section>';
}
