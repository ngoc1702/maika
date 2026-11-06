<?php
/** Blog category queries use twelve published posts per page. */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || !$query->is_category()) {
        return;
    }
    $query->set('posts_per_page', 12);
    $query->set('ignore_sticky_posts', true);
});
// Preserve category bookmarks, feeds and pagination after changing the base.
add_action('template_redirect', function () {
    global $wp;
    if (preg_match('#^category/(.+)$#', $wp->request, $match)) {
        $base = get_option('category_base') ?: 'category';
        if ($base === 'category') { return; }
        $url = home_url(user_trailingslashit($base . '/' . $match[1], 'category'));
        if (!empty($_SERVER['QUERY_STRING'])) { $url .= '?' . wp_unslash($_SERVER['QUERY_STRING']); }
        wp_safe_redirect($url, 301);
        exit;
    }
}, 1);
