<?php
/**
 * Plugin Name: Admin Safe Mode (Theme Isolation)
 * Description: Vô hiệu hóa ảnh hưởng của theme trong WP Admin để debug mất dữ liệu Woo.
 */

if ( ! defined('ABSPATH') ) exit;

/**
 * 1) Dequeue mọi CSS/JS của theme/child theme trong ADMIN
 */
add_action('admin_enqueue_scripts', function () {
  global $wp_styles, $wp_scripts;
  $theme_uris = array_filter([
    get_stylesheet_directory_uri(),
    get_template_directory_uri(),
  ]);

  if ( $wp_styles instanceof WP_Styles ) {
    foreach ($wp_styles->queue as $handle) {
      if ( ! isset($wp_styles->registered[$handle]) ) continue;
      $src = $wp_styles->registered[$handle]->src;
      foreach ($theme_uris as $uri) {
        if ($src && strpos($src, $uri) !== false) {
          wp_dequeue_style($handle);
        }
      }
    }
  }
  if ( $wp_scripts instanceof WP_Scripts ) {
    foreach ($wp_scripts->queue as $handle) {
      if ( ! isset($wp_scripts->registered[$handle]) ) continue;
      $src = $wp_scripts->registered[$handle]->src;
      foreach ($theme_uris as $uri) {
        if ($src && strpos($src, $uri) !== false) {
          wp_dequeue_script($handle);
        }
      }
    }
  }

  // 2) Hotfix: ép hiện các panel/metabox Woo trong admin để bạn nhìn thấy ngay
  wp_add_inline_style('wp-admin', '
    .woocommerce_options_panel,
    .woocommerce_variable_attributes,
    .postbox,
    #woocommerce-product-data,
    .wc-metaboxes-wrapper,
    #poststuff, #post-body { 
      display:block !important; 
      visibility:visible !important; 
      opacity:1 !important;
    }
  ');
}, 99);

/**
 * 3) Ngăn code theme “bóp méo” query admin
 * (Theme hay dùng pre_get_posts quá tay → admin trống dữ liệu)
 */
add_action('pre_get_posts', function($q){
  if ( is_admin() ) {
    // Không cho theme sửa main query ở admin
    // (Giữ chỗ, không chỉnh gì thêm)
  }
}, 0);

/**
 * 4) (TÙY CHỌN) Gỡ các filter dễ gây rỗng dữ liệu trong ADMIN
 * Bật nếu vẫn không thấy dữ liệu sau khi dequeue assets.
 */
add_action('admin_init', function(){
  // Gỡ mọi filter the_content/get_post_metadata trong ADMIN (cẩn trọng)
  // remove_all_filters('the_content');
  // remove_all_filters('get_post_metadata');

  // Khôi phục tab/panel Product Data nếu có filter phá hoại
  add_filter('woocommerce_product_data_tabs', function($tabs){
    if ( empty($tabs) || !is_array($tabs) ) {
      $tabs = [
        'general'   => ['label'=>__('General','woocommerce'),'target'=>'general_product_data','priority'=>10],
        'inventory' => ['label'=>__('Inventory','woocommerce'),'target'=>'inventory_product_data','priority'=>20],
        'shipping'  => ['label'=>__('Shipping','woocommerce'),'target'=>'shipping_product_data','priority'=>30],
      ];
    }
    return $tabs;
  }, 1);
}, 1);
