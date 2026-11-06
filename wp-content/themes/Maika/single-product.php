<?php
/** Product detail: gallery, short description and full description. */
defined('ABSPATH') || exit;
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', function () {
    while (have_posts()) {
        the_post();
        global $product, $caia_rating, $caia_social;
        $product = wc_get_product(get_the_ID());
        if (!$product) { continue; }
        if (post_password_required()) { echo get_the_password_form(); continue; }
        // Product descriptions use the site's typography without blog-only extras.
        foreach (array(array($caia_rating, 'add_rating_content_bottom'), array($caia_social, 'add_native_share_button_at_bottom')) as $callback) {
            $priority = has_filter('the_content', $callback);
            if ($priority !== false) { remove_filter('the_content', $callback, $priority); }
        }
        $image_ids = array_merge(array($product->get_image_id()), $product->get_gallery_image_ids(), get_post_meta($product->get_id(), 'anhsp', false));
        $image_ids = array_values(array_unique(array_filter(array_map('absint', $image_ids), 'wp_attachment_is_image')));
        ?>
        <article id="product-<?php the_ID(); ?>" <?php wc_product_class('maika-product-detail', $product); ?>>
            <?php woocommerce_output_all_notices(); ?>
            <div class="maika-product-overview">
                <div class="maika-product-gallery" data-maika-product-gallery role="region" aria-label="Ảnh sản phẩm">
                    <div class="maika-product-images">
                        <?php if ($image_ids) : ?>
                            <?php foreach ($image_ids as $index => $image_id) : ?>
                                <div id="maika-product-image-<?php echo esc_attr($index); ?>" class="maika-product-image" <?php echo $index ? 'hidden' : ''; ?>>
                                    <?php echo wp_get_attachment_image($image_id, 'woocommerce_single', false, array('alt' => get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: $product->get_name(), 'loading' => $index ? 'lazy' : 'eager', 'fetchpriority' => $index ? 'auto' : 'high', 'sizes' => '(max-width: 767px) calc(100vw - 32px), 560px')); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="maika-product-image"><?php echo wc_placeholder_img('woocommerce_single'); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if (count($image_ids) > 1) : ?>
                        <div class="maika-product-thumbnails" aria-label="Chọn ảnh sản phẩm">
                            <?php foreach ($image_ids as $index => $image_id) : ?>
                                <button type="button" data-product-image="<?php echo esc_attr($index); ?>" aria-label="<?php echo esc_attr('Xem ảnh ' . ($index + 1)); ?>" aria-controls="maika-product-image-<?php echo esc_attr($index); ?>" aria-pressed="<?php echo $index ? 'false' : 'true'; ?>">
                                    <?php echo wp_get_attachment_image($image_id, 'woocommerce_gallery_thumbnail', false, array('alt' => '', 'loading' => 'lazy', 'draggable' => 'false')); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="maika-product-summary">
                    <h1 class="maika-product-title"><?php echo esc_html(maika_product_display_name($product->get_name())); ?></h1>
                    <div class="maika-product-price"><span>Giá: </span><?php echo $product->get_price() !== '' ? wp_kses_post($product->get_price_html()) : '<span>Liên hệ</span>'; ?></div>
                    <?php if ($product->get_short_description()) : ?>
                        <div class="maika-product-short-description"><?php echo apply_filters('woocommerce_short_description', $product->get_short_description()); ?></div>
                    <?php endif; ?>
                    <?php if ($product->get_sku()) : ?><p class="maika-product-sku">Mã sản phẩm: <?php echo esc_html($product->get_sku()); ?></p><?php endif; ?>
                    <?php if (is_active_sidebar('content-luuy')) : ?><div class="maika-product-note"><?php dynamic_sidebar('content-luuy'); ?></div><?php endif; ?>
                    <?php if (is_active_sidebar('maika-product-contact')) { dynamic_sidebar('maika-product-contact'); } else { the_widget('Maika_Product_Contact_Widget', array(), array('before_widget' => '<div class="maika-product-contact-widget">', 'after_widget' => '</div>')); } ?>
                </div>
            </div>
            <details class="maika-product-description" open>
                <summary><h2>Mô tả sản phẩm</h2></summary>
                <div class="entry-content maika-product-description-body"><?php the_content(); ?></div>
            </details>
            <?php if ($product->has_attributes() || $product->has_dimensions() || $product->has_weight()) : ?>
                <details class="maika-product-description" open><summary><h2>Thông tin bổ sung</h2></summary><div class="entry-content maika-product-description-body"><?php wc_display_product_attributes($product); ?></div></details>
            <?php endif; ?>
            <?php if (is_active_sidebar('maika-product-benefits')) { dynamic_sidebar('maika-product-benefits'); } ?>
            <?php woocommerce_output_related_products(); ?>
        </article>
        <?php
    }
});
genesis();
