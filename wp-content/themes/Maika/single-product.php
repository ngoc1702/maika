<?php
add_filter('genesis_pre_get_option_site_layout', 'adsdigi_site_layout');
function adsdigi_site_layout($layout)
{
    // Áp dụng cho category archive 
    $layout = 'content-sidebar'; // hoặc 'sidebar-content'   
    return $layout;
}

// Xóa post-info và post-meta của Genesis
remove_action('genesis_entry_header', 'genesis_post_info', 12);
remove_action('genesis_entry_footer', 'genesis_post_meta');

// Xóa social share và rating mặc định
add_action('genesis_before_loop', 'remove_caia_rating');
function remove_caia_rating()
{
    global $caia_rating, $caia_social;

    $star_pri = has_filter('the_content', array($caia_rating, 'add_rating_content_bottom'));
    if ($star_pri !== false) {
        remove_filter('the_content', array($caia_rating, 'add_rating_content_bottom'), $star_pri);
    }

    $social_pri = has_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'));
    if ($social_pri !== false) {
        remove_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'), $social_pri);
    }
}

// Thêm thông tin dưới nội dung bài viết
add_action('genesis_entry_footer', 'caia_add_content_post_meta', 1);
function caia_add_content_post_meta()
{
    echo '<div class="content-info-meta section">';
    echo '<div class="rating-social section">';
    echo do_shortcode('[caia_rating]');
    echo do_shortcode('[caia_social_share]');
    echo '</div>';
    echo '</div>';
}

// Custom layout cho trang sản phẩm
add_action('genesis_before_content_sidebar_wrap', 'add_thongtin_sp');
function add_thongtin_sp()
{
    if ( ! function_exists('wc_get_product') ) return;

    $product_id = get_queried_object_id();
    $product    = wc_get_product($product_id);
    if ( ! $product ) return;

    $anhsp         = rwmb_meta('anhsp', [], $product_id);
    $regular_price = $product->get_regular_price();
    $sale_price    = $product->get_sale_price();
    $excerpt       = get_the_excerpt($product_id);

    // Giá số để đổ vào form (ưu tiên sale nếu có)
    $price_number = (float) ($sale_price !== '' ? $sale_price : $regular_price);

    // Meta bật trả góp
    $enable = get_post_meta($product_id, 'enable_installment', true);

    echo '<div class="content-sanpham">';

        /* Cột trái: Slider */
        echo '<div class="slide_sp">';

            if ( ! empty($anhsp) ) {
                echo '<div class="slider-for">';
                foreach ($anhsp as $image) {
                    if (empty($image['full_url'])) continue;
                    echo '<img src="' . esc_url($image['full_url']) . '" alt="">';
                }
                echo '</div>';

                echo '<div class="slider-nav">';
                foreach ($anhsp as $image) {
                    if (empty($image['full_url'])) continue;
                    echo '<img src="' . esc_url($image['full_url']) . '" alt="">';
                }
                echo '</div>';

            } else {
                $thumb_id = get_post_thumbnail_id($product_id);
                if ($thumb_id) {
                    $thumb_url = wp_get_attachment_image_url($thumb_id, 'full');
                    echo '<div class="slider-for"><img src="' . esc_url($thumb_url) . '" alt=""></div>';
                    echo '<div class="slider-nav"><img src="' . esc_url($thumb_url) . '" alt=""></div>';
                }
            }

        echo '</div>'; // .slide_sp


        /* Cột phải: Info */
        echo '<div class="info_sp">';

            echo '<h1 class="product-title">' . esc_html(get_the_title($product_id)) . '</h1>';

        echo '<div class="product-price">';
echo '<span class="price-label">Giá: </span>';

$has_sale    = ($sale_price !== '' && floatval($sale_price) > 0);
$has_regular = ($regular_price !== '' && floatval($regular_price) > 0);

if ( ! $has_sale && ! $has_regular ) {
    echo '<span class="price-contact">Liên hệ</span>';
} elseif ( $has_sale && $has_regular ) {
    echo '<span class="price-new">' . wc_price($sale_price) . '</span>';
    echo '<span class="price-old"><strike>' . wc_price($regular_price) . '</strike></span>';
} else {
    // Chỉ có regular (hoặc sale không hợp lệ)
    echo '<span class="price-current">' . wc_price($regular_price) . '</span>';
}

echo '</div>';


            if ($excerpt) {
                echo '<div class="product-excerpt">' . wp_kses_post($excerpt) . '</div>';
            }

            if (is_active_sidebar('content-luuy')) {
                echo '<div class="content-luuy section"><div class="wrap">';
                dynamic_sidebar('Sản phẩm - Lưu ý');
                echo '</div></div>';
            }

            /* Nút mua + trả góp */
          echo '<div class="product-buy-form-wrap">';
echo '<p class="btn-buy">Mua ngay</p>';

if ( ! empty($price_number) && floatval($price_number) > 0 ) {
    echo '<button type="button"
                class="button ad-installment"
                data-product_id="' . esc_attr($product_id) . '"
                data-price="' . esc_attr($price_number) . '">
                Trả góp
          </button>';
}

echo '</div>';

        echo '</div>'; // .info_sp

    echo '</div>'; // .content-sanpham
}


// Thêm bài viết liên quan
add_action('genesis_after_content_sidebar_wrap', 'caia_add_product_YARPP', 9);
function caia_add_product_YARPP()
{
    if (function_exists('yarpp_related')) {
        yarpp_related([
            'post_type' => 'product',
            'threshold' => 1,
            'template' => 'yarpp-template-product.php',
            'limit' => 4,
        ]);
    }
}


// Thêm khu vực tư vấn
add_action('genesis_before_sidebar_widget_area', 'add_product_info');
function add_product_info()
{
    if (is_active_sidebar('content-tuvan')) {
        echo '<div class="content-tuvan section"><div class="wrap">';
        dynamic_sidebar('Sản phẩm - Hỗ trợ tư vấn');
        echo '</div></div>';
    }
}

// Thêm khu vực hỗ trợ mua hàng
add_action('genesis_before_sidebar_widget_area', 'add_product_muahang');
function add_product_muahang()
{
    if (is_active_sidebar('content-muahang')) {
        echo '<div class="content-muahang section"><div class="wrap">';
        dynamic_sidebar('Sản phẩm - Hỗ trợ mua hàng');
        echo '</div></div>';
    }
}

genesis();
