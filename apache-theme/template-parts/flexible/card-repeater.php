<?php
/**
 * Flexible layout: Card Repeater.
 *
 * ACF layout key: card_repeater
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$cards_per_row = get_sub_field( 'cards_per_row' );
$cards_per_row = is_scalar( $cards_per_row ) ? (string) $cards_per_row : '3';
$cards_per_row = in_array( $cards_per_row, array( '2', '3', '4' ), true ) ? $cards_per_row : '3';

$cards = array();

if ( function_exists( 'have_rows' ) && have_rows( 'cards' ) ) {
	while ( have_rows( 'cards' ) ) {
		the_row();

		$selected_content = get_sub_field( 'selected_content' );
		$override_title   = get_sub_field( 'override_title' );
		$custom_image     = get_sub_field( 'custom_image' );
		$hide_thumbnail   = apache_2026_bool_from_mixed( get_sub_field( 'hide_thumbnail' ) );
		$hide_title       = apache_2026_bool_from_mixed( get_sub_field( 'hide_title' ) );
		$hide_excerpt     = apache_2026_bool_from_mixed( get_sub_field( 'hide_excerpt' ) );
		$hide_read_more   = apache_2026_bool_from_mixed( get_sub_field( 'hide_read_more' ) );
		$post_object      = null;

		if ( $selected_content instanceof WP_Post ) {
			$post_object = $selected_content;
		} elseif ( is_numeric( $selected_content ) ) {
			$post_object = get_post( (int) $selected_content );
		} elseif ( is_array( $selected_content ) && isset( $selected_content['ID'] ) ) {
			$post_object = get_post( (int) $selected_content['ID'] );
		}

		if ( ! $post_object instanceof WP_Post || 'publish' !== $post_object->post_status ) {
			continue;
		}

		if ( ! in_array( $post_object->post_type, array( 'post', 'page' ), true ) ) {
			continue;
		}

		$post_id = (int) $post_object->ID;
		$title   = is_string( $override_title ) && '' !== trim( $override_title )
			? trim( $override_title )
			: get_the_title( $post_id );
		$url     = get_permalink( $post_id );

		if ( ! is_string( $url ) || '' === $url ) {
			continue;
		}

		$excerpt = get_the_excerpt( $post_id );

		if ( ! is_string( $excerpt ) || '' === trim( $excerpt ) ) {
			$excerpt = wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) );
		}

		$image_id = 0;

		if ( is_array( $custom_image ) && isset( $custom_image['ID'] ) ) {
			$image_id = (int) $custom_image['ID'];
		} elseif ( is_numeric( $custom_image ) ) {
			$image_id = (int) $custom_image;
		}

		$cards[] = array(
			'post_id'         => $post_id,
			'title'           => is_string( $title ) && '' !== trim( $title ) ? trim( $title ) : __( 'Untitled', 'apache-2026' ),
			'url'             => $url,
			'excerpt'         => wp_trim_words( trim( (string) $excerpt ), 10, '…' ),
			'image_id'        => $image_id,
			'hide_thumbnail'  => $hide_thumbnail,
			'hide_title'      => $hide_title,
			'hide_excerpt'    => $hide_excerpt,
			'hide_read_more'  => $hide_read_more,
		);
	}
}

if ( empty( $cards ) ) {
	return;
}

$classes = array(
	'apache-flex',
	'apache-flex--card-repeater',
	'card-repeater',
	'card-repeater--cols-' . $cards_per_row,
);
?>

<section class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>">
	<div class="card-repeater__inner">
		<div class="card-repeater__grid">
			<?php foreach ( $cards as $card ) : ?>
				<?php $card_image_id = $card['image_id'] ? $card['image_id'] : get_post_thumbnail_id( $card['post_id'] ); ?>
				<div class="card-repeater__item">
					<article class="featured-post-slider__card<?php echo ( ! $card['hide_thumbnail'] && $card_image_id ) ? '' : ' featured-post-slider__card--no-image'; ?>">
						<?php if ( ! $card['hide_thumbnail'] && $card_image_id ) : ?>
							<a class="featured-post-slider__image-link" href="<?php echo esc_url( $card['url'] ); ?>" aria-hidden="true" tabindex="-1">
								<?php
								echo wp_get_attachment_image(
									$card_image_id,
									'medium_large',
									false,
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
							<?php if ( ! $card['hide_title'] ) : ?>
								<h3 class="featured-post-slider__post-title">
									<a href="<?php echo esc_url( $card['url'] ); ?>">
										<?php echo esc_html( $card['title'] ); ?>
									</a>
								</h3>
							<?php endif; ?>

							<?php if ( ! $card['hide_excerpt'] && '' !== $card['excerpt'] ) : ?>
								<p class="featured-post-slider__excerpt"><?php echo esc_html( $card['excerpt'] ); ?></p>
							<?php endif; ?>

							<?php if ( ! $card['hide_read_more'] ) : ?>
								<a class="btn btn-more" href="<?php echo esc_url( $card['url'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Learn more about %s', 'apache-2026' ), $card['title'] ) ); ?>">
									<?php esc_html_e( 'Learn More', 'apache-2026' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
