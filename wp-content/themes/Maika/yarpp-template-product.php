


<?php
if ($related_query->have_posts()):
    echo '<div class="wrap"><div class="widget"><div class="widgettitle">Sản phẩm liên quan</div><div class="main-posts">';
    while ($related_query->have_posts()) : $related_query->the_post(); 
        echo '<div class="product">';
            ?>
            <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="alignleft">
                <?php the_post_thumbnail('product-image'); ?>
            </a>
            <?php
			 $terms = get_the_terms(get_the_ID(), 'product_cat');
            // if ($terms && !is_wp_error($terms)) {
            //     echo '<ul class="product-categories">';
            //     foreach ($terms as $term) {
            //         echo '<li class="name-categories">' . esc_html($term->name) . '</li>';
            //     }
            //     echo '</ul>';
            // }
            echo '<p class="widget-item-title"><a href="' . get_permalink() . '" title="' . get_the_title() . '" rel="bookmark">' . get_the_title() . '</a></p>';
            $product = wc_get_product(get_the_ID());
            if ($product) {
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                echo '<div class="product-price">';
                if ($sale_price) {
                    echo '<span class="price-new">' . wc_price($sale_price) . '</span> ';
                    echo '<span class="price-old"><del>' . wc_price($regular_price) . '</del></span>';
                } else {
                    echo '<span class="price-current">' . wc_price($regular_price) . '</span>';
                }
                echo '</div>';
            }

        echo '</div>';
    endwhile;
    echo '</div></div></div>';
    wp_reset_postdata();
endif;
?>
