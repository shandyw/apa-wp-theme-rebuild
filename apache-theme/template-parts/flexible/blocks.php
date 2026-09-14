<?php
/**
 * ACF flexible content renderer.
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'have_rows' ) || ! have_rows( 'blocks' ) ) {
	return;
}

while ( have_rows( 'blocks' ) ) :
	the_row();

	$layout = get_row_layout();

	if ( ! $layout ) {
		continue;
	}

	$template_candidates = function_exists( 'apache_2026_get_flexible_template_candidates' )
		? apache_2026_get_flexible_template_candidates( $layout )
		: array( sanitize_title( str_replace( '_', '-', $layout ) ) );

	foreach ( $template_candidates as $template_candidate ) {
		$template = locate_template( "template-parts/flexible/{$template_candidate}.php" );

		if ( $template ) {
			$layout_options = function_exists( 'apache_2026_get_flexible_layout_options' )
				? apache_2026_get_flexible_layout_options( (string) $layout )
				: array();

			ob_start();
			load_template( $template, false );
			$markup = (string) ob_get_clean();

			if ( function_exists( 'apache_2026_apply_flexible_layout_options' ) ) {
				$markup = apache_2026_apply_flexible_layout_options( $markup, $layout_options );
			}

			echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			break;
		}
	}
endwhile;
