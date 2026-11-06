<?php
/** Shared minimal card for product archives and related products. */
defined('ABSPATH') || exit;
global $product;
if (!$product || !$product->is_visible()) { return; }
?>
<li <?php wc_product_class('maika-product-card', $product); ?>>
    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
        <?php woocommerce_template_loop_product_thumbnail(); ?>
        <h2 class="woocommerce-loop-product__title"><?php echo esc_html(maika_product_display_name($product->get_name())); ?></h2>
    </a>
</li>
