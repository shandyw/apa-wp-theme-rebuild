<?php
/**
 * Popup markup.
 *
 * @package Apache_2026
 */

$popup_id = isset( $args['popup_id'] ) ? absint( $args['popup_id'] ) : 0;

if ( $popup_id <= 0 ) {
	return;
}

$popup = get_post( $popup_id );

if ( ! $popup instanceof WP_Post || 'apache_popup' !== $popup->post_type || ! apache_2026_is_popup_enabled( $popup_id ) ) {
	return;
}

$popup_title   = trim( (string) get_the_title( $popup_id ) );
$show_title    = apache_2026_should_show_popup_title( $popup_id );
$popup_dom_id  = 'popup-' . $popup_id;
$title_dom_id  = $popup_dom_id . '-title';
$trigger_class = apache_2026_get_popup_trigger_class( $popup_id );
$auto_open     = apache_2026_should_popup_auto_open( $popup_id );
$auto_delay    = apache_2026_get_popup_auto_open_delay( $popup_id );

ob_start();
apache_2026_render_flexible_content( 'blocks', $popup_id );
$popup_content = trim( (string) ob_get_clean() );

if ( '' === $popup_content ) {
	if ( ! current_user_can( 'edit_post', $popup_id ) ) {
		return;
	}

	$popup_content = '<p class="site-popup__notice">' . esc_html__( 'Add flexible content blocks to this popup.', 'apache-2026' ) . '</p>';
}
?>

<div
	class="site-popup"
	id="<?php echo esc_attr( $popup_dom_id ); ?>"
	data-popup
	data-popup-id="<?php echo esc_attr( (string) $popup_id ); ?>"
	data-popup-trigger-class="<?php echo esc_attr( $trigger_class ); ?>"
	data-popup-auto-open="<?php echo $auto_open ? 'true' : 'false'; ?>"
	data-popup-auto-open-delay="<?php echo esc_attr( (string) $auto_delay ); ?>"
	aria-hidden="true"
	hidden
>
	<div class="site-popup__overlay" data-popup-close></div>
	<div
		class="site-popup__dialog"
		role="dialog"
		aria-modal="true"
		aria-labelledby="<?php echo esc_attr( $title_dom_id ); ?>"
		tabindex="-1"
		data-popup-dialog
	>
		<button
			class="site-popup__close"
			type="button"
			aria-label="<?php esc_attr_e( 'Close popup', 'apache-2026' ); ?>"
			data-popup-close
		>
			&times;
		</button>

		<div class="site-popup__content">
			<?php if ( $show_title && '' !== $popup_title ) : ?>
				<h2 class="site-popup__title" id="<?php echo esc_attr( $title_dom_id ); ?>">
					<?php echo esc_html( $popup_title ); ?>
				</h2>
			<?php else : ?>
				<span class="screen-reader-text" id="<?php echo esc_attr( $title_dom_id ); ?>">
					<?php esc_html_e( 'Popup dialog', 'apache-2026' ); ?>
				</span>
			<?php endif; ?>

			<div class="site-popup__body">
				<?php echo $popup_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</div>
