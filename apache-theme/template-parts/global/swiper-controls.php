<?php
/**
 * Shared Swiper navigation controls.
 *
 * @package Apache_2026
 */

$args = isset( $args ) && is_array( $args ) ? $args : array();

$wrapper_class = isset( $args['wrapper_class'] ) && is_string( $args['wrapper_class'] ) ? trim( $args['wrapper_class'] ) : '';
$button_class  = isset( $args['button_class'] ) && is_string( $args['button_class'] ) ? trim( $args['button_class'] ) : '';
$slider_id     = isset( $args['slider_id'] ) && is_string( $args['slider_id'] ) ? trim( $args['slider_id'] ) : '';
$prev_label    = isset( $args['prev_label'] ) && is_string( $args['prev_label'] ) ? trim( $args['prev_label'] ) : __( 'Previous slide', 'apache-2026' );
$next_label    = isset( $args['next_label'] ) && is_string( $args['next_label'] ) ? trim( $args['next_label'] ) : __( 'Next slide', 'apache-2026' );

$wrapper_classes = array_filter(
	array(
		'apache-swiper-controls',
		$wrapper_class,
	)
);

$button_classes = array_filter(
	array(
		'apache-swiper-controls__button',
		$button_class,
	)
);

$controls_attr = '';

if ( '' !== $slider_id ) {
	$controls_attr = ' aria-controls="' . esc_attr( $slider_id ) . '"';
}
?>

<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
	<button
		class="<?php echo esc_attr( implode( ' ', array_merge( array( 'swiper-button-prev' ), $button_classes ) ) ); ?>"
		type="button"
		data-swiper-prev
		aria-label="<?php echo esc_attr( $prev_label ); ?>"
		<?php echo $controls_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	>
		<span class="screen-reader-text"><?php echo esc_html( $prev_label ); ?></span>
	</button>
	<button
		class="<?php echo esc_attr( implode( ' ', array_merge( array( 'swiper-button-next' ), $button_classes ) ) ); ?>"
		type="button"
		data-swiper-next
		aria-label="<?php echo esc_attr( $next_label ); ?>"
		<?php echo $controls_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	>
		<span class="screen-reader-text"><?php echo esc_html( $next_label ); ?></span>
	</button>
</div>
