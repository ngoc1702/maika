<?php
/** Heading hierarchy and related articles on single blog posts. */
add_image_size('maika-related-thumb', 240, 160, true);
add_action('template_redirect', function () {
    if (is_singular('post')) {
        remove_action('genesis_sidebar', 'genesis_do_sidebar');
        add_action('genesis_sidebar', 'maika_single_related_sidebar');
    }
});
add_filter('genesis_site_layout', function ($layout) {
    return is_singular('post') ? 'content-sidebar' : $layout;
});
function maika_related_article_ids($post_id) {
    $categories = wp_get_post_categories($post_id);
    $base = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 6,
        'fields' => 'ids',
        'post__not_in' => array((int) $post_id),
        'ignore_sticky_posts' => true,
        'no_found_rows' => true,
    );
    $ids = $categories ? get_posts(array_merge($base, array('category__in' => $categories))) : array();
    if (count($ids) < 6) {
        $ids = array_merge($ids, get_posts(array_merge($base, array(
            'posts_per_page' => 6 - count($ids),
            'post__not_in' => array_merge(array((int) $post_id), $ids),
        ))));
    }
    return array_values(array_unique(array_map('absint', $ids)));
}
function maika_single_related_sidebar() {
    if (!is_singular('post')) {
        return;
    }
    $ids = maika_related_article_ids(get_queried_object_id());
    echo '<section class="maika-related-posts" aria-labelledby="maika-related-heading"><h2 id="maika-related-heading" class="maika-related-heading">Bài viết liên quan</h2>';
    if (!$ids) {
        echo '<p class="maika-related-empty">Chưa có bài viết liên quan.</p></section>';
        return;
    }
    echo '<div class="maika-related-list">';
    foreach ($ids as $id) {
        echo '<article class="maika-related-item"><a class="maika-related-link" href="' . esc_url(get_permalink($id)) . '">';
        if (has_post_thumbnail($id)) {
            echo get_the_post_thumbnail($id, 'maika-related-thumb', array('class' => 'maika-related-thumb', 'alt' => '', 'loading' => 'lazy', 'sizes' => '106px'));
        } else {
            echo '<span class="maika-related-thumb maika-related-placeholder" aria-hidden="true"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg></span>';
        }
        echo '<h3 class="maika-related-title">' . esc_html(get_the_title($id)) . '</h3></a></article>';
    }
    echo '</div></section>';
}
// Keep the article title as the only H1; content headings begin at H2.
add_filter('the_content', function ($content) {
    if ((is_singular(array('post', 'product')) || is_page_template('page-gioithieu.php')) && in_the_loop() && is_main_query()) {
        return preg_replace('/<(\/?)(h1)(\b[^>]*)>/i', '<$1h2$3>', $content);
    }
    return $content;
}, 20);
