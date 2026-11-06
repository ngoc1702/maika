<?php
/**
 * Template Name: Giới thiệu Maika
 * Template Post Type: page
 */
if (!defined('ABSPATH')) { exit; }
add_filter('body_class', function ($classes) { $classes[] = 'maika-about-page'; return $classes; });
add_filter('genesis_site_layout', function () { return 'full-width-content'; });
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', function () {
    while (have_posts()) {
        the_post();
        echo '<article class="maika-about-article"><header class="entry-header"><h1 class="entry-title">' . esc_html(get_the_title()) . '</h1></header><div class="entry-content">';
        the_content();
        echo '</div></article>';
    }
});
genesis();
