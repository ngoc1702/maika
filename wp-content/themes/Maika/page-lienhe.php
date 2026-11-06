<?php
/**
 * Template Name: Trang liên hệ ACF
 * Template Post Type: page
 */
if (!defined('ABSPATH')) {
    exit;
}
add_filter('genesis_site_layout', function () { return 'full-width-content'; });
remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', function () {
    $page_id = get_queried_object_id();
    $field = function ($name) use ($page_id) {
        return function_exists('get_field') ? get_field($name, $page_id) : '';
    };
    $heading = $field('contact_heading') ?: get_the_title($page_id);
    $company = $field('contact_company');
    $contact_html = '';
    $address = '';
    // Reuse the saved footer contact widget so edits there stay in sync.
    $sidebars = wp_get_sidebars_widgets();
    $text_widgets = get_option('widget_text', array());
    foreach ($sidebars['content-bfooter'] ?? array() as $widget_id) {
        if (!preg_match('/^text-(\d+)$/', $widget_id, $matches)) {
            continue;
        }
        $widget = $text_widgets[(int) $matches[1]] ?? array();
        if (strpos($widget['text'] ?? '', 'maika-footer-contact') !== false) {
            $company = $company ?: ($widget['title'] ?? '');
            $contact_html = $widget['text'];
            if (preg_match('/<li\b[^>]*>(.*?)<\/li>/s', $contact_html, $first_row)) {
                $address = trim(wp_strip_all_tags($first_row[1]));
            }
            break;
        }
    }
    $map = $field('contact_map_embed');
    $items = $field('contact_items');
    $background = wp_get_attachment_image_url((int) $field('contact_top_bg'), 'full') ?: get_stylesheet_directory_uri() . '/images/hero-pattern-small.png';
    foreach (is_array($items) ? $items : array() as $item) {
        if (($item['icon'] ?? '') === 'location-dot') { $address = $item['content']; break; }
    }
    ?>
    <div class="maika-contact-page">
        <section class="maika-contact-top" aria-labelledby="maika-contact-heading" style="--maika-contact-background:url(<?php echo esc_url($background); ?>)">
            <div class="wrap maika-contact-grid">
                <div class="maika-contact-info">
                    <h1 id="maika-contact-heading"><?php echo esc_html($heading ?: 'Liên hệ'); ?></h1>
                    <?php if ($company) : ?><h2><?php echo esc_html($company); ?></h2><?php endif; ?>
                    <?php if ($description = $field('contact_description')) : ?>
                        <div class="maika-contact-description"><?php echo wp_kses_post(wpautop($description)); ?></div>
                    <?php endif; ?>
                    <?php if ($items) : ?>
                        <ul class="maika-footer-contact">
                            <?php foreach ($items as $item) : ?>
                                <li><span class="maika-footer-contact-icon"><i class="fa-solid fa-<?php echo esc_attr($item['icon'] ?: 'circle-info'); ?>" aria-hidden="true"></i></span><span>
                                <?php if (!empty($item['link'])) : ?><a href="<?php echo esc_url($item['link'], array('http', 'https', 'tel', 'mailto')); ?>"><?php endif; ?>
                                <?php echo esc_html($item['content']); ?>
                                <?php if (!empty($item['link'])) : ?></a><?php endif; ?>
                                </span></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php elseif ($contact_html) : ?>
                        <?php echo wp_kses_post($contact_html); ?>
                    <?php endif; ?>
                    <?php $socials = $field('contact_socials'); ?>
                    <?php if (is_array($socials) && $socials) : ?>
                        <div class="maika-contact-socials" aria-label="Mạng xã hội">
                            <?php
                            $social_assets = array('facebook-f' => 'images.png', 'tiktok' => 'tiktok-icon-free-png.webp', 'zalo' => 'i2.png', 'youtube' => 'footer-youtube.svg');
                            $social_labels = array('facebook-f' => 'Facebook', 'tiktok' => 'TikTok', 'zalo' => 'Zalo', 'youtube' => 'YouTube', 'instagram' => 'Instagram');
                            foreach ($socials as $social) :
                                $type = $social['type'] ?? '';
                                if (empty($social['url'])) { continue; }
                                $label = $social_labels[$type] ?? $type;
                            ?>
                                <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($label); ?>">
                                    <?php if (!empty($social['image'])) : ?>
                                        <?php echo wp_get_attachment_image((int) $social['image'], 'thumbnail', false, array('alt' => $label, 'loading' => 'lazy')); ?>
                                    <?php elseif (isset($social_assets[$type])) : ?>
                                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/' . $social_assets[$type]); ?>" alt="<?php echo esc_attr($label); ?>" width="40" height="40" loading="lazy">
                                    <?php else : ?>
                                        <i class="fa-brands fa-<?php echo esc_attr($type); ?>" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="maika-contact-map" aria-label="Bản đồ vị trí công ty">
                    <?php if ($map) : ?>
                        <?php echo wp_kses($map, array('iframe' => array('src' => true, 'width' => true, 'height' => true, 'style' => true, 'allowfullscreen' => true, 'loading' => true, 'referrerpolicy' => true, 'title' => true))); ?>
                    <?php elseif ($address) : ?>
                        <iframe title="Bản đồ vị trí công ty" src="<?php echo esc_url('https://maps.google.com/maps?q=' . rawurlencode($address) . '&output=embed'); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    <?php else : ?>
                        <p>Vui lòng thêm mã nhúng bản đồ trong phần quản lý trang liên hệ.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php maika_render_consultation(); ?>
    </div>
    <?php
});
genesis();
