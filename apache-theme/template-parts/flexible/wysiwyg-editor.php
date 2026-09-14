<?php
/**
 * Flexible layout: WYSIWYG Editor.
 *
 * ACF layout key: wysiwyg_editor
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$content = get_sub_field( 'content', false, false );

if ( ! is_string( $content ) || '' === trim( $content ) ) {
	foreach ( array( 'editor', 'wysiwyg' ) as $fallback_field ) {
		$fallback_content = get_sub_field( $fallback_field );

		if ( is_string( $fallback_content ) && '' !== trim( $fallback_content ) ) {
			$content = $fallback_content;
			break;
		}
	}
}

if ( ! is_string( $content ) || '' === trim( $content ) ) {
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

$margin_top     = apache_2026_sanitize_css_px_value( get_sub_field( 'margin_top' ), 0, 400 );
$indent_padding = apache_2026_sanitize_css_px_value( get_sub_field( 'indent_padding' ), 0, 120 );
$max_width      = apache_2026_sanitize_css_px_value( get_sub_field( 'max_width' ), 320, 1440 );

$classes = array(
	'apache-flex',
	'apache-flex--wysiwyg-editor',
);

$background_color = get_sub_field( 'background_color' );
$text_color       = get_sub_field( 'text_color' );
$text_alignment   = get_sub_field( 'text_alignment' );

if ( is_string( $background_color ) && '' !== trim( $background_color ) ) {
	$classes[] = 'apache-flex--bg-' . sanitize_html_class( $background_color );
}

if ( is_string( $text_color ) && '' !== trim( $text_color ) ) {
	$classes[] = 'apache-flex--text-' . sanitize_html_class( $text_color );
}

if ( is_string( $text_alignment ) && '' !== trim( $text_alignment ) ) {
	$classes[] = 'apache-flex--align-' . sanitize_html_class( $text_alignment );
}

if ( null !== $margin_top ) {
	$classes[] = 'apache-flex--has-custom-margin';
}

if ( null !== $indent_padding && $indent_padding > 0 ) {
	$classes[] = 'apache-flex--has-indent';
}

if ( null !== $max_width ) {
	$classes[] = 'apache-flex--has-custom-width';
}

$styles = array();

if ( null !== $margin_top ) {
	$styles[] = '--apache-flex-margin-top: ' . $margin_top . 'px';
}

if ( null !== $indent_padding ) {
	$styles[] = '--apache-wysiwyg-indent: ' . $indent_padding . 'px';
}

if ( null !== $max_width ) {
	$styles[] = '--apache-wysiwyg-max-width: ' . $max_width . 'px';
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
	<div class="apache-flex__inner apache-flex--wysiwyg-editor__inner">
		<div class="apache-wysiwyg">
			<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
		</div>
	</div>
</section>
