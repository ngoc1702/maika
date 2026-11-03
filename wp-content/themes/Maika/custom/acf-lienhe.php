<?php
/**
 * ACF fields for the contact page template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', 'xenang_register_contact_fields' );

function xenang_register_contact_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_xenang_contact',
			'title'                 => 'Nội dung trang liên hệ',
			'fields'                => array(
				array(
					'key'   => 'field_contact_tab_info',
					'label' => 'Thông tin liên hệ',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_contact_heading',
					'label'         => 'Tiêu đề chính',
					'name'          => 'contact_heading',
					'type'          => 'text',
					'default_value' => 'Thông tin liên hệ',
				),
				array(
					'key'   => 'field_contact_company',
					'label' => 'Tên công ty',
					'name'  => 'contact_company',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_contact_description',
					'label'        => 'Đoạn giới thiệu',
					'name'         => 'contact_description',
					'type'         => 'textarea',
					'rows'         => 4,
					'new_lines'    => 'wpautop',
				),
				array(
					'key'          => 'field_contact_items',
					'label'        => 'Các dòng thông tin',
					'name'         => 'contact_items',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Thêm thông tin',
					'sub_fields'   => array(
						array(
							'key'     => 'field_contact_item_icon',
							'label'   => 'Biểu tượng',
							'name'    => 'icon',
							'type'    => 'select',
							'choices' => array(
								'location-dot' => 'Địa chỉ',
								'phone'        => 'Điện thoại',
								'headset'      => 'Hotline',
								'envelope'     => 'Email',
								'clock'        => 'Giờ làm việc',
							),
						),
						array(
							'key'      => 'field_contact_item_content',
							'label'    => 'Nội dung',
							'name'     => 'content',
							'type'     => 'text',
							'required' => 1,
						),
						array(
							'key'           => 'field_contact_item_link',
							'label'         => 'Liên kết',
							'name'          => 'link',
							'type'          => 'url',
							'instructions'  => 'Có thể nhập tel: hoặc mailto:',
						),
					),
				),
				array(
					'key'          => 'field_contact_socials',
					'label'        => 'Mạng xã hội',
					'name'         => 'contact_socials',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Thêm mạng xã hội',
					'sub_fields'   => array(
						array(
							'key'     => 'field_contact_social_type',
							'label'   => 'Mạng xã hội',
							'name'    => 'type',
							'type'    => 'select',
							'choices' => array(
								'facebook-f' => 'Facebook',
								'tiktok'     => 'TikTok',
								'youtube'    => 'YouTube',
								'instagram'  => 'Instagram',
							),
						),
						array(
							'key'      => 'field_contact_social_url',
							'label'    => 'Đường dẫn',
							'name'     => 'url',
							'type'     => 'url',
							'required' => 1,
						),
					),
				),
				array(
					'key'   => 'field_contact_tab_form',
					'label' => 'Form liên hệ',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_contact_form_shortcode',
					'label'         => 'Shortcode form',
					'name'          => 'contact_form_shortcode',
					'type'          => 'text',
					'instructions'  => 'Ví dụ: [fluentform id="1"]',
				),
				array(
					'key'   => 'field_contact_tab_map',
					'label' => 'Bản đồ',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_contact_map_embed',
					'label'        => 'Mã nhúng Google Maps',
					'name'         => 'contact_map_embed',
					'type'         => 'textarea',
					'rows'         => 6,
					'new_lines'    => '',
					'instructions' => 'Google Maps → Chia sẻ → Nhúng bản đồ → Sao chép HTML.',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-lienhe.php',
					),
				),
			),
			'position'              => 'acf_after_title',
			'show_in_rest'          => 0,
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
