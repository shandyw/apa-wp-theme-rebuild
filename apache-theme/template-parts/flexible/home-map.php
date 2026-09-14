<?php
/**
 * Flexible layout: Home Map.
 *
 * ACF layout key: home_map
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$margin_top = apache_2026_sanitize_css_px_value( get_sub_field( 'margin_top' ), 0, 600 );

$locations     = get_sub_field( 'map_locations' ) ?: array();
$map_locations = array();

foreach ( $locations as $location ) {
	$title        = trim( (string) ( $location['title'] ?? '' ) );
	$caption      = trim( (string) ( $location['caption'] ?? '' ) );
	$region       = strtoupper( trim( (string) ( $location['region'] ?? '' ) ) );
	$raw_lat      = isset( $location['lat'] ) && is_scalar( $location['lat'] ) ? trim( (string) $location['lat'] ) : '';
	$raw_lng      = isset( $location['lng'] ) && is_scalar( $location['lng'] ) ? trim( (string) $location['lng'] ) : '';
	$lat          = is_numeric( $raw_lat ) ? (float) $raw_lat : null;
	$lng          = is_numeric( $raw_lng ) ? (float) $raw_lng : null;
	$link         = $location['url'] ?? '';
	$location_url = '';

	if ( null !== $lat && ( $lat < -90 || $lat > 90 ) ) {
		$lat = null;
	}

	if ( null !== $lng && ( $lng < -180 || $lng > 180 ) ) {
		$lng = null;
	}

	if ( is_numeric( $link ) ) {
		$location_url = get_permalink( (int) $link ) ?: '';
	} elseif ( is_string( $link ) ) {
		$location_url = $link;
	}

	$map_locations[] = array(
		'title'   => $title,
		'caption' => $caption,
		'region'  => $region,
		'lat'     => $lat,
		'lng'     => $lng,
		'url'     => $location_url,
		'target'  => '',
	);
}

$classes = array(
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
	data-locations="<?php echo esc_attr( wp_json_encode( $map_locations ) ); ?>"
	<?php if ( $styles ) : ?>
		style="<?php echo esc_attr( implode( '; ', $styles ) ); ?>"
	<?php endif; ?>
>
	<div class="home-map__inner">
		
	<div class="home-map__content">
          <h2 class="h2">Where we operate</h2>
          <a href="/portfolio/" class="btn btn-more">Learn More</a>
        </div>
		
		
		<div class="home-map__media">
			<?php get_template_part( 'template-parts/svg/world-map' ); ?>
		</div>

		<div class="home-map__tooltip" data-map-tooltip hidden>
			<div class="home-map__tooltip-inner">
			<p class="home-map__tooltip-caption" data-map-tooltip-caption></p>
			</div>
		</div>
	</div>
</section>
