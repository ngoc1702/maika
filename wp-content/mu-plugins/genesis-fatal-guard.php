<?php
/**
 * Tạm gỡ filter của Genesis gây fatal ở trang cài plugin.
 * Ảnh hưởng: chỉ mất sắp xếp/đề xuất plugin của Genesis, mọi thứ khác bình thường.
 */
add_action('admin_init', function () {
    if ( has_filter('plugins_api_result', 'genesis_admin_plugins_api_result') ) {
        remove_filter('plugins_api_result', 'genesis_admin_plugins_api_result', 10);
        remove_filter('plugins_api_result', 'genesis_admin_plugins_api_result', 10, 3);
    }
}, 1);
