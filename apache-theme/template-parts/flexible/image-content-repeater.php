<?php
/**
 * Flexible layout: Image & Content Repeater.
 *
 * ACF layout key: image_content_repeater
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$items = array();

if ( function_exists( 'have_rows' ) && have_rows( 'items' ) ) {
	while ( have_rows( 'items' ) ) {
		the_row();

		$align_image = get_sub_field( 'align_image' );
		$image       = get_sub_field( 'image' );
		$title       = get_sub_field( 'title' );
		$content     = get_sub_field( 'content' );
		$cta_options = get_sub_field( 'cta_options' );
		$links       = array();

		$align_image = is_scalar( $align_image ) ? sanitize_key( (string) $align_image ) : 'left';
		$align_image = in_array( $align_image, array( 'left', 'right' ), true ) ? $align_image : 'left';
		$title       = is_string( $title ) ? trim( $title ) : '';
		$content     = is_string( $content ) ? trim( $content ) : '';
		$cta_options = is_scalar( $cta_options ) ? sanitize_key( (string) $cta_options ) : 'none';
		$cta_options = in_array( $cta_options, array( 'none', 'links', 'buttons' ), true ) ? $cta_options : 'none';

		$image_id = 0;
		$image_alt = '';

		if ( is_array( $image ) ) {
			$image_id  = isset( $image['ID'] ) ? (int) $image['ID'] : 0;
			$image_alt = isset( $image['alt'] ) && is_string( $image['alt'] ) ? trim( $image['alt'] ) : '';
		} elseif ( is_numeric( $image ) ) {
			$image_id = (int) $image;
		}

		if ( '' !== $image_alt ) {
			$image_alt = trim( $image_alt );
		} elseif ( $image_id ) {
			$image_alt = trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
		}

		if ( function_exists( 'have_rows' ) && have_rows( 'links' ) ) {
			while ( have_rows( 'links' ) ) {
				the_row();

				$link = get_sub_field( 'link' );

				if ( ! is_array( $link ) ) {
					continue;
				}

				$link_url    = isset( $link['url'] ) && is_string( $link['url'] ) ? trim( $link['url'] ) : '';
				$link_title  = isset( $link['title'] ) && is_string( $link['title'] ) ? trim( $link['title'] ) : '';
				$link_target = isset( $link['target'] ) && is_string( $link['target'] ) ? trim( $link['target'] ) : '_self';

				if ( 'links' === $cta_options && '' === $link_title ) {
					$link_title = __( 'Learn More', 'apache-2026' );
				}

				if ( '' === $link_url || '' === $link_title ) {
					continue;
				}

				$links[] = array(
					'url'    => $link_url,
					'title'  => $link_title,
					'target' => in_array( $link_target, array( '_blank', '_self' ), true ) ? $link_target : '_self',
				);
			}
		}

		if ( ! $image_id && '' === $title && '' === $content && empty( $links ) ) {
			continue;
		}

		$items[] = array(
			'align_image' => $align_image,
			'image_id'    => $image_id,
			'image_alt'   => $image_alt,
			'title'       => $title,
			'content'     => $content,
			'cta_options' => $cta_options,
			'links'       => $links,
		);
	}
}

if ( empty( $items ) ) {
	return;
}

$classes = array(
	'apache-flex',
	'apache-flex--image-content-repeater',
	'image-content-repeater',
);
?>

<section class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>">
	<div class="image-content-repeater__inner">
		<div class="image-content-repeater__rows">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$row_classes = array(
					'image-content-repeater__row',
					'image-content-repeater__row--image-' . $item['align_image'],
				);

				$cta_class = 'buttons' === $item['cta_options'] ? 'btn btn-primary' : 'btn btn-more';
				?>
				<article class="<?php echo esc_attr( implode( ' ', array_filter( $row_classes ) ) ); ?>">
					<div class="image-content-repeater__media">
						<?php if ( $item['image_id'] ) : ?>
							<?php
							echo wp_get_attachment_image(
								$item['image_id'],
								'large',
								false,
								array(
									'class'    => 'image-content-repeater__image',
									'alt'      => $item['image_alt'],
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
							?>
						<?php endif; ?>
					</div>

					<div class="image-content-repeater__content featured-post-slider__content">
						<?php if ( $item['title'] ) : ?>
							<h3 class="featured-post-slider__post-title image-content-repeater__title">
								<?php echo esc_html( $item['title'] ); ?>
							</h3>
						<?php endif; ?>

						<?php if ( $item['content'] ) : ?>
							<div class="image-content-repeater__copy featured-post-slider__excerpt">
								<?php echo apache_2026_kses_wysiwyg_content( $item['content'] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( 'none' !== $item['cta_options'] && ! empty( $item['links'] ) ) : ?>
							<div class="image-content-repeater__ctas image-content-repeater__ctas--<?php echo esc_attr( $item['cta_options'] ); ?>">
								<?php foreach ( $item['links'] as $link ) : ?>
									<a
										class="<?php echo esc_attr( $cta_class ); ?>"
										href="<?php echo esc_url( $link['url'] ); ?>"
										target="<?php echo esc_attr( $link['target'] ); ?>"
										<?php if ( '_blank' === $link['target'] ) : ?>
											rel="noopener noreferrer"
										<?php endif; ?>
									>
										<?php echo esc_html( $link['title'] ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
