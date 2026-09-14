<?php
/**
 * Global home map.
 *
 * @package Apache_2026
 */

$data = isset( $args['data'] ) && is_array( $args['data'] ) ? $args['data'] : apache_2026_get_home_map_data();

if ( ! apache_2026_has_home_map_data( $data ) ) {
	return;
}

$margin_top = isset( $data['margin_top'] ) ? apache_2026_sanitize_css_px_value( $data['margin_top'], 0, 600 ) : null;
$header_content = isset( $data['header_content'] ) && is_string( $data['header_content'] ) ? trim( $data['header_content'] ) : '';
$footer_title = isset( $data['footer_title'] ) && is_string( $data['footer_title'] ) ? trim( $data['footer_title'] ) : '';
$footer_cta = isset( $data['footer_cta'] ) && is_array( $data['footer_cta'] ) ? $data['footer_cta'] : array();
$locations  = isset( $data['locations'] ) && is_array( $data['locations'] ) ? $data['locations'] : array();
$classes    = array(
	'apache-flex',
	'apache-flex--home-map',
	'home-map',
);

if ( null !== $margin_top ) {
	$classes[] = 'apache-flex--has-custom-margin';
}

$styles = array();

if ( null !== $margin_top ) {
	$styles[] = '--apache-flex-margin-top: ' . $margin_top . 'px';
}
?>

<section
	class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
	data-home-map
	data-locations="<?php echo esc_attr( wp_json_encode( $locations ) ); ?>"
	<?php if ( $styles ) : ?>
		style="<?php echo esc_attr( implode( '; ', $styles ) ); ?>"
	<?php endif; ?>
>
	<div class="home-map__inner">
		<?php if ( '' !== $header_content ) : ?>
			<div class="home-map__header">
				<?php echo apache_2026_kses_wysiwyg_content( $header_content ); ?>
			</div>
		<?php endif; ?>

		<div class="home-map__media">
			<?php get_template_part( 'template-parts/svg/world-map' ); ?>
		</div>

		<div class="home-map__content">
			<?php if ( '' !== $footer_title ) : ?>
				<h2 class="h2"><?php echo esc_html( $footer_title ); ?></h2>
			<?php else : ?>
				<h2 class="h2">Where we operate</h2>
			<?php endif; ?>

			<?php if ( ! empty( $footer_cta['url'] ) && ! empty( $footer_cta['title'] ) ) : ?>
				<a
					href="<?php echo esc_url( $footer_cta['url'] ); ?>"
					class="btn btn-more"
					target="<?php echo esc_attr( $footer_cta['target'] ?? '_self' ); ?>"
					<?php if ( '_blank' === ( $footer_cta['target'] ?? '_self' ) ) : ?>
						rel="noopener noreferrer"
					<?php endif; ?>
				>
					<?php echo esc_html( $footer_cta['title'] ); ?>
				</a>
			<?php else : ?>
				<a href="/portfolio/" class="btn btn-more">Learn More</a>
			<?php endif; ?>
		</div>

		<div class="home-map__tooltip" data-map-tooltip hidden>
			<div class="home-map__tooltip-inner">
				<p class="home-map__tooltip-caption" data-map-tooltip-caption></p>
			</div>
		</div>
	</div>
</section>
