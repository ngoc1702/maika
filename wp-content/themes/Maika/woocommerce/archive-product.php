<?php
/**
 * Product archive with a category-only sidebar.
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>

<main class="shop-wrapper" id="main-content">
	<aside class="shop-sidebar" aria-label="Danh mục sản phẩm">
		<?php
		the_widget(
			'WC_Widget_Product_Categories',
			array(
				'title'        => 'Danh mục sản phẩm',
				'count'        => 1,
				'hierarchical' => 1,
				'dropdown'     => 0,
				'hide_empty'   => 0,
			),
			array(
				'before_widget' => '<section class="widget widget_product_categories">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
		?>
	</aside>

	<section class="shop-products" aria-label="Danh sách sản phẩm">
		<?php
		do_action( 'woocommerce_before_main_content' );

		if ( woocommerce_product_loop() ) {
			do_action( 'woocommerce_before_shop_loop' );
			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			do_action( 'woocommerce_no_products_found' );
		}

		do_action( 'woocommerce_after_main_content' );
		?>
	</section>
</main>

<?php
get_footer( 'shop' );
