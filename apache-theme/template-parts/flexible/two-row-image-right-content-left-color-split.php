<?php
/**
 * Flexible layout: Two Row Image Right Content Left Color Split.
 *
 * ACF layout key: two_row_image_right_content_left_color_split
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$image          = get_sub_field( 'image' );
$block_color    = get_sub_field( 'block_color' );
$section_header = get_sub_field( 'section_header' );
$text_top       = (bool) get_sub_field( 'text_top' );
$headline       = get_sub_field( 'headline' );
$content        = get_sub_field( 'content' );
$button         = get_sub_field( 'button' );
$button_two     = get_sub_field( 'button_two' );

$headline = is_string( $headline ) ? trim( $headline ) : '';
$content  = is_string( $content ) ? trim( $content ) : '';

if ( is_array( $block_color ) ) {
	$block_color = reset( $block_color );
}

$block_color = is_string( $block_color ) ? sanitize_key( $block_color ) : 'gold';
$block_color = in_array( $block_color, array( 'gold', 'green', 'yellow' ), true ) ? $block_color : 'gold';

$section_title = '';
$line_color    = 'gold';

if ( is_array( $section_header ) ) {
	$section_title = isset( $section_header['title'] ) && is_string( $section_header['title'] ) ? trim( $section_header['title'] ) : '';
	$line_color    = isset( $section_header['line_color'] ) ? $section_header['line_color'] : 'gold';
}

if ( is_array( $line_color ) ) {
	$line_color = reset( $line_color );
}

$line_color = is_string( $line_color ) ? sanitize_key( $line_color ) : 'gold';
$line_color = in_array( $line_color, array( 'gold', 'green', 'yellow' ), true ) ? $line_color : 'gold';

$image_url    = '';
$image_alt    = '';
$image_width  = '';
$image_height = '';

if ( is_array( $image ) ) {
	$image_url    = isset( $image['url'] ) && is_string( $image['url'] ) ? trim( $image['url'] ) : '';
	$image_alt    = isset( $image['alt'] ) && is_string( $image['alt'] ) ? trim( $image['alt'] ) : '';
	$image_width  = isset( $image['width'] ) ? absint( $image['width'] ) : '';
	$image_height = isset( $image['height'] ) ? absint( $image['height'] ) : '';
} elseif ( is_string( $image ) ) {
	$image_url = trim( $image );
}

$normalize_button = static function ( mixed $button_field ): array {
	if ( ! is_array( $button_field ) ) {
		return array();
	}

	$label  = isset( $button_field['button_text'] ) && is_string( $button_field['button_text'] ) ? trim( $button_field['button_text'] ) : '';
	$url    = '';
	$target = '_self';

	if ( ! empty( $button_field['link_to_page'] ) && isset( $button_field['page'] ) ) {
		$page = $button_field['page'];
		$url  = is_string( $page ) ? trim( $page ) : '';
	} elseif ( ! empty( $button_field['link_to_url'] ) && isset( $button_field['link'] ) ) {
		$link = $button_field['link'];

		if ( is_array( $link ) ) {
			$url    = isset( $link['url'] ) && is_string( $link['url'] ) ? trim( $link['url'] ) : '';
			$target = isset( $link['target'] ) && is_string( $link['target'] ) ? trim( $link['target'] ) : '_self';
		} elseif ( is_string( $link ) ) {
			$url = trim( $link );
		}
	}

	$target = in_array( $target, array( '_blank', '_self' ), true ) ? $target : '_self';

	if ( '' === $label || '' === $url ) {
		return array();
	}

	return array(
		'label'  => $label,
		'url'    => $url,
		'target' => $target,
	);
};

$buttons = array_values(
	array_filter(
		array(
			$normalize_button( $button ),
			$normalize_button( $button_two ),
		)
	)
);

if ( '' === $image_url && '' === $section_title && '' === $headline && '' === $content && empty( $buttons ) ) {
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

$classes = array(
	'apache-flex',
	'apache-flex--two-row-image-right-content-left-color-split',
	'section-full-split-with-half-image',
	$block_color,
	'apache-flex--color-' . $block_color,
);

if ( $text_top ) {
	$classes[] = 'apache-flex--text-top';
}
?>

<section
	<?php if ( $section_id ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
>
	<div class="apache-flex--two-row-image-right-content-left-color-split__decor" aria-hidden="true">
		<span class="apache-flex--two-row-image-right-content-left-color-split__arrows apache-flex--two-row-image-right-content-left-color-split__arrows--top"></span>
		<span class="apache-flex--two-row-image-right-content-left-color-split__arrows apache-flex--two-row-image-right-content-left-color-split__arrows--bottom"></span>
	</div>

	<div class="apache-flex__inner apache-flex--two-row-image-right-content-left-color-split__inner">
		<div class="apache-flex--two-row-image-right-content-left-color-split__body">
			<?php if ( $section_title || $headline ) : ?>
				<header class="apache-flex--two-row-image-right-content-left-color-split__header">
					<?php if ( $section_title ) : ?>
						<p class="h5 section-name <?php echo esc_attr( $line_color ); ?> apache-flex--two-row-image-right-content-left-color-split__section-name">
							<?php echo esc_html( $section_title ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $headline ) : ?>
						<h2 class="h1 apache-flex--two-row-image-right-content-left-color-split__headline">
							<?php echo esc_html( $headline ); ?>
						</h2>
					<?php endif; ?>
				</header>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<div class="apache-flex--two-row-image-right-content-left-color-split__content">
					<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $buttons ) : ?>
				<div class="apache-flex--two-row-image-right-content-left-color-split__ctas">
					<?php foreach ( $buttons as $button_item ) : ?>
						<a
							class="btn btn-primary"
							href="<?php echo esc_url( $button_item['url'] ); ?>"
							target="<?php echo esc_attr( $button_item['target'] ); ?>"
							<?php if ( '_blank' === $button_item['target'] ) : ?>
								rel="noopener noreferrer"
							<?php endif; ?>
						>
							<?php echo esc_html( $button_item['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $image_url ) : ?>
			<figure class="apache-flex--two-row-image-right-content-left-color-split__media">
				<img
					class="apache-flex--two-row-image-right-content-left-color-split__image image-right"
					src="<?php echo esc_url( $image_url ); ?>"
					alt="<?php echo esc_attr( $image_alt ); ?>"
					<?php if ( $image_width ) : ?>
						width="<?php echo esc_attr( (string) $image_width ); ?>"
					<?php endif; ?>
					<?php if ( $image_height ) : ?>
						height="<?php echo esc_attr( (string) $image_height ); ?>"
					<?php endif; ?>
					loading="lazy"
					decoding="async"
				>
			</figure>
		<?php endif; ?>
	</div>
</section>
