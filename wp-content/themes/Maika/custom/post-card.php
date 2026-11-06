<?php /** Shared article card for category archives and homepage news. */ ?>
<article class="blog-item">
    <a href="<?php the_permalink(); ?>" class="blog-thumb" aria-label="<?php echo esc_attr(get_the_title()); ?>">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium', array('alt' => '', 'loading' => 'lazy')); ?>
        <?php endif; ?>
    </a>
    <div class="blog-info">
        <?php $heading_tag = isset($args['heading_tag']) && $args['heading_tag'] === 'h3' ? 'h3' : 'h2'; ?>
        <<?php echo $heading_tag; ?> class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo $heading_tag; ?>>
        <div class="blog-meta">
            <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
            <time class="blog-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d/m/Y')); ?></time>
        </div>
        <div class="blog-excerpt"><?php echo esc_html(wp_strip_all_tags(get_the_excerpt())); ?></div>
    </div>
</article>
