<?php

add_filter('rwmb_meta_boxes', 'prefix_register_taxonomy_meta_boxes');
function prefix_register_taxonomy_meta_boxes($meta_boxes)
{
    $prefix = '';
    // Sản phẩm
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'oto'),
        'post_types' => array('product'),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'fields'     => array(

            // ẢNH SẢN PHẨM
             array(
                 'name' => 'Ảnh sản phẩm',
                 'id'   => 'anhsp',
                 'type' => 'image_advanced',
                 'max_file_uploads' => 8,
             ),

            // // ===== TRẢ GÓP =====
            // array(
            //     'type' => 'heading',
            //     'name' => 'THÔNG TIN TRẢ GÓP',
            // ),

            // array(
            //     'name' => 'Cho phép trả góp',
            //     'id'   => 'enable_installment',
            //     'type' => 'checkbox',
            // ),

            // // ===== GIỚI THIỆU =====
            // array(
            //     'type' => 'heading',
            //     'name' => 'Giới thiệu chung',
            // ),
            // array(
            //     'name' => 'Giới thiệu hình thức trả góp',
            //     'id'   => 'tragop_intro',
            //     'type' => 'wysiwyg',
            // ),

            // // ===== ƯU ĐIỂM =====
            // array(
            //     'type' => 'heading',
            //     'name' => 'Ưu điểm',
            // ),
            // array(
            //     'name' => 'Ưu điểm khi mua trả góp',
            //     'id'   => 'tragop_uudiem',
            //     'type' => 'wysiwyg',
            // ),

            // // ===== NHƯỢC ĐIỂM =====
            // array(
            //     'type' => 'heading',
            //     'name' => 'Nhược điểm',
            // ),
            // array(
            //     'name' => 'Nhược điểm khi mua trả góp',
            //     'id'   => 'tragop_nhuocdiem',
            //     'type' => 'wysiwyg',
            // ),

            // // ===== QUY TRÌNH =====
            // array(
            //     'type' => 'heading',
            //     'name' => 'Quy trình',
            // ),
            // array(
            //     'name' => 'Quy trình mua trả góp',
            //     'id'   => 'tragop_quytrinh',
            //     'type' => 'wysiwyg',
            // ),

            // // ===== GIẤY TỜ =====
            // array(
            //     'type' => 'heading',
            //     'name' => 'Giấy tờ cần chuẩn bị',
            // ),
            // array(
            //     'name' => 'Danh sách giấy tờ',
            //     'id'   => 'tragop_giayto',
            //     'type' => 'wysiwyg',
            // ),

            // // ===== NGÂN HÀNG =====
            // array(
            //     'type' => 'heading',
            //     'name' => 'Ngân hàng / Đơn vị hỗ trợ',
            // ),
            // array(
            //             'name' => 'Logo ngân hàng',
            //             'id'   => 'logo',
            //             'type' => 'image_advanced',
            //             'max_file_uploads' => 8,
                    
            //     ),
            


            // ===== CALCULATOR =====
            // array(
            //     'type' => 'heading',
            //     'name' => 'Form tính trả góp',
            // ),
            // array(
            //     'name' => 'Shortcode Calculator Form',
            //     'id'   => 'tragop_calculator_shortcode',
            //     'type' => 'text',
            //     'desc' => 'VD: [calculated_fields_form id="123"]',
            // ),
        ),
    );



    //trả góp
    $meta_boxes[] = [
        'title'      => 'Nội dung Trang Trả góp',
        'id'         => 'tragop_page_meta',
        'post_types' => ['page'],
        'include'    => [
            'template' => ['page-tragop.php'],
        ],
        'fields'     => [],
    ];




    // liên hệ
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'oto'),
        'post_types' => array('page',),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'include'   => array(
            'template'  => 'page-lienhe.php',
        ),
        'fields'     => array(
            array(
                'id' => $prefix . 'nd',
                'name' => esc_html__('Nội dung liên hệ', 'oto'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

            array(
                'name' => 'Form',
                'id' => 'form',
                'type' => 'text',
                'size' => 70,
            ),


            array(
                'id' => 'map',
                'name' => esc_html__('Map', 'oto'),
                'type'  => 'textarea',
                'sanitize_callback' => 'none',
            ),
        ),
    );

    //BLOG
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'oto'),
        'post_types' => array('page'),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'include'    => array(
            'template' => 'page-blog.php',
        ),
        'fields'     => array(
            array(
                'id' => $prefix . 'tieude',
                'name' => esc_html__('Tiêu đề hỗ trợ', 'oto'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

            array(
                'id'     => $prefix . 'khung_hotro',
                'type'   => 'group',
                'clone'  => true,
                'sort_clone' => true,
                'name'   => 'Nội dung hỗ trợ',
                'fields' => array(
                    array(
                        'id'   => $prefix . 'nd',
                        'name' => 'Nội dung',
                        'type' => 'wysiwyg',
                        'options' => array(
                            'textarea_rows' => 2,
                        ),
                    ),
                ),
            ),

        ),
    );




      //Giới thiệu
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'oto'),
        'post_types' => array('page'),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'include'    => array(
            'template' => 'page-gioithieu.php',
        ),
        'fields'     => array(

              //Tầm nhìn sứ mệnh
         
            array(
                'name' => 'Ảnh Content',
                'id'   => 'anh_content',
                'type' => 'image_advanced',
                'max_file_uploads' => 1,
            ),

                 array(
                'id' => $prefix . 'tieude_tamnhin',
                'name' => esc_html__('Tiêu đề tầm nhìn', 'oto'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

            array(
                'id'          => $prefix . 'cards_tamnhin',
                'name'        => esc_html__('Tầm nhìn và sứ mệnh', 'oto'),
                'type'        => 'group',
                'clone'       => true,
                'sort_clone'  => true,
                'collapsible' => true,
                'group_title' => array('field' => 'card_title'),
                'fields'      => array(

                    array(
                        'id'   => 'card_title',
                        'name' => esc_html__('Tiêu đề', 'oto'),
                        'type' => 'text',
                    ),
                 array(
                        'id'   => 'card_desc',
                        'name' => esc_html__('Mô tả', 'oto'),
                        'type' => 'textarea',
                        'rows' => 3,
                    ),
                ),
            ),

            //Giá trị cốt lỗi
            array(
                'id' => $prefix . 'tieude_giatri',
                'name' => esc_html__('Tiêu đề giá trị', 'oto'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),
            array(
                'id'          => $prefix . 'cards_giatri',
                'name'        => esc_html__('Danh sách Card giá trị', 'oto'),
                'type'        => 'group',
                'clone'       => true,
                'sort_clone'  => true,
                'collapsible' => true,
                'group_title' => array('field' => 'card_title'),
                'fields'      => array(
                    array(
                        'id'   => 'card_image',
                        'name' => esc_html__('Icon card', 'oto'),
                        'type' => 'image_advanced',
                    ),
                    array(
                        'id'   => 'card_title',
                        'name' => esc_html__('Tiêu đề', 'oto'),
                        'type' => 'text',
                    ),
                
                ),
            ),


                             array(
                'id' => $prefix . 'cauchuyen',
                'name' => esc_html__('Câu chuyện thương hiệu', 'oto'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

        ),

    );




    return $meta_boxes;
}
