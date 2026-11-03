<?php
/**
 * Template Name: Giới thiệu Landing ACF
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about_css = get_stylesheet_directory() . '/custom/css/gioithieu.css';
wp_enqueue_style(
	'xenang-about-landing',
	get_stylesheet_directory_uri() . '/custom/css/gioithieu.css',
	array(),
	file_exists( $about_css ) ? filemtime( $about_css ) : null
);

$asset_url       = get_stylesheet_directory_uri() . '/images/about/';
$hero_image      = get_field( 'about_hero_image' );
$intro_title     = get_field( 'about_intro_title' );
$intro_content   = get_field( 'about_intro_content' );
$intro_image     = get_field( 'about_intro_image' );
$sections        = get_field( 'about_sections' );
$reasons_title   = get_field( 'about_reasons_title' );
$reasons         = get_field( 'about_reasons' );
$about_form_title= get_field( 'about_form_title' );
$about_form_image= get_field( 'about_form_image' );
$front_page_id   = (int) get_option( 'page_on_front' );
$form_shortcode  = $front_page_id ? get_field( 'home_form_shortcode', $front_page_id ) : '';

if ( ! $sections ) {
	$sections = array(
		array(
			'title'          => 'Tầm nhìn chiến lược',
			'content'        => '<p>Trở thành đơn vị cung cấp xe nâng và giải pháp kho vận uy tín hàng đầu, đồng hành cùng doanh nghiệp nâng cao hiệu suất và phát triển bền vững.</p><ul><li>Trở thành thương hiệu xe nâng đáng tin cậy</li><li>Dẫn đầu về hiệu quả và chất lượng dịch vụ</li><li>Phát triển bền vững cùng khách hàng</li></ul>',
			'image_position' => 'left',
		),
		array(
			'title'          => 'Sứ mệnh',
			'content'        => '<p>Mang đến những dòng xe nâng chính hãng, giải pháp vận hành an toàn và dịch vụ tận tâm, giúp khách hàng tối ưu chi phí trong sản xuất, kho vận và logistics.</p><ul><li>Cung cấp sản phẩm chính hãng, chất lượng cao</li><li>Giá cả cạnh tranh, minh bạch và tối ưu</li><li>Luôn lấy khách hàng làm trung tâm</li></ul>',
			'image_position' => 'right',
		),
		array(
			'title'          => 'Phương châm hoạt động',
			'content'        => '<p>Sự hài lòng của khách hàng là nền tảng cho mọi hoạt động. Chúng tôi không chỉ bán sản phẩm mà còn xây dựng mối quan hệ hợp tác lâu dài.</p><ul><li>Sản phẩm chất lượng, chính hãng</li><li>Chuyên nghiệp, tận tâm, minh bạch</li><li>Hỗ trợ tận tâm, nhanh chóng</li></ul>',
			'image_position' => 'left',
		),
	);
}

if ( ! $reasons ) {
	$reasons = array(
		array( 'icon' => 'medal', 'title' => 'Sản phẩm chính hãng 100%', 'description' => 'Nguồn gốc rõ ràng, xuất xứ minh bạch và đáng tin cậy.' ),
		array( 'icon' => 'tags', 'title' => 'Giá cạnh tranh, minh bạch', 'description' => 'Báo giá rõ ràng, không phát sinh và thông tin minh bạch.' ),
		array( 'icon' => 'user-gear', 'title' => 'Đội ngũ chuyên gia', 'description' => 'Tư vấn đúng nhu cầu, hỗ trợ kỹ thuật nhanh chóng.' ),
		array( 'icon' => 'handshake', 'title' => 'Hậu mãi và bảo hành lâu dài', 'description' => 'Đồng hành cùng khách hàng trong suốt quá trình vận hành.' ),
	);
}

$section_fallbacks = array( 'about-heavy-duty.webp', 'about-warehouse.webp', 'about-heavy-duty.webp' );
$hero_url          = ! empty( $hero_image['url'] ) ? $hero_image['url'] : $asset_url . 'about-hero.webp';
$intro_url         = ! empty( $intro_image['url'] ) ? $intro_image['url'] : $asset_url . 'about-warehouse.webp';
$form_image_url    = ! empty( $about_form_image['url'] ) ? $about_form_image['url'] : $asset_url . 'about-warehouse.webp';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'xa-about-landing' ); ?>>
<?php wp_body_open(); ?>

<main id="main-content" class="xa-about">
	<section class="xa-hero" aria-label="Giới thiệu công ty">
		<img src="<?php echo esc_url( $hero_url ); ?>" alt="Xe nâng hoạt động tại khu công nghiệp" fetchpriority="high">
	</section>

	<section class="xa-section xa-intro">
		<div class="xa-container xa-intro__grid">
			<div class="xa-copy">
				<span class="xa-eyebrow">Về chúng tôi</span>
				<h1><?php echo esc_html( $intro_title ?: 'Giới thiệu về chúng tôi' ); ?></h1>
				<div class="xa-richtext">
					<?php
					if ( $intro_content ) {
						echo wp_kses_post( $intro_content );
					} else {
						echo '<p><strong>Xe nâng Quang Ngọc</strong> chuyên cung cấp các dòng xe nâng và giải pháp kho vận phù hợp cho doanh nghiệp sản xuất, thương mại và logistics.</p><p>Với định hướng phát triển dựa trên chất lượng sản phẩm, dịch vụ minh bạch và sự tận tâm, chúng tôi luôn nỗ lực mang đến giải pháp an toàn, hiệu quả và bền vững.</p>';
					}
					?>
				</div>
			</div>
			<div class="xa-media xa-media--intro"><img src="<?php echo esc_url( $intro_url ); ?>" alt="Xe nâng trong kho hàng" loading="lazy"></div>
		</div>
	</section>

	<section class="xa-section xa-strategy">
		<div class="xa-container xa-strategy__list">
			<?php foreach ( $sections as $index => $section ) :
				$position  = ! empty( $section['image_position'] ) ? $section['image_position'] : ( $index % 2 ? 'right' : 'left' );
				$image      = ! empty( $section['image'] ) ? $section['image'] : array();
				$fallback   = $section_fallbacks[ $index % count( $section_fallbacks ) ];
				$section_url= ! empty( $image['url'] ) ? $image['url'] : $asset_url . $fallback;
				?>
				<article class="xa-feature xa-feature--image-<?php echo esc_attr( $position ); ?>">
					<div class="xa-media"><img src="<?php echo esc_url( $section_url ); ?>" alt="<?php echo esc_attr( $section['title'] ); ?>" loading="lazy"></div>
					<div class="xa-copy">
						<h2><?php echo esc_html( $section['title'] ); ?></h2>
						<div class="xa-richtext"><?php echo wp_kses_post( $section['content'] ); ?></div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="xa-section xa-reasons">
		<div class="xa-container">
			<div class="xa-heading"><span class="xa-eyebrow">Giá trị khác biệt</span><h2><?php echo esc_html( $reasons_title ?: 'Lý do lựa chọn chúng tôi' ); ?></h2></div>
			<div class="xa-reasons__grid">
				<?php foreach ( $reasons as $reason ) : ?>
					<article class="xa-reason">
						<i class="fa-solid fa-<?php echo esc_attr( $reason['icon'] ?: 'medal' ); ?>" aria-hidden="true"></i>
						<h3><?php echo esc_html( $reason['title'] ); ?></h3>
						<p><?php echo esc_html( $reason['description'] ); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php if ( $form_shortcode || shortcode_exists( 'fluentform' ) ) : ?>
		<section class="xa-section xa-form-wrap">
			<div class="xa-container xa-form-card">
				<div class="xa-form-card__image"><img src="<?php echo esc_url( $form_image_url ); ?>" alt="Tư vấn giải pháp xe nâng" loading="lazy"></div>
				<div class="xa-form-card__content">
					<span class="xa-form-card__quote" aria-hidden="true">”</span>
					<span class="xa-eyebrow">Nhận tư vấn</span>
					<h2><?php echo esc_html( $about_form_title ?: 'Liên hệ với chúng tôi' ); ?></h2>
					<div class="xa-shared-form"><?php echo do_shortcode( $form_shortcode ?: '[fluentform id="1"]' ); ?></div>
				</div>
			</div>
		</section>
	<?php endif; ?>
</main>

<?php wp_footer(); ?>
</body>
</html>
