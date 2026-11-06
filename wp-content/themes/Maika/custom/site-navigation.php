<?php
/** Shared navigation on all pages; the homepage retains its banner layout. */
add_action('genesis_after_header', 'maika_site_navigation', 5);
function maika_site_navigation() {
    if (is_front_page() || !is_active_sidebar('sidebar')) {
        return;
    }
    echo '<nav class="maika-home-hero maika-inner-navigation wrap" aria-label="Danh mục và menu chính">';
    add_filter('widget_title', 'maika_home_category_title', 10, 3);
    add_filter('dynamic_sidebar_params', 'maika_site_navigation_widget_params');
    remove_filter('widget_display_callback', 'maika_hide_relocated_navigation_widgets', 10);
    add_filter('list_product_cats', 'maika_navigation_category_image', 10, 2);
    dynamic_sidebar('sidebar');
    remove_filter('list_product_cats', 'maika_navigation_category_image', 10);
    add_filter('widget_display_callback', 'maika_hide_relocated_navigation_widgets', 10, 3);
    remove_filter('dynamic_sidebar_params', 'maika_site_navigation_widget_params');
    remove_filter('widget_title', 'maika_home_category_title', 10);
    echo '</nav>';
}
function maika_navigation_category_image($name, $category) {
    if ((int) $category->parent !== 0) {
        return $name;
    }
    $image_id = (int) get_term_meta($category->term_id, 'thumbnail_id', true);
    $image = $image_id ? wp_get_attachment_image($image_id, 'thumbnail', false, array(
        'class' => 'maika-category-image',
        'alt' => '',
        'width' => 25,
        'height' => 25,
    )) : '';
    return $image . '<span>' . $name . '</span>';
}
function maika_site_navigation_widget_params($params) {
    if (isset($params[0]['widget_id']) && strpos($params[0]['widget_id'], 'woocommerce_product_categories-') === 0) {
        $params[0]['before_title'] = '<div class="widget-title widgettitle"><button type="button" class="maika-category-toggle" aria-expanded="false" aria-controls="maika-site-categories">';
        $params[0]['after_title'] = '</button></div>';
    }
    return $params;
}
// The saved header widgets belong in the navigation, not a second copy in the sidebar.
add_filter('widget_display_callback', 'maika_hide_relocated_navigation_widgets', 10, 3);
function maika_hide_relocated_navigation_widgets($instance, $widget, $args) {
    if (is_admin() || is_front_page() || empty($args['widget_id'])) {
        return $instance;
    }
    $sidebars = wp_get_sidebars_widgets();
    $is_navigation = in_array($widget->id_base, array('nav_menu', 'woocommerce_product_categories'), true);
    if ($is_navigation && in_array($args['widget_id'], $sidebars['sidebar'] ?? array(), true)) {
        return false;
    }
    return $instance;
}
add_action('wp_enqueue_scripts', function () {
    $path = get_stylesheet_directory() . '/custom/site-navigation.js';
    wp_enqueue_script('maika-site-navigation', get_stylesheet_directory_uri() . '/custom/site-navigation.js', array(), filemtime($path), true);
});
