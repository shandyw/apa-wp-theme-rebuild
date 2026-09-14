<?php
/**
 * Flexible layout: Callout Box.
 *
 * ACF layout key: callout_box
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$section_header = get_sub_field( 'section_header' );
$title          = get_sub_field( 'title' );
$copy           = get_sub_field( 'copy' );
$box_background = get_sub_field( 'box_background' );
$box_copy       = get_sub_field( 'box_copy' );
$remove_padding = apache_2026_bool_from_mixed( get_sub_field( 'remove_padding' ) );

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

$title    = is_string( $title ) ? trim( $title ) : '';
$copy     = is_string( $copy ) ? trim( $copy ) : '';
$box_copy = is_string( $box_copy ) ? trim( $box_copy ) : '';

$allowed_backgrounds = array( 'gold', 'grey' );
$box_background      = is_string( $box_background ) ? trim( $box_background ) : 'grey';
$box_background      = in_array( $box_background, $allowed_backgrounds, true ) ? $box_background : 'grey';

if ( '' === $section_title && '' === $title && '' === $copy && '' === $box_copy ) {
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


$line_color = sanitize_html_class( $line_color );

$classes = array(
	'apache-flex',
	'apache-flex--callout-box',
	'callout-box',
);

if ( $remove_padding ) {
	$classes[] = 'apache-flex--no-section-top-padding';
}

$styles = array();
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
	<div class="callout-box__inner">
		<?php if ( $section_title ) : ?>
			<p class="h5 section-name <?php echo esc_attr( $line_color ); ?>">
				<?php echo esc_html( $section_title ); ?>
			</p>
		<?php endif; ?>

		<?php if ( '' !== $title || '' !== $copy ) : ?>
			<div class="callout-box__intro">
				<?php if ( '' !== $title ) : ?>
					<h2 class="callout-box__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $copy ) : ?>
					<div class="apache-wysiwyg callout-box__copy">
						<?php echo apache_2026_kses_wysiwyg_content( $copy ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $box_copy ) : ?>
			<div class="<?php echo esc_attr( implode( ' ', array( 'callout-box__card', 'callout-box__card--' . sanitize_html_class( $box_background ) ) ) ); ?>">
				<div class="apache-wysiwyg callout-box__card-copy">
					<?php echo apache_2026_kses_wysiwyg_content( $box_copy ); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
