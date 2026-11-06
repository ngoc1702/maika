<?php
/** Product layouts and editable contact buttons. */
add_theme_support('woocommerce');
add_filter('genesis_site_layout', function ($layout) {
    return function_exists('is_woocommerce') && (is_woocommerce() || is_singular('product')) ? 'full-width-content' : $layout;
}, 30);
add_filter('loop_shop_columns', function ($columns) {
    return is_shop() || is_product_taxonomy() || is_search() ? 5 : $columns;
});
add_action('widgets_init', function () {
    register_sidebar(array(
        'id' => 'maika-product-contact',
        'name' => 'Sản phẩm - Nút gọi điện và Zalo',
        'description' => 'Hai nút liên hệ ở bên phải ảnh sản phẩm. Thêm widget Maika - Liên hệ sản phẩm để sửa số điện thoại và link Zalo.',
        'before_widget' => '<div class="maika-product-contact-widget">',
        'after_widget' => '</div>',
        'before_title' => '',
        'after_title' => '',
    ));
    register_widget('Maika_Product_Contact_Widget');
});
class Maika_Product_Contact_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('maika_product_contact', 'Maika - Liên hệ sản phẩm', array('description' => 'Cấu hình nút gọi điện và Zalo trên trang chi tiết sản phẩm.'));
    }
    public function widget($args, $instance) {
        $phone = preg_replace('/[^+0-9]/', '', $instance['phone'] ?? '0396767286');
        $zalo = $instance['zalo_url'] ?? 'https://zalo.me/0396767286';
        echo $args['before_widget'];
        echo '<div class="maika-product-contact-buttons">';
        if ($phone) {
            echo '<a class="maika-product-call" href="' . esc_url('tel:' . $phone) . '"><i class="fa-solid fa-phone-volume" aria-hidden="true"></i><span>' . esc_html($instance['phone_label'] ?? 'Gọi tư vấn ngay') . '</span></a>';
        }
        if ($zalo) {
            echo '<a class="maika-product-zalo" href="' . esc_url($zalo) . '" target="_blank" rel="noopener noreferrer"><img src="' . esc_url(get_stylesheet_directory_uri() . '/images/i2.png') . '" width="28" height="28" alt=""><span>' . esc_html($instance['zalo_label'] ?? 'Tư vấn qua Zalo') . '</span></a>';
        }
        echo '</div>' . $args['after_widget'];
    }
    public function form($instance) {
        $fields = array('phone' => array('Số điện thoại', '0396767286'), 'phone_label' => array('Chữ trên nút gọi', 'Gọi tư vấn ngay'), 'zalo_url' => array('Đường dẫn Zalo', 'https://zalo.me/0396767286'), 'zalo_label' => array('Chữ trên nút Zalo', 'Tư vấn qua Zalo'));
        foreach ($fields as $key => $field) {
            echo '<p><label for="' . esc_attr($this->get_field_id($key)) . '">' . esc_html($field[0]) . '</label><input class="widefat" type="' . ($key === 'zalo_url' ? 'url' : 'text') . '" id="' . esc_attr($this->get_field_id($key)) . '" name="' . esc_attr($this->get_field_name($key)) . '" value="' . esc_attr($instance[$key] ?? $field[1]) . '"></p>';
        }
    }
    public function update($new_instance, $old_instance) {
        return array('phone' => sanitize_text_field($new_instance['phone'] ?? ''), 'phone_label' => sanitize_text_field($new_instance['phone_label'] ?? ''), 'zalo_url' => esc_url_raw($new_instance['zalo_url'] ?? ''), 'zalo_label' => sanitize_text_field($new_instance['zalo_label'] ?? ''));
    }
}
add_action('wp_enqueue_scripts', function () {
    if (is_singular('product')) {
        $path = get_stylesheet_directory() . '/custom/product-gallery.js';
        wp_enqueue_script('maika-product-gallery', get_stylesheet_directory_uri() . '/custom/product-gallery.js', array(), filemtime($path), true);
    }
});

// Twenty products per archive page; related products share the five-column cards.
add_filter('loop_shop_per_page', function () { return 20; }, 30);
add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = 5;
    $args['columns'] = 5;
    return $args;
});
// Keep old shop bookmarks working after renaming the shop page.
add_action('template_redirect', function () {
    global $wp;
    $old_path = trim((string) get_option('maika_legacy_shop_path'), '/');
    if ($old_path && preg_match('#^' . preg_quote($old_path, '#') . '(?:/page/(\d+))?/?$#', $wp->request, $match)) {
        $url = wc_get_page_permalink('shop');
        if (!empty($match[1])) { $url = trailingslashit($url) . 'page/' . absint($match[1]) . '/'; }
        if (!empty($_SERVER['QUERY_STRING'])) { $url .= '?' . wp_unslash($_SERVER['QUERY_STRING']); }
        wp_safe_redirect($url, 301);
        exit;
    }
});

/** Display all-caps imports in title case without changing saved product names. */
function maika_product_display_name($name) {
    if (mb_strtoupper($name, 'UTF-8') === $name && mb_strtolower($name, 'UTF-8') !== $name) {
        return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    }
    return $name;
}

// Sticky navigation is shared by the homepage and inner pages.
add_action('wp_enqueue_scripts', function () {
    $path = get_stylesheet_directory() . '/custom/sticky-navigation.js';
    wp_enqueue_script('maika-sticky-navigation', get_stylesheet_directory_uri() . '/custom/sticky-navigation.js', array('maika-site-navigation'), filemtime($path), true);
});
// All public search forms look up products through the native WordPress query.
add_filter('get_search_form', function ($form) {
    if (!is_admin() && !preg_match('/name=["\x27]post_type["\x27]/', $form)) {
        $form = str_replace('</form>', '<input type="hidden" name="post_type" value="product"></form>', $form);
    }
    return $form;
});
add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->set('post_type', 'product');
        // Search product names only; descriptions must not create unrelated matches.
        $query->set('search_columns', array('post_title'));
        $query->set('posts_per_page', 20);
    }
}, 5);
add_filter('body_class', function ($classes) {
    if (is_search()) {
        $classes = array_merge($classes, array('woocommerce', 'woocommerce-page', 'maika-product-search'));
    }
    return array_unique($classes);
});

// Keep the results grid visible even when only one product name matches.
add_filter('woocommerce_redirect_single_search_result', '__return_false');

add_action('wp_enqueue_scripts', function () {
    $path = get_stylesheet_directory() . '/custom/mobile-navigation.js';
    wp_enqueue_script('maika-mobile-navigation', get_stylesheet_directory_uri() . '/custom/mobile-navigation.js', array('maika-sticky-navigation'), filemtime($path), true);
});

// Synchronize the classic product editor's selected image before saving.
add_action('admin_enqueue_scripts', function ($hook) {
    $screen = get_current_screen();
    if (!in_array($hook, array('post.php', 'post-new.php'), true) || !$screen || $screen->post_type !== 'product') { return; }
    $path = get_stylesheet_directory() . '/custom/product-featured-image-admin.js';
    wp_enqueue_script('maika-product-featured-image-admin', get_stylesheet_directory_uri() . '/custom/product-featured-image-admin.js', array('media-editor'), filemtime($path), true);
});
