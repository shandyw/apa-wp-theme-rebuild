<?php
/**
 * Global popup renderer.
 *
 * @package Apache_2026
 */

$popup_ids = isset( $args['popup_ids'] ) && is_array( $args['popup_ids'] ) ? array_map( 'absint', $args['popup_ids'] ) : array();

if ( empty( $popup_ids ) ) {
	return;
}

foreach ( $popup_ids as $popup_id ) {
	if ( $popup_id <= 0 ) {
		continue;
	}

	get_template_part(
		'template-parts/popups/popup',
		null,
		array(
			'popup_id' => $popup_id,
		)
	);
}
