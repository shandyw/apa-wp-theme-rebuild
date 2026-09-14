<?php
/**
 * Flexible layout: Map.
 *
 * ACF layout key: map
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$image      = apache_2026_normalize_acf_image( get_sub_field( 'map_image' ), 'large' );
$map_url    = get_sub_field( 'map_url' );
$content    = get_sub_field( 'content' );
$margin_top = apache_2026_sanitize_css_px_value( get_sub_field( 'margin_top' ), 0, 600 );

$map_url = is_string( $map_url ) ? trim( $map_url ) : '';
$content = is_string( $content ) ? trim( $content ) : '';

if ( empty( $image['url'] ) && '' === $content ) {
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

$classes = array(
	'apache-flex',
	'apache-flex--map',
	'fifty-fifty',
	'map',
	'hero',
);

if ( null !== $margin_top ) {
	$classes[] = 'apache-flex--has-custom-margin';
}

$styles = array();

if ( null !== $margin_top ) {
	$styles[] = '--apache-flex-margin-top: ' . $margin_top . 'px';
}
?>

<section
	<?php if ( $section_id ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
	<?php if ( $styles ) : ?>
		style="<?php echo esc_attr( implode( '; ', $styles ) ); ?>"
	<?php endif; ?>
>
	<div class="apache-flex--map__inner">
		<?php if ( ! empty( $image['url'] ) ) : ?>
			<figure class="apache-flex--map__media map-container">
				<?php if ( $map_url ) : ?>
					<a class="apache-flex--map__link" href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="screen-reader-text"><?php esc_html_e( 'Open map in a new window', 'apache-2026' ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $image['id'] ) ) : ?>
					<?php
					echo wp_get_attachment_image(
						$image['id'],
						'large',
						false,
						array(
							'class'   => 'apache-flex--map__image',
							'alt'     => $image['alt'],
							'loading' => 'lazy',
						)
					);
					?>
				<?php else : ?>
					<img
						class="apache-flex--map__image"
						src="<?php echo esc_url( $image['url'] ); ?>"
						alt="<?php echo esc_attr( $image['alt'] ); ?>"
						loading="lazy"
						decoding="async"
					>
				<?php endif; ?>

				<?php if ( $map_url ) : ?>
						<span class="map-button apache-flex--map__button" aria-hidden="true"></span>
					</a>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

		<div class="apache-flex--map__details">
			<div class="emergency-hotline apache-flex--map__hotline">
				<h2 class="apache-flex--map__hotline-title"><?php esc_html_e( '24-Hour Emergency Hotline', 'apache-2026' ); ?></h2>
				<a class="phone apache-flex--map__phone" href="tel:18552966400">855-296-6400</a>
			</div>

			<?php if ( $content ) : ?>
				<div class="address light-box-cut-top apache-flex--map__address">
					<div class="address-container apache-wysiwyg apache-flex--map__address-container">
						<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
