<?php
/** Selected product category rails, immediately before homepage consultation. */
add_action('genesis_after_header', function () {
    if (!is_front_page() || !function_exists('get_field') || !function_exists('WC')) { return; }
    $sections = get_field('maika_home_product_sections', (int) get_option('page_on_front'));
    if (is_array($sections)) { maika_render_home_product_sections($sections); }
}, 17);

function maika_render_home_product_sections($sections) {
    foreach ($sections as $section) {
        if (isset($section['enabled']) && !$section['enabled']) { continue; }
        $term = get_term(absint($section['category'] ?? 0), 'product_cat');
        if (!$term || is_wp_error($term)) { continue; }
        $url = get_term_link($term);
        if (is_wp_error($url)) { continue; }
        $query = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => min(20, max(4, absint($section['limit'] ?? 8))),
            'orderby' => 'date',
            'order' => 'DESC',
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
            'tax_query' => WC()->query->get_tax_query(array(array('taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $term->term_id, 'include_children' => true))),
            'meta_query' => WC()->query->get_meta_query(),
        ));
        if (!$query->have_posts()) { continue; }
        $title = $term->name;
        $heading_id = wp_unique_id('maika-category-heading-');
        $previous_product = $GLOBALS['product'] ?? null;
        $previous_loop = $GLOBALS['woocommerce_loop'] ?? null;
        wc_setup_loop(array('columns' => 5));
        echo '<section class="maika-home-category-products" aria-labelledby="' . esc_attr($heading_id) . '"><div class="wrap"><div class="maika-home-category-block">';
        echo '<header class="maika-home-category-header"><h2 id="' . esc_attr($heading_id) . '" class="maika-home-category-title">' . esc_html($title) . '</h2><a class="maika-home-category-more" href="' . esc_url($url) . '" aria-label="' . esc_attr('Xem thêm: ' . $title) . '">Xem thêm <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></a></header>';
        echo '<div class="maika-category-products-body"><ul class="products columns-5 maika-category-track" data-maika-category-track tabindex="0" aria-label="' . esc_attr('Sản phẩm: ' . $title . '. Kéo, vuốt hoặc dùng phím mũi tên để xem tiếp.') . '">';
        while ($query->have_posts()) {
            $query->the_post();
            $GLOBALS['product'] = wc_get_product(get_the_ID());
            wc_get_template_part('content', 'product');
        }
        echo '</ul></div></div></div></section>';
        wp_reset_postdata();
        $GLOBALS['product'] = $previous_product;
        if ($previous_loop === null) { wc_reset_loop(); } else { $GLOBALS['woocommerce_loop'] = $previous_loop; }
    }
}
add_action('wp_enqueue_scripts', function () {
    if (is_front_page() && function_exists('get_field') && get_field('maika_home_product_sections', (int) get_option('page_on_front'))) {
        $path = get_stylesheet_directory() . '/custom/category-products-slider.js';
        wp_enqueue_script('maika-category-products-slider', get_stylesheet_directory_uri() . '/custom/category-products-slider.js', array(), filemtime($path), true);
    }
});
