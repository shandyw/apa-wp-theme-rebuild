<?php
/**
 * Video component.
 *
 * Expects sanitized `$data` from `apache_2026_get_video_component_data()`.
 *
 * @package Apache_2026
 */

if ( ! isset( $data ) || ! is_array( $data ) ) {
	return;
}

$classes     = isset( $data['classes'] ) && is_array( $data['classes'] ) ? $data['classes'] : array( 'apache-video' );
$source_type = isset( $data['source_type'] ) && is_string( $data['source_type'] ) ? $data['source_type'] : '';
$post_title  = isset( $data['post_title'] ) && is_string( $data['post_title'] ) ? $data['post_title'] : __( 'Video', 'apache-2026' );
$caption     = isset( $data['caption'] ) && is_string( $data['caption'] ) ? $data['caption'] : '';
$poster      = isset( $data['poster'] ) && is_array( $data['poster'] ) ? $data['poster'] : array();
$media       = isset( $data['media'] ) && is_array( $data['media'] ) ? $data['media'] : array();
$has_poster  = ! empty( $poster['url'] ) && is_string( $poster['url'] );
$player_id   = 'apache-video-player-' . absint( $data['video_id'] ?? 0 );
$asset_uri   = get_template_directory_uri() . '/assets/images/player';
?>

<div
	class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
	data-apache-video
	data-video-type="<?php echo esc_attr( $source_type ); ?>"
	data-player-id="<?php echo esc_attr( $player_id ); ?>"
>
	<div class="apache-video__embed" data-apache-video-embed>
		<?php if ( $has_poster ) : ?>
			<img
				class="apache-video__poster"
				src="<?php echo esc_url( $poster['url'] ); ?>"
				alt="<?php echo esc_attr( $poster['alt'] ?? $post_title ); ?>"
				loading="lazy"
				decoding="async"
			>
		<?php endif; ?>
		<?php if ( 'vimeo' === $source_type && ! empty( $media['player_url'] ) ) : ?>
			<div
				id="<?php echo esc_attr( $player_id ); ?>"
				class="apache-video__iframe apache-video__vimeo-player"
				data-apache-vimeo-player
				data-apache-vimeo-url="<?php echo esc_url( $media['player_url'] ); ?>"
				data-apache-vimeo-autoplay="<?php echo ! empty( $media['autoplay'] ) ? 'true' : 'false'; ?>"
				data-apache-vimeo-muted="<?php echo ! empty( $media['muted'] ) ? 'true' : 'false'; ?>"
				data-apache-vimeo-loop="<?php echo ! empty( $media['loop'] ) ? 'true' : 'false'; ?>"
			></div>
		<?php elseif ( 'youtube' === $source_type && ! empty( $media['iframe_src'] ) ) : ?>
			<iframe
				id="<?php echo esc_attr( $player_id ); ?>"
				class="apache-video__iframe"
				src="<?php echo esc_url( $media['iframe_src'] ); ?>"
				title="<?php echo esc_attr( $post_title ); ?>"
				allow="<?php echo esc_attr( $media['iframe_allow'] ?? '' ); ?>"
				allowfullscreen
				loading="lazy"
				referrerpolicy="strict-origin-when-cross-origin"
			></iframe>
		<?php elseif ( 'self_hosted' === $source_type && ! empty( $media['file_url'] ) && ! empty( $media['mime'] ) ) : ?>
			<video
				id="<?php echo esc_attr( $player_id ); ?>"
				class="apache-video__native"
				<?php if ( ! empty( $media['autoplay'] ) ) : ?>
					autoplay
				<?php endif; ?>
				<?php if ( ! empty( $media['muted'] ) ) : ?>
					muted
				<?php endif; ?>
				<?php if ( ! empty( $media['loop'] ) ) : ?>
					loop
				<?php endif; ?>
				<?php if ( ! empty( $media['poster'] ) ) : ?>
					poster="<?php echo esc_url( $media['poster'] ); ?>"
				<?php endif; ?>
				playsinline
				preload="metadata"
			>
				<source src="<?php echo esc_url( $media['file_url'] ); ?>" type="<?php echo esc_attr( $media['mime'] ); ?>">
			</video>
		<?php elseif ( 'embed' === $source_type && ! empty( $media['embed_html'] ) ) : ?>
			<?php echo $media['embed_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>

		<?php if ( 'embed' !== $source_type ) : ?>
			<button
				type="button"
				class="apache-video__surface"
				data-apache-video-surface
				aria-label="<?php echo esc_attr( sprintf( __( 'Play or pause %s', 'apache-2026' ), $post_title ) ); ?>"
			></button>

			<button
				type="button"
				class="apache-video__play-button"
				data-apache-video-play
				aria-label="<?php echo esc_attr( sprintf( __( 'Play %s', 'apache-2026' ), $post_title ) ); ?>"
			>
				<img
					class="apache-video__icon-image apache-video__icon-image--overlay-play"
					src="<?php echo esc_url( $asset_uri . '/play.svg' ); ?>"
					alt=""
					aria-hidden="true"
				>
			</button>

			<div class="apache-video__controls" data-apache-video-controls>
				<div class="apache-video__controls-group apache-video__controls-group--leading">
					<button type="button" class="apache-video__control-button" data-apache-video-seek="-10" aria-label="<?php esc_attr_e( 'Rewind 10 seconds', 'apache-2026' ); ?>">
						<img
							class="apache-video__icon-image apache-video__icon-image--seek"
							src="<?php echo esc_url( $asset_uri . '/rewind.svg' ); ?>"
							alt=""
							aria-hidden="true"
						>
					</button>
					<button type="button" class="apache-video__control-button" data-apache-video-play-toggle aria-label="<?php esc_attr_e( 'Play or pause video', 'apache-2026' ); ?>">
						<img
							class="apache-video__icon-image apache-video__icon-image--play"
							src="<?php echo esc_url( $asset_uri . '/play.svg' ); ?>"
							alt=""
							aria-hidden="true"
						>
						<span class="apache-video__pause-icon" aria-hidden="true"></span>
					</button>
					<button type="button" class="apache-video__control-button" data-apache-video-seek="10" aria-label="<?php esc_attr_e( 'Forward 10 seconds', 'apache-2026' ); ?>">
						<img
							class="apache-video__icon-image apache-video__icon-image--seek"
							src="<?php echo esc_url( $asset_uri . '/fast-forward.svg' ); ?>"
							alt=""
							aria-hidden="true"
						>
					</button>
				</div>

				<div class="apache-video__progress-wrap">
					<input
						type="range"
						class="apache-video__progress"
						min="0"
						max="100"
						step="0.1"
						value="0"
						data-apache-video-progress
						aria-label="<?php esc_attr_e( 'Video progress', 'apache-2026' ); ?>"
					>
				</div>

				<div class="apache-video__controls-group apache-video__controls-group--trailing">
					<span class="apache-video__time" data-apache-video-time>00:00</span>
					<button type="button" class="apache-video__control-button" data-apache-video-mute aria-label="<?php esc_attr_e( 'Mute or unmute video', 'apache-2026' ); ?>">
						<img
							class="apache-video__icon-image apache-video__icon-image--volume-on"
							src="<?php echo esc_url( $asset_uri . '/sound.svg' ); ?>"
							alt=""
							aria-hidden="true"
						>
						<img
							class="apache-video__icon-image apache-video__icon-image--volume-off"
							src="<?php echo esc_url( $asset_uri . '/mute.svg' ); ?>"
							alt=""
							aria-hidden="true"
						>
					</button>
					<input
						type="range"
						class="apache-video__volume"
						min="0"
						max="1"
						step="0.05"
						value="<?php echo ! empty( $media['muted'] ) ? '0' : '1'; ?>"
						data-apache-video-volume
						aria-label="<?php esc_attr_e( 'Video volume', 'apache-2026' ); ?>"
					>
					<button type="button" class="apache-video__control-button" data-apache-video-fullscreen aria-label="<?php esc_attr_e( 'Toggle fullscreen', 'apache-2026' ); ?>">
	
								<img
							class="apache-video__icon-image apache-video__icon-image--expand"
							src="<?php echo esc_url( $asset_uri . '/expand.svg' ); ?>"
							alt=""
							aria-hidden="true"
						>
						
					</button>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( '' !== $caption ) : ?>
		<div class="apache-video__caption">
			<?php echo $caption; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>
</div>
