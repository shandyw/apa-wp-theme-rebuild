<?php
/**
 * Flexible layout: Two Column Split.
 *
 * ACF layout key: two_column_split
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$content_side         = get_sub_field( 'content_side' );
$background_color     = get_sub_field( 'background_color' );
$color_layout         = get_sub_field( 'color_layout' );
$justify_center_content = get_sub_field( 'justify_center_content' );
$eyebrow              = get_sub_field( 'eyebrow' );
$headline             = get_sub_field( 'headline' );
$copy                 = get_sub_field( 'copy' );
$media_type           = get_sub_field( 'media_type' );
$image                = get_sub_field( 'image' );
$video_popup_class    = get_sub_field( 'video_popup_class' );
$show_accents         = (bool) get_sub_field( 'show_accents' );

$content_side = is_string( $content_side ) ? sanitize_key( $content_side ) : 'right';
$content_side = in_array( $content_side, array( 'left', 'right' ), true ) ? $content_side : 'right';

$background_color = is_string( $background_color ) ? sanitize_key( $background_color ) : 'gray';
$background_color = in_array( $background_color, array( 'gray', 'gold', 'green', 'blue' ), true ) ? $background_color : 'gray';

$color_layout = is_string( $color_layout ) ? sanitize_key( $color_layout ) : 'split';
$color_layout = in_array( $color_layout, array( 'split', 'full' ), true ) ? $color_layout : 'split';
$justify_center_content = apache_2026_bool_from_mixed( $justify_center_content );

$media_type = is_string( $media_type ) ? sanitize_key( $media_type ) : 'image';
$media_type = in_array( $media_type, array( 'image', 'video_popup' ), true ) ? $media_type : 'image';

$headline             = is_string( $headline ) ? trim( $headline ) : '';
$copy                 = is_string( $copy ) ? trim( $copy ) : '';
$video_popup_class    = is_string( $video_popup_class ) ? trim( $video_popup_class ) : '';

$eyebrow_title         = '';
$eyebrow_line_color    = 'gold';
$use_parent_directory  = false;

if ( is_array( $eyebrow ) ) {
	$eyebrow_title        = isset( $eyebrow['title'] ) && is_string( $eyebrow['title'] ) ? trim( $eyebrow['title'] ) : '';
	$eyebrow_line_color   = isset( $eyebrow['line_color'] ) && is_string( $eyebrow['line_color'] ) ? trim( $eyebrow['line_color'] ) : 'gold';
	$use_parent_directory = ! empty( $eyebrow['use_parent_directory'] );
}

if ( $use_parent_directory ) {
	$current_post = get_post();

	if ( $current_post instanceof WP_Post && ! empty( $current_post->post_parent ) ) {
		$parent_title = get_the_title( (int) $current_post->post_parent );

		if ( is_string( $parent_title ) && '' !== trim( $parent_title ) ) {
			$eyebrow_title = trim( $parent_title );
		}
	}
}

$eyebrow_line_color = in_array( $eyebrow_line_color, array( 'gold', 'green', 'yellow' ), true ) ? $eyebrow_line_color : 'gold';

$image_url    = '';
$image_alt    = '';
$image_width  = 0;
$image_height = 0;

if ( is_array( $image ) ) {
	$image_url    = isset( $image['url'] ) && is_string( $image['url'] ) ? trim( $image['url'] ) : '';
	$image_alt    = isset( $image['alt'] ) && is_string( $image['alt'] ) ? trim( $image['alt'] ) : '';
	$image_width  = isset( $image['width'] ) ? absint( $image['width'] ) : 0;
	$image_height = isset( $image['height'] ) ? absint( $image['height'] ) : 0;
}

$buttons = array();

if ( function_exists( 'have_rows' ) && have_rows( 'button_repeater' ) ) {
	while ( have_rows( 'button_repeater' ) ) {
		the_row();

		$button = apache_2026_normalize_link_field( get_sub_field( 'button' ), 'btn btn-primary' );

		if ( ! empty( $button ) ) {
			$buttons[] = $button;
		}
	}
}

$has_popup_media = 'video_popup' === $media_type && '' !== $video_popup_class && '' !== $image_url;

if ( '' === $eyebrow_title && '' === $headline && '' === $copy && '' === $image_url && empty( $buttons ) && ! $has_popup_media ) {
	return;
}

$classes = array(
	'apache-flex',
	'apache-flex--two-column-split',
	'apache-flex--two-column-split--content-' . $content_side,
	'apache-flex--two-column-split--bg-' . $background_color,
	'apache-flex--two-column-split--layout-' . $color_layout,
);

if ( $show_accents ) {
	$classes[] = 'apache-flex--two-column-split--show-accents';
}

if ( 'full' === $color_layout && $justify_center_content ) {
	$classes[] = 'apache-flex--two-column-split--justify-center-content';
}
?>

<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php if ( $show_accents ) : ?>
		<div class="apache-flex--two-column-split__decor" aria-hidden="true">
			<span class="apache-flex--two-column-split__square">
				<img
					width="102"
					height="102"
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/square-accent.svg' ); ?>"
					alt=""
				>
			</span>
			<span class="apache-flex--two-column-split__rectangle">
				<img
					width="102"
					height="323"
					src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/rectangle-accent.svg' ); ?>"
					alt=""
				>
			</span>
		</div>
	<?php endif; ?>

	<div class="apache-flex__inner apache-flex--two-column-split__inner">
		<div class="apache-flex--two-column-split__media">
			<?php if ( $image_url ) : ?>
				<div class="apache-flex--two-column-split__media-frame">
					<?php if ( $has_popup_media ) : ?>
						<a
							class="<?php echo esc_attr( trim( 'apache-flex--two-column-split__media-link apache-flex--two-column-split__media-link--popup ' . $video_popup_class ) ); ?>"
							href="#"
						>
					<?php endif; ?>

					<img
						class="apache-flex--two-column-split__image"
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo esc_attr( $image_alt ); ?>"
						<?php if ( $image_width ) : ?>
							width="<?php echo esc_attr( (string) $image_width ); ?>"
						<?php endif; ?>
						<?php if ( $image_height ) : ?>
							height="<?php echo esc_attr( (string) $image_height ); ?>"
						<?php endif; ?>
						loading="lazy"
					>

					<?php if ( $has_popup_media ) : ?>
						<span class="apache-flex--two-column-split__play-button" aria-hidden="true"></span>
						<span class="screen-reader-text"><?php esc_html_e( 'Open video popup', 'apache-2026' ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="apache-flex--two-column-split__content-column">
			<div class="apache-flex--two-column-split__content-top<?php echo $eyebrow_title ? '' : ' apache-flex--two-column-split__content-top--no-eyebrow'; ?>">
				<?php if ( $eyebrow_title ) : ?>
					<p class="h5 section-name <?php echo esc_attr( $eyebrow_line_color ); ?> apache-flex--two-column-split__eyebrow">
						<?php echo esc_html( $eyebrow_title ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $headline ) : ?>
					<h2 class="apache-flex--two-column-split__headline">
						<?php echo esc_html( $headline ); ?>
					</h2>
				<?php endif; ?>
			</div>

			<div class="apache-flex--two-column-split__content-bottom">
				<?php if ( $copy ) : ?>
					<div class="apache-flex--two-column-split__copy apache-wysiwyg">
						<?php echo apache_2026_kses_wysiwyg_content( $copy ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $buttons ) ) : ?>
					<div class="apache-flex--two-column-split__ctas">
						<?php foreach ( $buttons as $button ) : ?>
							<a
								class="<?php echo esc_attr( $button['class'] ); ?>"
								href="<?php echo esc_url( $button['url'] ); ?>"
								target="<?php echo esc_attr( $button['target'] ); ?>"
								<?php if ( '_blank' === $button['target'] ) : ?>
									rel="noopener noreferrer"
								<?php endif; ?>
							>
								<?php echo esc_html( $button['title'] ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
