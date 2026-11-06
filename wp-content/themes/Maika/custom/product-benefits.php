<?php
/** Four editable service benefits before related products. */
add_action('widgets_init', function () {
    register_sidebar(array(
        'id' => 'maika-product-benefits',
        'name' => 'Sản phẩm - Cam kết dịch vụ',
        'description' => 'Dải nền xanh ở cuối chi tiết sản phẩm, trước sản phẩm tương tự. Dùng widget Maika - Cam kết dịch vụ để sửa 4 ô và thay icon.',
        'before_widget' => '<div class="maika-product-benefits-widget">',
        'after_widget' => '</div>',
        'before_title' => '',
        'after_title' => '',
    ));
    register_widget('Maika_Product_Benefits_Widget');
});
class Maika_Product_Benefits_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('maika_product_benefits', 'Maika - Cam kết dịch vụ', array('description' => 'Dải 4 lợi ích nền xanh: thay ảnh icon, tiêu đề và mô tả.'), array('width' => 440));
    }
    public static function defaults() {
        $titles = array('Vận chuyển', 'Giá tốt', 'Sản phẩm', 'Hỗ trợ');
        $descriptions = array('An toàn và nhanh chóng', 'Mức giá cạnh tranh trên thị trường', 'Đa dạng sản phẩm sẵn sàng phục vụ', 'Đội ngũ nhân sự chuyên nghiệp');
        $values = array();
        for ($i = 0; $i < 4; $i++) {
            $values['image_' . $i] = 0;
            $values['title_' . $i] = $titles[$i];
            $values['description_' . $i] = $descriptions[$i];
        }
        return $values;
    }
    public function widget($args, $instance) {
        $instance = wp_parse_args($instance, self::defaults());
        $icons = array('fa-truck-fast', 'fa-tags', 'fa-boxes-stacked', 'fa-headset');
        $items = '';
        for ($i = 0; $i < 4; $i++) {
            if (!$instance['title_' . $i] && !$instance['description_' . $i]) { continue; }
            $image = absint($instance['image_' . $i]);
            $items .= '<li class="maika-product-benefit"><span class="maika-product-benefit-icon" aria-hidden="true">';
            $items .= $image && wp_attachment_is_image($image)
                ? wp_get_attachment_image($image, 'thumbnail', false, array('alt' => '', 'loading' => 'lazy', 'sizes' => '52px'))
                : '<i class="fa-solid ' . esc_attr($icons[$i]) . '"></i>';
            $items .= '</span><div class="maika-product-benefit-copy">';
            if ($instance['title_' . $i]) { $items .= '<h3>' . esc_html($instance['title_' . $i]) . '</h3>'; }
            if ($instance['description_' . $i]) { $items .= '<p>' . esc_html($instance['description_' . $i]) . '</p>'; }
            $items .= '</div></li>';
        }
        if (!$items) { return; }
        echo $args['before_widget'] . '<section class="maika-product-benefits" aria-label="Cam kết dịch vụ"><ul>' . $items . '</ul></section>' . $args['after_widget'];
    }
    public function form($instance) {
        $instance = wp_parse_args($instance, self::defaults());
        echo '<p>Khuyến nghị icon PNG nền trong suốt, màu trắng, khoảng 120 × 120 px. Để trống dùng icon mặc định.</p>';
        for ($i = 0; $i < 4; $i++) {
            $image_key = 'image_' . $i;
            $preview = wp_get_attachment_image_url(absint($instance[$image_key]), 'thumbnail');
            echo '<div class="maika-benefit-editor" style="padding:12px 0;border-top:1px solid #ddd"><strong>Ô ' . ($i + 1) . '</strong><p class="maika-benefit-image-preview" style="background:#0c6fdb;padding:8px;min-height:40px">';
            if ($preview) { echo '<img src="' . esc_url($preview) . '" alt="" style="width:52px;height:52px;object-fit:contain">'; }
            echo '</p><input class="maika-benefit-image-id" type="hidden" name="' . esc_attr($this->get_field_name($image_key)) . '" value="' . absint($instance[$image_key]) . '">';
            echo '<p><button type="button" class="button" data-maika-benefit-upload>Chọn / thay icon</button> <button type="button" class="button-link" data-maika-benefit-remove>Xóa ảnh</button></p>';
            foreach (array('title_' => 'Tiêu đề', 'description_' => 'Mô tả') as $prefix => $label) {
                $key = $prefix . $i;
                echo '<p><label for="' . esc_attr($this->get_field_id($key)) . '">' . esc_html($label) . '</label>';
                if ($prefix === 'description_') {
                    echo '<textarea class="widefat" rows="2" id="' . esc_attr($this->get_field_id($key)) . '" name="' . esc_attr($this->get_field_name($key)) . '">' . esc_textarea($instance[$key]) . '</textarea>';
                } else {
                    echo '<input class="widefat" type="text" id="' . esc_attr($this->get_field_id($key)) . '" name="' . esc_attr($this->get_field_name($key)) . '" value="' . esc_attr($instance[$key]) . '">';
                }
                echo '</p>';
            }
            echo '</div>';
        }
    }
    public function update($new_instance, $old_instance) {
        $values = array();
        for ($i = 0; $i < 4; $i++) {
            $values['image_' . $i] = absint($new_instance['image_' . $i] ?? 0);
            $values['title_' . $i] = sanitize_text_field($new_instance['title_' . $i] ?? '');
            $values['description_' . $i] = sanitize_textarea_field($new_instance['description_' . $i] ?? '');
        }
        return $values;
    }
}
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, array('widgets.php', 'customize.php'), true)) { return; }
    wp_enqueue_media();
    $path = get_stylesheet_directory() . '/custom/product-benefits-admin.js';
    wp_enqueue_script('maika-product-benefits-admin', get_stylesheet_directory_uri() . '/custom/product-benefits-admin.js', array('jquery', 'media-views'), filemtime($path), true);
});
