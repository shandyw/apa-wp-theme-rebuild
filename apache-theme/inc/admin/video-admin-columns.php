<?php
/**
 * Video admin columns and copy-shortcode behavior.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_video_admin_columns( array $columns ): array {
	$updated_columns = array();

	foreach ( $columns as $key => $label ) {
		$updated_columns[ $key ] = $label;

		if ( 'title' === $key ) {
			$updated_columns['apache_video_poster']      = __( 'Poster', 'apache-2026' );
			$updated_columns['apache_video_source_type'] = __( 'Source Type', 'apache-2026' );
			$updated_columns['apache_video_shortcode']   = __( 'Shortcode', 'apache-2026' );
			$updated_columns['apache_video_copy']        = __( 'Copy Shortcode', 'apache-2026' );
		}
	}

	return $updated_columns;
}
add_filter( 'manage_edit-apache_video_columns', 'apache_2026_video_admin_columns' );

function apache_2026_render_video_admin_column( string $column, int $post_id ): void {
	if ( 'apache_video_poster' === $column ) {
		$poster = function_exists( 'get_field' ) ? get_field( 'video_poster_image', $post_id ) : array();
		$poster = is_array( $poster ) ? $poster : array();

		if ( ! empty( $poster['ID'] ) ) {
			echo wp_get_attachment_image( (int) $poster['ID'], array( 80, 45 ), false, array( 'style' => 'display:block;width:80px;height:auto;' ) );
		}
		return;
	}

	if ( 'apache_video_source_type' === $column ) {
		$source_type = function_exists( 'get_field' ) ? get_field( 'video_source_type', $post_id ) : '';
		$source_type = is_string( $source_type ) ? trim( $source_type ) : '';
		$labels      = array(
			'vimeo'       => __( 'Vimeo', 'apache-2026' ),
			'youtube'     => __( 'YouTube', 'apache-2026' ),
			'self_hosted' => __( 'Self-hosted', 'apache-2026' ),
			'embed'       => __( 'Embed', 'apache-2026' ),
		);

		echo esc_html( $labels[ $source_type ] ?? __( 'Unknown', 'apache-2026' ) );
		return;
	}

	if ( 'apache_video_shortcode' === $column ) {
		echo '<code>' . esc_html( sprintf( '[apache_video id="%d"]', $post_id ) ) . '</code>';
		return;
	}

	if ( 'apache_video_copy' === $column ) {
		$shortcode = sprintf( '[apache_video id="%d"]', $post_id );
		?>
		<button
			type="button"
			class="button apache-copy-shortcode"
			data-shortcode="<?php echo esc_attr( $shortcode ); ?>"
			data-default-label="<?php echo esc_attr__( 'Copy shortcode', 'apache-2026' ); ?>"
			data-copied-label="<?php echo esc_attr__( 'Copied', 'apache-2026' ); ?>"
		>
			<?php esc_html_e( 'Copy shortcode', 'apache-2026' ); ?>
		</button>
		<?php
	}
}
add_action( 'manage_apache_video_posts_custom_column', 'apache_2026_render_video_admin_column', 10, 2 );

function apache_2026_enqueue_video_admin_assets( string $hook_suffix ): void {
	if ( 'edit.php' !== $hook_suffix ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'apache_video' !== $screen->post_type ) {
		return;
	}

	$script_path = get_theme_file_path( 'assets/js/admin-video-copy-shortcode.js' );

	if ( ! file_exists( $script_path ) ) {
		return;
	}

	wp_enqueue_script(
		'apache-2026-admin-video-copy-shortcode',
		get_theme_file_uri( 'assets/js/admin-video-copy-shortcode.js' ),
		array(),
		apache_2026_asset_version( $script_path ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'apache_2026_enqueue_video_admin_assets' );
