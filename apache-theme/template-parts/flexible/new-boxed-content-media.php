<?php
/**
 * Flexible layout: NEW Boxed Content Media.
 *
 * ACF layout key: new_boxed_content_media
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$margin_top = apache_2026_sanitize_css_px_value( get_sub_field( 'margin_top' ), 0, 400 );
$video      = get_sub_field( 'video' );
$image      = get_sub_field( 'image' );
$headline   = get_sub_field( 'headline' );
$content    = get_sub_field( 'content' );

$video_id  = apache_2026_get_video_post_id( $video );
$headline = is_string( $headline ) ? trim( $headline ) : '';
$content  = is_string( $content ) ? trim( $content ) : '';

$image_url = '';

if ( is_array( $image ) ) {
	$image_url = isset( $image['url'] ) && is_string( $image['url'] ) ? trim( $image['url'] ) : '';
} elseif ( is_string( $image ) ) {
	$image_url = trim( $image );
}

if ( 0 === $video_id && '' === $image_url && '' === $headline && '' === $content ) {
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
	'apache-flex--new-boxed-content-media',
	'section-boxed-content-media',
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
	<div class="apache-flex__inner apache-flex--new-boxed-content-media__inner gray">
		<div class="apache-flex--new-boxed-content-media__content content-container">
			<?php if ( $headline ) : ?>
				<h2 class="h3 apache-flex--new-boxed-content-media__headline">
					<?php echo esc_html( $headline ); ?>
				</h2>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<div class="apache-flex--new-boxed-content-media__wysiwyg">
					<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $video_id || $image_url ) : ?>
			<figure class="apache-flex--new-boxed-content-media__media">
				<?php if ( $video_id ) : ?>
					<div class="apache-flex--new-boxed-content-media__video">
						<?php echo apache_2026_render_video( $video_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php elseif ( $image_url ) : ?>
					<img
						class="apache-flex--new-boxed-content-media__image"
						src="<?php echo esc_url( $image_url ); ?>"
						alt=""
						loading="lazy"
						decoding="async"
					>
				<?php endif; ?>
			</figure>
		<?php endif; ?>
	</div>
</section>
