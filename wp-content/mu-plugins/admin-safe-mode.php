<?php
/**
 * Plugin Name: Admin Theme Style Guard
 * Description: Keep the public theme stylesheet out of WordPress admin without overriding WooCommerce panels or admin scripts.
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', function () {
    // Admin widgets and media tools need their own theme scripts. Only block
    // the public stylesheet; WooCommerce controls its tabs and panel visibility.
    $public_stylesheet = get_stylesheet_uri();
    $styles = wp_styles();
    foreach ($styles->queue as $handle) {
        $registered = $styles->registered[$handle] ?? null;
        if ($registered && $registered->src && strtok($registered->src, '?') === $public_stylesheet) {
            wp_dequeue_style($handle);
        }
    }
}, 99);
