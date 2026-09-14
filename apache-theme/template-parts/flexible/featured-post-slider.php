<?php
/**
 * Flexible layout: Featured Post Slider.
 *
 * ACF layout key: featured_post_slider
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$margin_top = apache_2026_sanitize_css_px_value( get_sub_field( 'margin_top' ), 0, 600 );
$title      = get_sub_field( 'module_title' );
$view_all   = get_sub_field( 'view_all_link' );
$show_all_featured = apache_2026_bool_from_mixed( get_sub_field( 'show_all_featured_posts' ) );
$posts      = get_sub_field( 'featured_posts' );

$title = is_string( $title ) && '' !== trim( $title ) ? trim( $title ) : __( 'Featured Stories', 'apache-2026' );

$view_all_url    = '';
$view_all_label  = __( 'View All', 'apache-2026' );
$view_all_target = '_self';

if ( is_array( $view_all ) ) {
	$view_all_url    = isset( $view_all['url'] ) && is_string( $view_all['url'] ) ? trim( $view_all['url'] ) : '';
	$view_all_label  = isset( $view_all['title'] ) && is_string( $view_all['title'] ) && '' !== trim( $view_all['title'] ) ? trim( $view_all['title'] ) : $view_all_label;
	$view_all_target = isset( $view_all['target'] ) && is_string( $view_all['target'] ) && '' !== trim( $view_all['target'] ) ? trim( $view_all['target'] ) : '_self';
} elseif ( is_string( $view_all ) && '' !== trim( $view_all ) ) {
	$view_all_url = trim( $view_all );
}

if ( ! in_array( $view_all_target, array( '_self', '_blank' ), true ) ) {
	$view_all_target = '_self';
}

$normalized_posts = array();
$post_items       = array();

if ( $show_all_featured ) {
	$featured_query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => array(
				array(
					'key'   => 'apache_2026_featured_post',
					'value' => '1',
				),
			),
		)
	);

	$post_items = $featured_query->posts;
} else {
	$post_items = is_array( $posts ) ? $posts : array();
}

foreach ( $post_items as $post_item ) {
	$post_object = null;

	if ( $post_item instanceof WP_Post ) {
		$post_object = $post_item;
	} elseif ( is_numeric( $post_item ) ) {
		$post_object = get_post( (int) $post_item );
	} elseif ( is_array( $post_item ) && isset( $post_item['ID'] ) ) {
		$post_object = get_post( (int) $post_item['ID'] );
	}

	if ( ! $post_object instanceof WP_Post ) {
		continue;
	}

	if ( 'post' !== $post_object->post_type || 'publish' !== $post_object->post_status ) {
		continue;
	}

	$normalized_posts[] = $post_object;
}

if ( empty( $normalized_posts ) ) {
	return;
}

$classes = array(
	'apache-flex',
	'apache-flex--featured-post-slider',
	'featured-post-slider',
);

if ( null !== $margin_top ) {
	$classes[] = 'apache-flex--has-custom-margin';
}

$styles = array();

if ( null !== $margin_top ) {
	$styles[] = '--apache-flex-margin-top: ' . $margin_top . 'px';
}

$slider_id = wp_unique_id( 'featured-post-slider-' );
$post_count = count( $normalized_posts );
$is_grid_mode = $show_all_featured;
?>

<section
	class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
	<?php if ( $styles ) : ?>
		style="<?php echo esc_attr( implode( '; ', $styles ) ); ?>"
	<?php endif; ?>
>
	<div class="featured-post-slider__inner">
		<div class="featured-post-slider__header">
			<?php if ( $title ) : ?>
				<h2 class="featured-post-slider__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $view_all_url ) : ?>
			<a
				class="featured-post-slider__view-all viewAllBtn"
				href="<?php echo esc_url( $view_all_url ); ?>"
				target="<?php echo esc_attr( $view_all_target ); ?>"
				<?php if ( '_blank' === $view_all_target ) : ?>
					rel="noopener noreferrer"
				<?php endif; ?>
			>
				<?php echo esc_html( $view_all_label ); ?>
			</a>
			<?php endif; ?>
		</div>

		<div class="featured-post-slider__slider-shell">
			<div
				<?php if ( ! $is_grid_mode ) : ?>
					id="<?php echo esc_attr( $slider_id ); ?>"
				<?php endif; ?>
				class="featured-post-slider__slider<?php echo $is_grid_mode ? ' featured-post-slider__slider--grid' : ' apache-swiper swiper'; ?>"
				<?php if ( ! $is_grid_mode ) : ?>
					data-apache-swiper="featured-posts"
					data-swiper-loop="true"
					data-swiper-autoplay="false"
					data-swiper-effect="slide"
				<?php endif; ?>
				aria-label="<?php echo esc_attr( $title ); ?>"
				aria-roledescription="<?php echo esc_attr( $is_grid_mode ? __( 'list', 'apache-2026' ) : __( 'carousel', 'apache-2026' ) ); ?>"
			>
				<div class="featured-post-slider__wrapper<?php echo $is_grid_mode ? ' featured-post-slider__wrapper--grid' : ' swiper-wrapper'; ?>">
					<?php foreach ( $normalized_posts as $index => $post_object ) : ?>
						<?php
						$post_id    = (int) $post_object->ID;
						$post_title = get_the_title( $post_id );
						$post_title = is_string( $post_title ) && '' !== trim( $post_title ) ? trim( $post_title ) : __( 'Untitled', 'apache-2026' );
						$post_url   = get_permalink( $post_id );

						if ( ! is_string( $post_url ) || '' === $post_url ) {
							continue;
						}

						$excerpt = get_the_excerpt( $post_id );

						if ( ! is_string( $excerpt ) || '' === trim( $excerpt ) ) {
							$excerpt = wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) );
						}

						$excerpt = wp_trim_words( trim( (string) $excerpt ), 10, '…' );
						?>
						<div class="featured-post-slider__slide<?php echo $is_grid_mode ? ' featured-post-slider__slide--grid' : ' swiper-slide'; ?>"<?php if ( ! $is_grid_mode ) : ?> role="group" aria-label="<?php echo esc_attr( sprintf( __( 'Slide %1$d of %2$d', 'apache-2026' ), $index + 1, $post_count ) ); ?>"<?php endif; ?>>
							<article class="featured-post-slider__card<?php echo has_post_thumbnail( $post_id ) ? '' : ' featured-post-slider__card--no-image'; ?>">
								<?php if ( has_post_thumbnail( $post_id ) ) : ?>
									<a class="featured-post-slider__image-link" href="<?php echo esc_url( $post_url ); ?>" aria-hidden="true" tabindex="-1">
										<?php
										echo get_the_post_thumbnail(
											$post_id,
											'medium_large',
											array(
												'class'    => 'featured-post-slider__image',
												'loading'  => 'lazy',
												'decoding' => 'async',
											)
										);
										?>
									</a>
								<?php endif; ?>

								<div class="featured-post-slider__content">
									<h3 class="featured-post-slider__post-title">
										<a href="<?php echo esc_url( $post_url ); ?>">
											<?php echo esc_html( $post_title ); ?>
										</a>
									</h3>

									<?php if ( '' !== $excerpt ) : ?>
										<p class="featured-post-slider__excerpt"><?php echo esc_html( $excerpt ); ?></p>
									<?php endif; ?>

									<a class="btn btn-more" href="<?php echo esc_url( $post_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Learn more about %s', 'apache-2026' ), $post_title ) ); ?>">
										<?php esc_html_e( 'Learn More', 'apache-2026' ); ?>
									</a>
								</div>
							</article>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( ! $is_grid_mode && 1 < $post_count ) : ?>
				<?php
				get_template_part(
					'template-parts/global/swiper-controls',
					null,
					array(
						'wrapper_class' => 'featured-post-slider__controls',
						'button_class'  => 'featured-post-slider__button',
						'slider_id'     => $slider_id,
						'prev_label'    => __( 'Previous featured story', 'apache-2026' ),
						'next_label'    => __( 'Next featured story', 'apache-2026' ),
					)
				);
				?>
			<?php endif; ?>
		</div>
	</div>
</section>
