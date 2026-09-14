<?php
/**
 * Flexible layout: Full Width One Col with Background Image.
 *
 * ACF layout key: full_width_one_col_bg_image
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$section_header = get_sub_field( 'section_header' );
$image          = get_sub_field( 'image' );
$headline       = get_sub_field( 'headline' );
$content        = get_sub_field( 'content' );
$button_text    = get_sub_field( 'button_text' );
$link_type      = get_sub_field( 'link_to_page_url' );
$page_url       = get_sub_field( 'page' );
$custom_url     = get_sub_field( 'url' );
$target         = get_sub_field( 'target' );

$section_title = '';
$line_color    = 'gold';

if ( is_array( $section_header ) ) {
	$section_title = isset( $section_header['title'] ) && is_string( $section_header['title'] )
		? trim( $section_header['title'] )
		: '';

	$line_color = isset( $section_header['line_color'] ) && is_string( $section_header['line_color'] ) && '' !== trim( $section_header['line_color'] )
		? trim( $section_header['line_color'] )
		: 'gold';
}

$image_url   = is_string( $image ) ? trim( $image ) : '';
$headline    = is_string( $headline ) ? trim( $headline ) : '';
$content     = is_string( $content ) ? trim( $content ) : '';
$button_text = is_string( $button_text ) ? trim( $button_text ) : '';
$link_type   = is_string( $link_type ) ? trim( $link_type ) : '';
$page_url    = is_string( $page_url ) ? trim( $page_url ) : '';
$custom_url  = is_string( $custom_url ) ? trim( $custom_url ) : '';
$target      = is_string( $target ) && in_array( $target, array( '_blank', '_self' ), true ) ? $target : '_self';

$button_url = '';

if ( 'page' === $link_type && $page_url ) {
	$button_url = $page_url;
} elseif ( 'url' === $link_type && $custom_url ) {
	$button_url = $custom_url;
}

if ( '' === $section_title && '' === $headline && '' === $content && ( '' === $button_text || '' === $button_url ) ) {
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

$margin_top = apache_2026_sanitize_css_px_value( get_sub_field( 'margin_top' ), 0, 400 );
$line_color = sanitize_html_class( $line_color );

$classes = array(
	'apache-flex',
	'apache-flex--full-width-one-col-bg-image',
	'fwlcimg',
);

if ( $image_url ) {
	$classes[] = 'apache-flex--has-background-image';
}

if ( null !== $margin_top ) {
	$classes[] = 'apache-flex--has-custom-margin';
}

$styles = array();

if ( null !== $margin_top ) {
	$styles[] = '--apache-flex-margin-top: ' . $margin_top . 'px';
}

if ( $image_url ) {
	$styles[] = '--apache-flex-background-image: url("' . esc_url( $image_url ) . '")';
}

$rel_attr = '_blank' === $target ? 'noopener noreferrer' : '';
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
	<div class="apache-flex__inner apache-flex--full-width-one-col-bg-image__header">
		<?php if ( $section_title ) : ?>
			<p class="h5 section-name <?php echo esc_attr( $line_color ); ?>">
				<?php echo esc_html( $section_title ); ?>
			</p>
		<?php endif; ?>
	</div>

	<div class="apache-flex--full-width-one-col-bg-image__media main">
		<div class="apache-flex__inner apache-flex--full-width-one-col-bg-image__inner">
			<div class="apache-flex--full-width-one-col-bg-image__content-panel">
				<?php if ( $headline ) : ?>
					<div class="apache-flex--full-width-one-col-bg-image__headline">
						<?php echo apache_2026_kses_content( $headline ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $content ) : ?>
					<div class="apache-wysiwyg apache-flex--full-width-one-col-bg-image__content">
						<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $button_text && $button_url ) : ?>
					<p class="apache-flex--full-width-one-col-bg-image__cta cta-container">
						<a
							class="btn btn-primary gold"
							href="<?php echo esc_url( $button_url ); ?>"
							target="<?php echo esc_attr( $target ); ?>"
							<?php if ( $rel_attr ) : ?>
								rel="<?php echo esc_attr( $rel_attr ); ?>"
							<?php endif; ?>
						>
							<?php echo esc_html( $button_text ); ?>
						</a>
					</p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
