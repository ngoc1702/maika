<?php
/**
 * Template Name: Trang tin tức
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'genesis_site_layout', 'xenang_news_layout' );
function xenang_news_layout() {
	return 'full-width-content';
}

remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
remove_action( 'genesis_after_header', 'genesis_do_breadcrumbs', 9 );
remove_action( 'genesis_loop', 'genesis_do_loop' );
remove_action( 'genesis_before_footer', 'caia_add_content_after_footer', 8 );

add_action( 'wp_enqueue_scripts', 'xenang_news_assets', 120 );
function xenang_news_assets() {
	$file = get_stylesheet_directory() . '/custom/css/tintuc.css';
	wp_enqueue_style(
		'xenang-news',
		get_stylesheet_directory_uri() . '/custom/css/tintuc.css',
		array(),
		file_exists( $file ) ? filemtime( $file ) : null
	);
}

add_action( 'genesis_loop', 'xenang_render_news_page' );
function xenang_render_news_page() {
	$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	$news  = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 8,
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
		)
	);

	$latest = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 8,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	?>
	<main class="xn-news" id="main-content">
		<div class="xn-news__layout">
			<section class="xn-news__main" aria-labelledby="news-page-title">
				<div class="xn-section-title">
					<h1 id="news-page-title"><?php echo esc_html( get_the_title() ); ?></h1>
				</div>

				<?php if ( $news->have_posts() ) : ?>
					<div class="xn-news-list">
						<?php while ( $news->have_posts() ) : $news->the_post(); ?>
							<article <?php post_class( 'xn-news-card' ); ?>>
								<a class="xn-news-card__thumb" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'thumbnail' ); ?>
									<?php else : ?>
										<span class="xn-image-placeholder"><i class="fa-regular fa-image" aria-hidden="true"></i></span>
									<?php endif; ?>
								</a>
								<div class="xn-news-card__content">
									<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></time>
									<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30, '…' ) ); ?></p>
								</div>
							</article>
						<?php endwhile; ?>
					</div>

					<?php if ( $news->max_num_pages > 1 ) : ?>
						<nav class="xn-pagination" aria-label="Phân trang tin tức">
							<?php
							echo wp_kses_post(
								paginate_links(
									array(
										'total'     => $news->max_num_pages,
										'current'   => $paged,
										'mid_size'  => 2,
										'end_size'  => 1,
										'prev_text' => '<span aria-hidden="true">←</span><span class="screen-reader-text">Trang trước</span>',
										'next_text' => '<span class="screen-reader-text">Trang sau</span><span aria-hidden="true">→</span>',
										'type'      => 'list',
									)
								)
							);
							?>
						</nav>
					<?php endif; ?>
				<?php else : ?>
					<div class="xn-news__empty">
						<i class="fa-regular fa-newspaper" aria-hidden="true"></i>
						<p>Chưa có bài viết nào được xuất bản.</p>
					</div>
				<?php endif; ?>
			</section>

			<aside class="xn-news__sidebar" aria-labelledby="latest-news-title">
				<div class="xn-section-title xn-section-title--small">
					<h2 id="latest-news-title">Bài viết mới nhất</h2>
				</div>
				<?php if ( $latest->have_posts() ) : ?>
					<div class="xn-latest-list">
						<?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
							<article class="xn-latest-card">
								<a class="xn-latest-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'thumbnail' ); ?>
									<?php else : ?>
										<span class="xn-image-placeholder"><i class="fa-regular fa-image" aria-hidden="true"></i></span>
									<?php endif; ?>
								</a>
								<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							</article>
						<?php endwhile; ?>
					</div>
				<?php else : ?>
					<p class="xn-news__sidebar-empty">Bài viết mới sẽ hiển thị tại đây.</p>
				<?php endif; ?>
			</aside>
		</div>
	</main>
	<?php
	wp_reset_postdata();
}

genesis();
