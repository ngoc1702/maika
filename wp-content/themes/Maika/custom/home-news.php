<?php
/** Homepage articles selected and ordered in ACF. */
add_action('genesis_after_header', 'maika_home_featured_news', 25);
function maika_home_featured_news() {
    if (!is_front_page() || !function_exists('get_field')) {
        return;
    }
    $front = (int) get_option('page_on_front');
    $selected = get_field('maika_featured_posts', $front);
    $selected = is_array($selected) ? array_values(array_unique(array_filter(array_map('absint', $selected)))) : array();
    $heading = get_field('maika_news_heading', $front) ?: 'Tin nổi bật';
    if (!$selected) {
        if (current_user_can('edit_post', $front)) {
            echo '<section class="maika-featured-news"><div class="wrap"><h2 class="maika-news-heading">' . esc_html($heading) . '</h2><p class="maika-news-empty"><a href="' . esc_url(get_edit_post_link($front)) . '">Chọn bài trong tab Tin nổi bật của trang chủ →</a></p></div></section>';
        }
        return;
    }
    $query = new WP_Query(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'post__in' => $selected,
        'orderby' => 'post__in',
        'posts_per_page' => count($selected),
        'ignore_sticky_posts' => true,
        'no_found_rows' => true,
    ));
    if (!$query->have_posts()) {
        return;
    }
    $section_bg = wp_get_attachment_image_url((int) get_field('maika_news_bg', $front), 'full') ?: get_stylesheet_directory_uri() . '/images/nen-yte.png';
    echo '<section class="maika-featured-news" aria-labelledby="maika-news-heading" style="--maika-section-background:url(' . esc_url($section_bg) . ')"><div class="wrap">';
    echo '<h2 id="maika-news-heading" class="maika-news-heading">' . esc_html($heading) . '</h2><div class="blog-wrapper maika-news-grid">';
    while ($query->have_posts()) {
        $query->the_post();
        get_template_part('custom/post-card', null, array('heading_tag' => 'h3'));
    }
    wp_reset_postdata();
    echo '</div>';
    $news_category = get_category_by_slug('tin-tuc');
    $news_url = $news_category ? get_category_link($news_category) : home_url('/danh-muc/tin-tuc/');
    $more_url = get_field('maika_news_more_url', $front) ?: $news_url;
    echo '<div class="maika-news-actions"><a class="maika-news-more" href="' . esc_url($more_url) . '">Xem thêm <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a></div>';
    echo '</div></section>';
}