<?php
/**
 * Global announcement banner.
 *
 * ACF field group: Announcement Banner
 *
 * @package Apache_2026
 */

$data      = isset( $args['data'] ) && is_array( $args['data'] ) ? $args['data'] : apache_2026_get_announcement_banner_data();
$is_active = ! empty( $data['is_active'] );
$message   = isset( $data['message'] ) && is_string( $data['message'] ) ? trim( $data['message'] ) : '';

if ( ! $is_active || '' === $message ) {
	return;
}
?>

<div class="announcement-banner" role="region" aria-label="<?php esc_attr_e( 'Site announcement', 'apache-2026' ); ?>" data-announcement-banner>
	<div class="announcement-banner__inner">
		<div class="announcement-banner__message">
			<?php echo apache_2026_kses_wysiwyg_content( $message ); ?>
		</div>

		<button class="announcement-banner__close" type="button" aria-label="<?php esc_attr_e( 'Dismiss announcement', 'apache-2026' ); ?>" data-announcement-close>
			<span aria-hidden="true">&times;</span>
		</button>
	</div>
</div>
