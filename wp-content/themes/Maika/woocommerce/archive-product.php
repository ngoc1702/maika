<?php
/** Full-width product archive, using native WooCommerce queries and pagination. */
defined('ABSPATH') || exit;
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
get_header('shop');
?>
<main class="maika-product-archive" id="main-content">
    <?php do_action('woocommerce_before_main_content'); ?>
    <header class="woocommerce-products-header">
        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?><h1 class="maika-archive-title"><?php if (is_search()) { echo esc_html(sprintf('Kết quả tìm kiếm: %s', get_search_query())); } else { woocommerce_page_title(); } ?></h1><?php endif; ?>
        <?php do_action('woocommerce_archive_description'); ?>
    </header>
    <?php
    if (woocommerce_product_loop()) {
        echo '<div class="maika-product-toolbar">';
        do_action('woocommerce_before_shop_loop');
        echo '</div>';
        woocommerce_product_loop_start();
        if (wc_get_loop_prop('total')) {
            while (have_posts()) {
                the_post();
                do_action('woocommerce_shop_loop');
                wc_get_template_part('content', 'product');
            }
        }
        woocommerce_product_loop_end();
        do_action('woocommerce_after_shop_loop');
    } else {
        do_action('woocommerce_no_products_found');
    }
    do_action('woocommerce_after_main_content');
    ?>
</main>
<?php get_footer('shop'); ?>
