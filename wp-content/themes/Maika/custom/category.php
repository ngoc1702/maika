<?php
get_header();
?>
<h1 class="cat-title"><?php single_cat_title(); ?></h1>
<?php
$desc = category_description(); // lấy mô tả danh mục hiện tại (đã có HTML)
if ( ! empty( $desc ) ) : ?>
  <div class="cat-description">
    <?php echo $desc; ?>
  </div>
<?php endif; ?>


<div class="blog-wrapper category-blog">

<?php if ( have_posts() ) : ?>
    
    <?php while ( have_posts() ) : the_post(); ?>

        <article class="blog-item">
            <a href="<?php the_permalink(); ?>" class="blog-thumb">
                <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'medium' );
                }
                ?>
            </a>

            <div class="blog-info">
                <h2 class="blog-title">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>

                <div class="blog-meta">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span class="blog-date">
                        <?php echo get_the_date('d/m/Y'); ?>
                    </span>
                </div>

                <div class="blog-excerpt">
                    <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                </div>
            </div>
        </article>

    <?php endwhile; ?>

</div>

<div class="pagination">
    <?php
    echo paginate_links( array(
        'prev_text' => '«',
        'next_text' => '»',
    ) );
    ?>
</div>

<?php else : ?>

    <p>Không có bài viết nào trong chuyên mục này.</p>

<?php endif; ?>

<?php get_footer(); ?>
