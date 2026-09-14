<?php
/**
 * Flexible layout: Media Hero.
 *
 * ACF layout key: media_hero
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$background_image = get_sub_field( 'background_image' );
$parallax         = apache_2026_bool_from_mixed( get_sub_field( 'parallax' ) );
$layout_width     = get_sub_field( 'layout_width' );
$content_align    = get_sub_field( 'content_align' );
$eyebrow_text     = get_sub_field( 'eyebrow_text' );
$headline         = get_sub_field( 'headline' );
$copy             = get_sub_field( 'copy' );
$cta_options      = get_sub_field( 'cta_options' );
$link             = get_sub_field( 'link' );
$popup_class      = get_sub_field( 'popup_class' );

$layout_width  = is_scalar( $layout_width ) ? sanitize_key( (string) $layout_width ) : 'full_width';
$content_align = is_scalar( $content_align ) ? sanitize_key( (string) $content_align ) : 'left';
$cta_options   = is_scalar( $cta_options ) ? sanitize_key( (string) $cta_options ) : 'link';
$eyebrow_text  = is_string( $eyebrow_text ) ? trim( $eyebrow_text ) : '';
$headline      = is_string( $headline ) ? trim( $headline ) : '';
$copy          = is_string( $copy ) ? trim( $copy ) : '';
$popup_class   = is_string( $popup_class ) ? trim( $popup_class ) : '';

$layout_width  = in_array( $layout_width, array( 'full_width', 'content_width' ), true ) ? $layout_width : 'full_width';
$content_align = in_array( $content_align, array( 'left', 'center', 'right' ), true ) ? $content_align : 'left';
$cta_options   = in_array( $cta_options, array( 'link', 'popup' ), true ) ? $cta_options : 'link';

$image_url = '';
$image_alt = '';

if ( is_array( $background_image ) ) {
	$image_url = isset( $background_image['url'] ) && is_string( $background_image['url'] ) ? trim( $background_image['url'] ) : '';
	$image_alt = isset( $background_image['alt'] ) && is_string( $background_image['alt'] ) ? trim( $background_image['alt'] ) : '';
} elseif ( is_string( $background_image ) ) {
	$image_url = trim( $background_image );
}

$link_url    = '';
$link_title  = '';
$link_target = '_self';

if ( is_array( $link ) ) {
	$link_url    = isset( $link['url'] ) && is_string( $link['url'] ) ? trim( $link['url'] ) : '';
	$link_title  = isset( $link['title'] ) && is_string( $link['title'] ) ? trim( $link['title'] ) : '';
	$link_target = isset( $link['target'] ) && is_string( $link['target'] ) ? trim( $link['target'] ) : '_self';
}

$link_target = in_array( $link_target, array( '_self', '_blank' ), true ) ? $link_target : '_self';

$has_link_cta  = 'link' === $cta_options && '' !== $link_url && '' !== $link_title;
$has_popup_cta = 'popup' === $cta_options && '' !== $popup_class;

if ( '' === $image_url && '' === $eyebrow_text && '' === $headline && '' === $copy && ! $has_link_cta && ! $has_popup_cta ) {
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
	'apache-flex--media-hero',
	'media-hero',
	'apache-flex--media-hero--layout-' . str_replace( '_', '-', $layout_width ),
	'apache-flex--media-hero--align-' . $content_align,
);

if ( $parallax ) {
	$classes[] = 'apache-flex--media-hero--parallax';
}

$styles = array();

if ( $image_url ) {
	$styles[] = '--apache-media-hero-image: url("' . esc_url( $image_url ) . '")';
}

$is_first_module = function_exists( 'get_row_index' ) && 1 === (int) get_row_index();
$hero_headline   = '';

if ( function_exists( 'apache_2026_get_hero_data' ) ) {
	$hero_data     = apache_2026_get_hero_data();
	$hero_headline = isset( $hero_data['title'] ) && is_string( $hero_data['title'] ) ? trim( $hero_data['title'] ) : '';
}

$headline_tag = ( $is_first_module && '' === $hero_headline ) ? 'h1' : 'h2';
?>

<section
	<?php if ( $section_id ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
	<?php if ( $styles ) : ?>
		style="<?php echo esc_attr( implode( '; ', $styles ) ); ?>"
	<?php endif; ?>
>
	<?php if ( $image_url && $image_alt ) : ?>
		<div class="screen-reader-text"><?php echo esc_html( $image_alt ); ?></div>
	<?php endif; ?>

	<div class="apache-flex__inner apache-flex--media-hero__inner">
		<div class="apache-flex--media-hero__content">
			<?php if ( $eyebrow_text ) : ?>
				<p class="apache-flex--media-hero__eyebrow"><?php echo esc_html( $eyebrow_text ); ?></p>
			<?php endif; ?>

			<?php if ( $headline ) : ?>
				<<?php echo esc_attr( $headline_tag ); ?> class="apache-flex--media-hero__headline">
					<?php echo esc_html( $headline ); ?>
				</<?php echo esc_attr( $headline_tag ); ?>>
			<?php endif; ?>

			<?php if ( $copy ) : ?>
				<div class="apache-wysiwyg apache-flex--media-hero__copy">
					<?php echo apache_2026_kses_wysiwyg_content( $copy ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $has_link_cta || $has_popup_cta ) : ?>
				<div class="apache-flex--media-hero__cta">
					<?php if ( $has_link_cta ) : ?>
						<a
							class="btn btn-primary white"
							href="<?php echo esc_url( $link_url ); ?>"
							target="<?php echo esc_attr( $link_target ); ?>"
							<?php if ( '_blank' === $link_target ) : ?>
								rel="noopener noreferrer"
							<?php endif; ?>
						>
							<?php echo esc_html( $link_title ); ?>
						</a>
					<?php else : ?>
						<button
							type="button"
							class="<?php echo esc_attr( trim( 'btn btn-icon play ' . $popup_class ) ); ?>"
						>
							<span class="screen-reader-text"><?php esc_html_e( 'Open popup', 'apache-2026' ); ?></span>
						</button>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
