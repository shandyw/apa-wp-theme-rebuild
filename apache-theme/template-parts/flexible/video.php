<?php
/**
 * Flexible layout: Video.
 *
 * ACF layout key: video_module
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$selected_video  = get_sub_field( 'selected_video' );
$video_id        = apache_2026_get_video_post_id( $selected_video );
$section_id      = '';
$section_header  = get_sub_field( 'section_header' );
$section_title   = '';
$line_color      = 'gold';
$module_title    = get_sub_field( 'module_title' );
$module_title    = is_string( $module_title ) ? trim( $module_title ) : '';

if ( is_array( $section_header ) ) {
	$section_title = isset( $section_header['title'] ) && is_string( $section_header['title'] )
		? trim( $section_header['title'] )
		: '';

	$line_color = isset( $section_header['line_color'] ) && is_string( $section_header['line_color'] ) && '' !== trim( $section_header['line_color'] )
		? trim( $section_header['line_color'] )
		: 'gold';
}

foreach ( array( 'section_id', 'anchor_id', 'anchor_tag_name' ) as $id_field ) {
	$id_value = get_sub_field( $id_field );

	if ( is_string( $id_value ) && '' !== trim( $id_value ) ) {
		$section_id = sanitize_title( $id_value );
		break;
	}
}

if ( $video_id <= 0 ) {
	if ( current_user_can( 'edit_posts' ) ) {
		?>
		<section class="apache-flex apache-flex--video-module video-module">
			<div class="video-module__inner">
				<p class="video-module__notice"><?php esc_html_e( 'Select a Video to display this module.', 'apache-2026' ); ?></p>
			</div>
		</section>
		<?php
	}

	return;
}

$video_markup = apache_2026_render_video( $video_id );

if ( '' === trim( $video_markup ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		?>
		<section class="apache-flex apache-flex--video-module video-module">
			<div class="video-module__inner">
				<p class="video-module__notice"><?php esc_html_e( 'The selected Video could not be rendered.', 'apache-2026' ); ?></p>
			</div>
		</section>
		<?php
	}

	return;
}
?>

<section
	<?php if ( $section_id ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="apache-flex apache-flex--video-module video-module"
>
	<div class="video-module__inner">
		<?php if ( $module_title || $section_title ) : ?>
			<header class="video-module__header">
				<?php if ( $section_title ) : ?>
					<p class="h5 section-name <?php echo esc_attr( sanitize_html_class( $line_color ) ); ?>">
						<?php echo esc_html( $section_title ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $module_title ) : ?>
					<h2 class="video-module__title"><?php echo esc_html( $module_title ); ?></h2>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<div class="video-module__media">
			<?php echo $video_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</section>
