<?php
/**
 * Flexible layout: Gallery.
 *
 * ACF layout key: gallery_module
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$selected_gallery = get_sub_field( 'selected_gallery' );
$gallery_id       = apache_2026_get_gallery_post_id( $selected_gallery );
$section_id       = '';
$section_header   = get_sub_field( 'section_header' );
$section_title    = '';
$line_color       = 'gold';

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

if ( $gallery_id <= 0 ) {
	if ( current_user_can( 'edit_posts' ) ) {
		?>
		<section class="apache-flex apache-flex--gallery-module gallery-module">
			<div class="gallery-module__inner">
				<p class="gallery-module__notice"><?php esc_html_e( 'Select a Gallery to display this module.', 'apache-2026' ); ?></p>
			</div>
		</section>
		<?php
	}

	return;
}

$gallery_images = function_exists( 'get_field' ) ? get_field( 'gallery_images', $gallery_id ) : array();
$gallery_images = apache_2026_normalize_gallery_images( $gallery_images );

if ( empty( $gallery_images ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		?>
		<section class="apache-flex apache-flex--gallery-module gallery-module">
			<div class="gallery-module__inner">
				<p class="gallery-module__notice"><?php esc_html_e( 'The selected Gallery does not have any images yet.', 'apache-2026' ); ?></p>
			</div>
		</section>
		<?php
	}

	return;
}

$module_title        = get_sub_field( 'module_title' );
$module_title        = is_string( $module_title ) ? trim( $module_title ) : '';
$gallery_intro       = function_exists( 'get_field' ) ? get_field( 'gallery_intro', $gallery_id ) : '';
$gallery_intro       = is_string( $gallery_intro ) ? trim( $gallery_intro ) : '';
$gallery_layout      = function_exists( 'get_field' ) ? get_field( 'gallery_default_layout', $gallery_id ) : 'grid';
$gallery_columns     = function_exists( 'get_field' ) ? get_field( 'gallery_columns', $gallery_id ) : '3';
$gallery_captions    = function_exists( 'get_field' ) ? get_field( 'show_captions', $gallery_id ) : true;
$display_style       = get_sub_field( 'display_style' );
$columns             = get_sub_field( 'columns' );
$module_show_caption = get_sub_field( 'show_captions' );
$resolved_layout     = apache_2026_normalize_gallery_layout(
	is_string( $display_style ) && 'default' !== $display_style ? $display_style : $gallery_layout,
	'grid'
);
$resolved_columns = apache_2026_normalize_gallery_columns(
	is_string( $columns ) && 'default' !== $columns ? $columns : $gallery_columns,
	3
);
$show_captions = null === $module_show_caption || '' === $module_show_caption
	? apache_2026_bool_from_mixed( $gallery_captions )
	: apache_2026_bool_from_mixed( $module_show_caption );
$gallery_items = array();

foreach ( $gallery_images as $index => $image_id ) {
	$lightbox_url = wp_get_attachment_image_url( $image_id, 'full' );
	$image_html = wp_get_attachment_image(
		$image_id,
		'large',
		false,
		array(
			'class'    => 'gallery-module__image',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);

	if ( '' === $image_html ) {
		continue;
	}

	$lightbox_url = is_string( $lightbox_url ) ? trim( $lightbox_url ) : '';
	$lightbox_url = wp_http_validate_url( $lightbox_url ) ? $lightbox_url : '';

	$caption       = wp_get_attachment_caption( $image_id );
	$caption       = is_string( $caption ) ? trim( $caption ) : '';
	$context_label = trim( (string) get_the_title( $image_id ) );
	$context_label = '' !== $context_label ? $context_label : sprintf( __( 'image %d', 'apache-2026' ), $index + 1 );
	$image_alt     = trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
	$low_res_file  = function_exists( 'get_field' ) ? apache_2026_get_gallery_download_file( get_field( 'low_res_download', $image_id ) ) : array();
	$high_res_file = function_exists( 'get_field' ) ? apache_2026_get_gallery_download_file( get_field( 'high_res_download', $image_id ) ) : array();

	if ( empty( $low_res_file ) && empty( $high_res_file ) ) {
		$low_res_file = apache_2026_get_gallery_attachment_file( $image_id );
	}

	$gallery_items[] = array(
		'caption'       => $caption,
		'context_label' => $context_label,
		'has_caption'   => $show_captions && '' !== $caption,
		'high_res_file' => $high_res_file,
		'image_alt'     => $image_alt,
		'image_html'    => $image_html,
		'lightbox_url'  => $lightbox_url,
		'low_res_file'  => $low_res_file,
	);
}

if ( empty( $gallery_items ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		?>
		<section class="apache-flex apache-flex--gallery-module gallery-module">
			<div class="gallery-module__inner">
				<p class="gallery-module__notice"><?php esc_html_e( 'The selected Gallery could not render any valid images.', 'apache-2026' ); ?></p>
			</div>
		</section>
		<?php
	}

	return;
}

$is_carousel = 'carousel' === $resolved_layout;
$slider_id   = $is_carousel ? wp_unique_id( 'apache-gallery-' ) : '';
$image_count = count( $gallery_items );
$grid_class  = 'gallery-module__grid--' . $resolved_columns;
$gallery_label = $module_title;
$lightbox_id = wp_unique_id( 'apache-gallery-lightbox-' );

if ( '' === $gallery_label ) {
	$gallery_label = get_the_title( $gallery_id );
}

$gallery_label = is_string( $gallery_label ) && '' !== trim( $gallery_label )
	? trim( $gallery_label )
	: __( 'Gallery', 'apache-2026' );
?>

<section
	<?php if ( $section_id ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="apache-flex apache-flex--gallery-module gallery-module"
>
	<div class="gallery-module__inner">
		<?php if ( $module_title || $gallery_intro ) : ?>
			<header class="gallery-module__header">
				<?php if ( $section_title ) : ?>
					<p class="h5 section-name <?php echo esc_attr( sanitize_html_class( $line_color ) ); ?>">
						<?php echo esc_html( $section_title ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $module_title ) : ?>
					<h2 class="gallery-module__title"><?php echo esc_html( $module_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $gallery_intro ) : ?>
					<div class="gallery-module__intro"><?php echo apache_2026_kses_wysiwyg_content( $gallery_intro ); ?></div>
				<?php endif; ?>
			</header>
		<?php elseif ( $section_title ) : ?>
			<header class="gallery-module__header">
				<p class="h5 section-name <?php echo esc_attr( sanitize_html_class( $line_color ) ); ?>">
					<?php echo esc_html( $section_title ); ?>
				</p>
			</header>
		<?php endif; ?>

		<?php if ( $is_carousel ) : ?>
			<div class="gallery-module__viewport apache-swiper swiper" id="<?php echo esc_attr( $slider_id ); ?>" data-apache-swiper="gallery" data-gallery-columns="<?php echo esc_attr( (string) $resolved_columns ); ?>" data-swiper-loop="<?php echo 1 < $image_count ? 'true' : 'false'; ?>" data-swiper-autoplay="false" data-swiper-effect="slide" aria-label="<?php echo esc_attr( $gallery_label ); ?>" aria-roledescription="<?php esc_attr_e( 'carousel', 'apache-2026' ); ?>">
				<div class="gallery-module__grid <?php echo esc_attr( $grid_class ); ?> swiper-wrapper">
		<?php else : ?>
			<div class="gallery-module__grid <?php echo esc_attr( $grid_class ); ?>">
		<?php endif; ?>
			<?php foreach ( $gallery_items as $item ) : ?>
				<?php
				$item_classes = array( 'gallery-module__item' );

				if ( $is_carousel ) {
					$item_classes[] = 'swiper-slide';
				}
				?>
				<figure class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>">
					<div class="gallery-module__image-frame">
						<?php if ( '' !== $item['lightbox_url'] ) : ?>
							<button
								class="gallery-module__trigger"
								type="button"
								data-gallery-lightbox-trigger
								data-gallery-lightbox-target="<?php echo esc_attr( $lightbox_id ); ?>"
								data-gallery-lightbox-src="<?php echo esc_url( $item['lightbox_url'] ); ?>"
								data-gallery-lightbox-alt="<?php echo esc_attr( $item['image_alt'] ); ?>"
								data-gallery-lightbox-caption="<?php echo esc_attr( $item['caption'] ); ?>"
								data-gallery-lightbox-label="<?php echo esc_attr( $item['context_label'] ); ?>"
								data-gallery-lightbox-low-res-url="<?php echo ! empty( $item['low_res_file'] ) ? esc_url( $item['low_res_file']['url'] ) : ''; ?>"
								data-gallery-lightbox-high-res-url="<?php echo ! empty( $item['high_res_file'] ) ? esc_url( $item['high_res_file']['url'] ) : ''; ?>"
								data-gallery-lightbox-low-res-download="<?php echo ! empty( $item['low_res_file'] ) && apache_2026_should_add_download_attribute( $item['low_res_file']['url'] ) ? 'true' : 'false'; ?>"
								data-gallery-lightbox-high-res-download="<?php echo ! empty( $item['high_res_file'] ) && apache_2026_should_add_download_attribute( $item['high_res_file']['url'] ) ? 'true' : 'false'; ?>"
								aria-label="<?php echo esc_attr( sprintf( __( 'Open larger image for %s', 'apache-2026' ), $item['context_label'] ) ); ?>"
							>
								<?php echo $item['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</button>
						<?php else : ?>
							<?php echo $item['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>

					<?php if ( $item['has_caption'] ) : ?>
						<figcaption class="gallery-module__meta">
							<p class="gallery-module__caption"><?php echo esc_html( $item['caption'] ); ?></p>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		<?php if ( $is_carousel ) : ?>
				</div>
			</div>

			<?php if ( 1 < $image_count ) : ?>
				<?php
				get_template_part(
					'template-parts/global/swiper-controls',
					null,
					array(
						'wrapper_class' => 'gallery-module__controls',
						'button_class'  => 'gallery-module__button',
						'slider_id'     => $slider_id,
						'prev_label'    => __( 'Previous gallery image', 'apache-2026' ),
						'next_label'    => __( 'Next gallery image', 'apache-2026' ),
					)
				);
				?>
			<?php endif; ?>
		<?php else : ?>
			</div>
		<?php endif; ?>

		<dialog class="gallery-module__lightbox" id="<?php echo esc_attr( $lightbox_id ); ?>" data-gallery-lightbox>
			<div class="gallery-module__lightbox-shell">
				<button
					class="gallery-module__lightbox-close"
					type="button"
					data-gallery-lightbox-close
					aria-label="<?php esc_attr_e( 'Close image dialog', 'apache-2026' ); ?>"
				>
					<span aria-hidden="true">&times;</span>
				</button>

				<div class="gallery-module__lightbox-stage">
					<img
						class="gallery-module__lightbox-image"
						src=""
						alt=""
						data-gallery-lightbox-image
					>
				</div>

				<div class="gallery-module__lightbox-meta" data-gallery-lightbox-meta hidden>
					<p class="gallery-module__lightbox-caption" data-gallery-lightbox-caption></p>
					<div class="gallery-module__lightbox-downloads" data-gallery-lightbox-downloads hidden>
						<span class="gallery-module__lightbox-download-label"><?php esc_html_e( 'Download:', 'apache-2026' ); ?></span>
						<a
							class="gallery-module__lightbox-download-link"
							href=""
							data-gallery-lightbox-low-res
							hidden
						>
							<?php esc_html_e( 'Low Res', 'apache-2026' ); ?>
						</a>
						<span class="gallery-module__lightbox-download-separator" data-gallery-lightbox-separator hidden>|</span>
						<a
							class="gallery-module__lightbox-download-link"
							href=""
							data-gallery-lightbox-high-res
							hidden
						>
							<?php esc_html_e( 'High Res', 'apache-2026' ); ?>
						</a>
					</div>
				</div>
			</div>
		</dialog>
	</div>
</section>
