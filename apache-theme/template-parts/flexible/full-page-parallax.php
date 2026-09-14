<?php
/**
 * Flexible layout: Full Page Parallax.
 *
 * ACF layout key: full_page_parallax
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$background_image = get_sub_field( 'background_image' );
$eyebrow_text     = get_sub_field( 'eyebrow_text' );
$headline         = get_sub_field( 'headline' );
$content          = get_sub_field( 'content' );

$eyebrow_text = is_string( $eyebrow_text ) ? trim( $eyebrow_text ) : '';
$headline     = is_string( $headline ) ? trim( $headline ) : '';
$content      = is_string( $content ) ? trim( $content ) : '';

$image_url = '';
$image_alt = '';

if ( is_array( $background_image ) ) {
	$image_url = isset( $background_image['url'] ) && is_string( $background_image['url'] ) ? trim( $background_image['url'] ) : '';
	$image_alt = isset( $background_image['alt'] ) && is_string( $background_image['alt'] ) ? trim( $background_image['alt'] ) : '';
} elseif ( is_string( $background_image ) ) {
	$image_url = trim( $background_image );
}

if ( '' === $image_url && '' === $eyebrow_text && '' === $headline && '' === $content ) {
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
	'apache-flex--full-page-parallax',
	'full-page-parallax',
);

$styles = array();

if ( $image_url ) {
	$styles[] = '--apache-full-page-parallax-image: url("' . esc_url( $image_url ) . '")';
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

	<div class="apache-flex__inner apache-flex--full-page-parallax__inner">
		<div class="apache-flex--full-page-parallax__content">
			<?php if ( $eyebrow_text ) : ?>
				<p class="apache-flex--full-page-parallax__eyebrow"><?php echo esc_html( $eyebrow_text ); ?></p>
			<?php endif; ?>

			<?php if ( $headline ) : ?>
				<<?php echo esc_attr( $headline_tag ); ?> class="apache-flex--full-page-parallax__headline"><?php echo esc_html( $headline ); ?></<?php echo esc_attr( $headline_tag ); ?>>
			<?php endif; ?>

			<?php if ( $content ) : ?>
				<div class="apache-flex--full-page-parallax__copy">
					<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
