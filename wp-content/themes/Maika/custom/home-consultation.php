<?php
/** Consultation block, before homepage partner reviews. */
add_filter('acf/load_field/key=field_maika_consultation_form', function ($field) {
    $field['choices'] = array();
    if (class_exists('FluentForm\App\Models\Form')) {
        $forms = \FluentForm\App\Models\Form::where('status', 'published')->orderBy('id', 'desc')->get();
        foreach ($forms as $form) {
            $field['choices'][$form->id] = $form->title . ' (#' . $form->id . ')';
        }
    }
    return $field;
});
add_action('genesis_after_header', 'maika_home_consultation', 18);
function maika_home_consultation() {
    if (!is_front_page() || !function_exists('get_field')) {
        return;
    }
    maika_render_consultation();
}
function maika_render_consultation() {
    if (!function_exists('get_field')) {
        return;
    }
    $front = (int) get_option('page_on_front');
    $form = (int) get_field('maika_consultation_form', $front);
    if (!$form || !shortcode_exists('fluentform')) {
        return;
    }
    $form_heading = get_field('maika_consultation_form_heading', $front) ?: 'Giải pháp phù hợp cho bạn';
    $description = get_field('maika_consultation_description', $front);
    $image = (int) get_field('maika_consultation_image', $front);
    $body_bg = wp_get_attachment_image_url((int) get_field('maika_consultation_bg', $front), 'full') ?: get_stylesheet_directory_uri() . '/images/nen-yte.png';
    echo '<section class="maika-consultation" aria-labelledby="maika-consultation-heading" style="--maika-consultation-background:url(' . esc_url($body_bg) . ')">';
    echo '<div class="maika-consultation-body"><div class="wrap"><div class="maika-consultation-card">';
    $is_banner = $image && strpos((string) get_attached_file($image), 'maika-slide-') !== false;
    echo '<div class="maika-consultation-visual' . ($is_banner ? ' is-banner-crop' : '') . '">';
    if ($image) {
        $image_url = wp_get_attachment_image_url($image, 'full');
        $image_meta = wp_get_attachment_metadata($image);
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" width="' . esc_attr((int) ($image_meta['width'] ?? 0)) . '" height="' . esc_attr((int) ($image_meta['height'] ?? 0)) . '" alt="" loading="lazy" decoding="async">';
        }
    }
    echo '</div><div class="maika-consultation-panel"><h2 id="maika-consultation-heading">' . esc_html($form_heading) . '</h2>';
    if ($description) {
        echo '<p class="maika-consultation-description">' . nl2br(esc_html($description)) . '</p>';
    }
    $form_html = do_shortcode('[fluentform id="' . $form . '"]');
    $form_html = preg_replace_callback('/(<button\b[^>]*\bclass="[^"]*\bff-btn-submit\b[^"]*"[^>]*>)(.*?)(<\/button>)/s', function ($match) {
        $label = trim(html_entity_decode(wp_strip_all_tags($match[2]), ENT_QUOTES, 'UTF-8'));
        if ($label && mb_strtoupper($label, 'UTF-8') === $label) {
            $label = mb_strtoupper(mb_substr($label, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($label, 1, null, 'UTF-8'), 'UTF-8');
        }
        $opening = preg_replace('/\baria-label="[^"]*"/', 'aria-label="' . esc_attr($label) . '"', $match[1]);
        $arrow = '<svg class="maika-submit-arrow" aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>';
        return $opening . '<span>' . esc_html($label) . '</span>' . $arrow . $match[3];
    }, $form_html);
    echo $form_html;
    echo '</div></div></div></div></section>';
}
// Phone validation is scoped to the consultation form created for Maika.
add_filter('fluentform/validation_errors', function ($errors, $data, $form) {
    if ((int) $form->id !== (int) get_option('maika_consultation_form_id')) {
        return $errors;
    }
    $phone = isset($data['phone']) ? trim((string) $data['phone']) : '';
    if ($phone && (!preg_match('/^[+0-9().\s-]+$/', $phone) || strlen(preg_replace('/\D/', '', $phone)) < 8 || strlen(preg_replace('/\D/', '', $phone)) > 15)) {
        $errors['phone'] = array('Vui lòng nhập số điện thoại hợp lệ.');
    }
    return $errors;
}, 10, 3);