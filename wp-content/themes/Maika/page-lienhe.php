<?php
/**
 * Template Name: Trang liên hệ ACF
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'genesis_site_layout', 'xenang_contact_layout' );
function xenang_contact_layout() {
	return 'full-width-content';
}

remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_after_header', 'genesis_do_breadcrumbs', 9 );
remove_action( 'genesis_loop', 'genesis_do_loop' );

add_action( 'wp_enqueue_scripts', 'xenang_contact_assets', 120 );
function xenang_contact_assets() {
	$file = get_stylesheet_directory() . '/custom/css/lienhe.css';
	wp_enqueue_style(
		'xenang-contact',
		get_stylesheet_directory_uri() . '/custom/css/lienhe.css',
		array(),
		file_exists( $file ) ? filemtime( $file ) : null
	);
}

add_action( 'genesis_loop', 'xenang_render_contact_page' );
function xenang_render_contact_page() {
	$heading     = get_field( 'contact_heading' );
	$company     = get_field( 'contact_company' );
	$description = get_field( 'contact_description' );
	$items       = get_field( 'contact_items' );
	$socials     = get_field( 'contact_socials' );
	$shortcode   = get_field( 'contact_form_shortcode' );
	$map         = get_field( 'contact_map_embed' );
	?>
	<main class="xc-contact" id="main-content">
		<div class="xc-contact__top">
			<section class="xc-contact__info" aria-labelledby="contact-heading">
				<h1 id="contact-heading"><?php echo esc_html( $heading ?: get_the_title() ); ?></h1>
				<?php if ( $company ) : ?><h2><?php echo esc_html( $company ); ?></h2><?php endif; ?>
				<?php if ( $description ) : ?><div class="xc-contact__description"><?php echo wp_kses_post( $description ); ?></div><?php endif; ?>

				<?php if ( $items ) : ?>
					<ul class="xc-contact__list">
						<?php foreach ( $items as $item ) :
							$icon = ! empty( $item['icon'] ) ? $item['icon'] : 'circle-info';
							$link = ! empty( $item['link'] ) ? $item['link'] : '';
							?>
							<li>
								<span class="xc-contact__icon"><i class="fa-solid fa-<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i></span>
								<?php if ( $link ) : ?><a href="<?php echo esc_url( $link, array( 'http', 'https', 'tel', 'mailto' ) ); ?>"><?php endif; ?>
								<?php echo esc_html( $item['content'] ); ?>
								<?php if ( $link ) : ?></a><?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $socials ) : ?>
					<div class="xc-contact__socials" aria-label="Mạng xã hội">
						<?php foreach ( $socials as $social ) : ?>
							<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $social['type'] ) ); ?>"><i class="fa-brands fa-<?php echo esc_attr( $social['type'] ); ?>" aria-hidden="true"></i></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</section>

			<?php if ( $shortcode ) : ?>
				<section class="xc-contact__form" aria-label="Gửi yêu cầu tư vấn">
					<?php echo do_shortcode( $shortcode ); ?>
				</section>
			<?php endif; ?>
		</div>

		<?php if ( $map ) : ?>
			<section class="xc-contact__map" aria-label="Bản đồ">
				<?php
				echo wp_kses(
					$map,
					array(
						'iframe' => array(
							'src' => true, 'width' => true, 'height' => true, 'style' => true,
							'allowfullscreen' => true, 'loading' => true, 'referrerpolicy' => true,
							'title' => true,
						),
					)
				);
				?>
			</section>
		<?php endif; ?>
	</main>
	<?php
}

genesis();
