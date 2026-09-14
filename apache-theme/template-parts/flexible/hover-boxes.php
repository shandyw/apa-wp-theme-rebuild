<?php
/**
 * Flexible layout: Hover Boxes.
 *
 * ACF layout key: hover_boxes
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$eyebrow = get_sub_field( 'eyebrow' );
$headline = get_sub_field( 'headline' );
$copy = get_sub_field( 'copy' );

$eyebrow = is_string( $eyebrow ) ? trim( $eyebrow ) : '';
$headline = is_string( $headline ) ? trim( $headline ) : '';
$copy = is_string( $copy ) ? trim( $copy ) : '';

$items = array();

if ( function_exists( 'have_rows' ) && have_rows( 'items' ) ) {
	while ( have_rows( 'items' ) ) {
		the_row();

		$image = get_sub_field( 'image' );
		$item_eyebrow = get_sub_field( 'item_eyebrow' );
		$item_headline = get_sub_field( 'item_headline' );
		$item_copy = get_sub_field( 'item_copy' );
		$link = get_sub_field( 'link' );
		$block_color = get_sub_field( 'block_color' );

		$item_eyebrow = is_string( $item_eyebrow ) ? trim( $item_eyebrow ) : '';
		$item_headline = is_string( $item_headline ) ? trim( $item_headline ) : '';
		$item_copy = is_string( $item_copy ) ? trim( $item_copy ) : '';
		$block_color = is_scalar( $block_color ) ? sanitize_key( (string) $block_color ) : 'gold';
		$block_color = in_array( $block_color, array( 'gold', 'green', 'red', 'blue', 'navy' ), true ) ? $block_color : 'gold';

		$image_id = 0;
		$image_url = '';
		$image_alt = '';

		if ( is_array( $image ) ) {
			$image_id = isset( $image['ID'] ) ? absint( $image['ID'] ) : 0;
			$image_url = isset( $image['url'] ) && is_string( $image['url'] ) ? trim( $image['url'] ) : '';
			$image_alt = isset( $image['alt'] ) && is_string( $image['alt'] ) ? trim( $image['alt'] ) : '';
		} elseif ( is_numeric( $image ) ) {
			$image_id = absint( $image );
		} elseif ( is_string( $image ) ) {
			$image_url = trim( $image );
		}

		if ( '' === $image_alt && $image_id ) {
			$image_alt = trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
		}

		$link_url = '';
		$link_title = '';
		$link_target = '_self';

		if ( is_array( $link ) ) {
			$link_url = isset( $link['url'] ) && is_string( $link['url'] ) ? trim( $link['url'] ) : '';
			$link_title = isset( $link['title'] ) && is_string( $link['title'] ) ? trim( $link['title'] ) : '';
			$link_target = isset( $link['target'] ) && is_string( $link['target'] ) ? trim( $link['target'] ) : '_self';
		}

		$link_target = in_array( $link_target, array( '_self', '_blank' ), true ) ? $link_target : '_self';

		if ( ! $image_id && '' === $image_url && '' === $item_eyebrow && '' === $item_headline && '' === $item_copy && '' === $link_url ) {
			continue;
		}

		$items[] = array(
			'image_id' => $image_id,
			'image_url' => $image_url,
			'image_alt' => $image_alt,
			'eyebrow' => $item_eyebrow,
			'headline' => $item_headline,
			'copy' => $item_copy,
			'link_url' => $link_url,
			'link_title' => $link_title,
			'link_target' => $link_target,
			'block_color' => $block_color,
		);
	}
}

if ( '' === $eyebrow && '' === $headline && '' === $copy && empty( $items ) ) {
	return;
}

$section_id = '';

foreach ( array( 'section_id', 'anchor_id', 'anchor_tag_name' ) as $id_field ) {
	$id_value = get_sub_field( $id_field );

	if ( is_string( $id_value ) && '' !== trim( $id_value ) ) {
		$section_id = sanitize_title( $id_value );
		break;
	}
}
?>

<section
	<?php if ( $section_id ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="apache-flex apache-flex--hover-boxes hover-boxes"
>	<?php if ( '' !== $eyebrow) : ?>
			<div class="hover-boxes__row">
				<?php if ( '' !== $eyebrow ) : ?>
					<p class="hover-boxes__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>

				
			</div>
		<?php endif; ?>
	<div class="hover-boxes__inner">
	

		<?php if ( ! empty( $items || '' !== $headline || '' !== $copy  ) ) : ?>
			<div class="hover-boxes__grid">
				<?php if ( '' !== $headline || '' !== $copy) : ?>
					<article class="hover-boxes__card hover-boxes__card--intro">
						<?php if ( '' !== $headline ) : ?>
					<h2 class="hover-boxes__headline"><?php echo esc_html( $headline ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $copy ) : ?>
					<div class="hover-boxes__copy apache-wysiwyg">
						<?php echo apache_2026_kses_wysiwyg_content( $copy ); ?>
					</div>
				<?php endif; ?>
					
					</article>
				<?php endif; ?>	
			
				<?php foreach ( $items as $item ) : ?>
					<article
						class="hover-boxes__card hover-boxes__card--<?php echo esc_attr( $item['block_color'] ); ?>"
						tabindex="0"
					>
						<div class="hover-boxes__media">
							<?php if ( $item['image_id'] ) : ?>
								<?php
								echo wp_get_attachment_image(
									$item['image_id'],
									'large',
									false,
									array(
										'class' => 'hover-boxes__image',
										'alt' => $item['image_alt'],
										'loading' => 'lazy',
										'decoding' => 'async',
									)
								);
								?>
							<?php elseif ( '' !== $item['image_url'] ) : ?>
								<img
									class="hover-boxes__image"
									src="<?php echo esc_url( $item['image_url'] ); ?>"
									alt="<?php echo esc_attr( $item['image_alt'] ); ?>"
									loading="lazy"
									decoding="async"
								>
							<?php endif; ?>
						</div>

						<div class="hover-boxes__panel">
							<div class="hover-boxes__panel-static">
								<?php if ( '' !== $item['eyebrow'] ) : ?>
									<p class="hover-boxes__item-eyebrow"><?php echo esc_html( $item['eyebrow'] ); ?></p>
								<?php endif; ?>

								<?php if ( '' !== $item['headline'] ) : ?>
									<h3 class="hover-boxes__item-headline"><?php echo esc_html( $item['headline'] ); ?></h3>
								<?php endif; ?>
							</div>

							<div class="hover-boxes__panel-hover">
								<?php if ( '' !== $item['copy'] ) : ?>
									<div class="hover-boxes__item-copy apache-wysiwyg">
										<?php echo apache_2026_kses_wysiwyg_content( $item['copy'] ); ?>
									</div>
								<?php endif; ?>

								<?php if ( '' !== $item['link_url'] && '' !== $item['link_title'] ) : ?>
									<p class="hover-boxes__item-link-wrap">
										<a
											class="hover-boxes__item-link"
											href="<?php echo esc_url( $item['link_url'] ); ?>"
											target="<?php echo esc_attr( $item['link_target'] ); ?>"
											<?php if ( '_blank' === $item['link_target'] ) : ?>
												rel="noopener noreferrer"
											<?php endif; ?>
										>
											<?php echo esc_html( $item['link_title'] ); ?>
										</a>
									</p>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
