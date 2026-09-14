<?php
/**
 * Video shortcode registration.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_video_shortcode( array $atts = array() ): string {
	$atts = shortcode_atts(
		array(
			'id'    => 0,
			'class' => '',
		),
		$atts,
		'apache_video'
	);

	$video_id = absint( $atts['id'] );

	if ( $video_id <= 0 ) {
		return '';
	}

	$video = get_post( $video_id );

	if ( ! $video instanceof WP_Post || 'apache_video' !== $video->post_type ) {
		return current_user_can( 'edit_posts' ) ? '<p class="apache-video__notice">' . esc_html__( 'Invalid video shortcode.', 'apache-2026' ) . '</p>' : '';
	}

	return apache_2026_render_video(
		$video_id,
		array(
			'class' => $atts['class'],
		)
	);
}
add_shortcode( 'apache_video', 'apache_2026_video_shortcode' );
