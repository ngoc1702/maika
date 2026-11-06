<?php
/** Blog category listing uses the main WordPress query and its pagination. */
get_header();
?>
<header class="maika-blog-archive-header">
    <h1 class="cat-title maika-archive-title"><?php single_cat_title(); ?></h1>
    <?php $description = category_description(); ?>
    <?php if ($description) : ?>
        <div class="cat-description"><?php echo wp_kses_post($description); ?></div>
    <?php endif; ?>
</header>

<?php if (have_posts()) : ?>
    <div class="blog-wrapper category-blog">
        <?php while (have_posts()) : the_post(); ?>
            <?php get_template_part('custom/post-card'); ?>
        <?php endwhile; ?>
    </div>
    <?php global $wp_query; ?>
    <?php if ($wp_query->max_num_pages > 1) : ?>
        <nav class="maika-blog-pagination" aria-label="Phân trang bài viết">
            <?php
            echo wp_kses_post(paginate_links(array(
                'current' => max(1, (int) get_query_var('paged')),
                'total' => $wp_query->max_num_pages,
                'type' => 'list',
                'mid_size' => 2,
                'prev_text' => '<span aria-hidden="true">«</span><span class="screen-reader-text">Trang trước</span>',
                'next_text' => '<span class="screen-reader-text">Trang sau</span><span aria-hidden="true">»</span>',
            )));
            ?>
        </nav>
    <?php endif; ?>
<?php else : ?>
    <p class="maika-blog-empty">Không có bài viết nào trong chuyên mục này.</p>
<?php endif; ?>
<?php get_footer(); ?>