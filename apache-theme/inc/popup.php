<?php
/**
 * Popup helpers, ACF fields, rendering, and asset loading.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const APACHE_2026_POPUP_ENABLED_FIELD_KEY         = 'field_apache_2026_popup_enabled';
const APACHE_2026_POPUP_SHOW_TITLE_FIELD_KEY      = 'field_apache_2026_popup_show_title';
const APACHE_2026_POPUP_TRIGGER_CLASS_FIELD_KEY   = 'field_apache_2026_popup_trigger_css_class';
const APACHE_2026_POPUP_AUTO_OPEN_FIELD_KEY       = 'field_apache_2026_popup_enable_auto_open';
const APACHE_2026_POPUP_AUTO_OPEN_PAGES_FIELD_KEY = 'field_apache_2026_popup_auto_open_pages';
const APACHE_2026_POPUP_AUTO_DELAY_FIELD_KEY      = 'field_apache_2026_popup_auto_open_delay';

function apache_2026_register_popup_acf_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_apache_2026_popup_settings',
			'title'                 => __( 'Popup Settings', 'apache-2026' ),
			'fields'                => array(
				array(
					'key'           => APACHE_2026_POPUP_ENABLED_FIELD_KEY,
					'label'         => __( 'Enabled', 'apache-2026' ),
					'name'          => 'popup_enabled',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui'            => 1,
				),
				array(
					'key'       => 'field_apache_2026_popup_display_settings_tab',
					'label'     => __( 'Display Settings', 'apache-2026' ),
					'name'      => '',
					'type'      => 'tab',
					'placement' => 'top',
				),
				array(
					'key'           => APACHE_2026_POPUP_SHOW_TITLE_FIELD_KEY,
					'label'         => __( 'Show Popup Title', 'apache-2026' ),
					'name'          => 'show_popup_title',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui'            => 1,
				),
				array(
					'key'           => APACHE_2026_POPUP_TRIGGER_CLASS_FIELD_KEY,
					'label'         => __( 'Trigger CSS Class', 'apache-2026' ),
					'name'          => 'trigger_css_class',
					'type'          => 'text',
					'instructions'  => __( 'Add this CSS class to any button, link, or element to open the popup.', 'apache-2026' ),
					'default_value' => '',
				),
				array(
					'key'       => 'field_apache_2026_popup_trigger_settings_tab',
					'label'     => __( 'Trigger Settings', 'apache-2026' ),
					'name'      => '',
					'type'      => 'tab',
					'placement' => 'top',
				),
				array(
					'key'           => APACHE_2026_POPUP_AUTO_OPEN_FIELD_KEY,
					'label'         => __( 'Enable Auto Open', 'apache-2026' ),
					'name'          => 'enable_auto_open',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui'            => 1,
				),
				array(
					'key'               => APACHE_2026_POPUP_AUTO_OPEN_PAGES_FIELD_KEY,
					'label'             => __( 'Auto Open Pages / Posts', 'apache-2026' ),
					'name'              => 'auto_open_pages',
					'type'              => 'post_object',
					'instructions'      => __( 'Choose the pages or posts where this popup should open automatically.', 'apache-2026' ),
					'post_type'         => array( 'page', 'post' ),
					'multiple'          => 1,
					'return_format'     => 'id',
					'ui'                => 1,
					'allow_null'        => 0,
					'conditional_logic' => array(
						array(
							array(
								'field'    => APACHE_2026_POPUP_AUTO_OPEN_FIELD_KEY,
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
				array(
					'key'               => APACHE_2026_POPUP_AUTO_DELAY_FIELD_KEY,
					'label'             => __( 'Auto Open Delay (ms)', 'apache-2026' ),
					'name'              => 'auto_open_delay',
					'type'              => 'number',
					'default_value'     => 500,
					'min'               => 0,
					'step'              => 50,
					'conditional_logic' => array(
						array(
							array(
								'field'    => APACHE_2026_POPUP_AUTO_OPEN_FIELD_KEY,
								'operator' => '==',
								'value'    => '1',
							),
						),
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'apache_popup',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => 0,
			'acfe'                  => array(
				'autosync' => array( 'json' ),
			),
		)
	);
}
add_action( 'acf/init', 'apache_2026_register_popup_acf_fields' );

function apache_2026_add_popup_location_to_blocks_field_group( array $field_group ): array {
	if ( empty( $field_group['key'] ) || 'group_5e15ed52211c4' !== $field_group['key'] ) {
		return $field_group;
	}

	$popup_location = array(
		array(
			'param'    => 'post_type',
			'operator' => '==',
			'value'    => 'apache_popup',
		),
	);

	$locations = isset( $field_group['location'] ) && is_array( $field_group['location'] )
		? $field_group['location']
		: array();

	foreach ( $locations as $location_group ) {
		if ( ! is_array( $location_group ) ) {
			continue;
		}

		foreach ( $location_group as $rule ) {
			if (
				is_array( $rule ) &&
				isset( $rule['param'], $rule['operator'], $rule['value'] ) &&
				'post_type' === $rule['param'] &&
				'==' === $rule['operator'] &&
				'apache_popup' === $rule['value']
			) {
				return $field_group;
			}
		}
	}

	$field_group['location'][] = $popup_location;

	return $field_group;
}
add_filter( 'acf/load_field_group/key=group_5e15ed52211c4', 'apache_2026_add_popup_location_to_blocks_field_group' );

function apache_2026_get_popup_trigger_class( int $popup_id ): string {
	$popup_id = absint( $popup_id );

	if ( $popup_id <= 0 ) {
		return '';
	}

	$post = get_post( $popup_id );

	if ( ! $post instanceof WP_Post || 'apache_popup' !== $post->post_type ) {
		return '';
	}

	$slug = sanitize_title( $post->post_name );

	if ( '' === $slug ) {
		$slug = (string) $popup_id;
	}

	return 'popup-trigger-' . $slug;
}

function apache_2026_sync_popup_trigger_class( int $post_id ): void {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( 'apache_popup' !== get_post_type( $post_id ) ) {
		return;
	}

	$trigger_class = apache_2026_get_popup_trigger_class( $post_id );

	if ( '' === $trigger_class ) {
		return;
	}

	update_post_meta( $post_id, 'trigger_css_class', $trigger_class );
	update_post_meta( $post_id, '_trigger_css_class', APACHE_2026_POPUP_TRIGGER_CLASS_FIELD_KEY );
}
add_action( 'save_post_apache_popup', 'apache_2026_sync_popup_trigger_class' );

function apache_2026_sync_popup_enabled_meta( mixed $post_id ): void {
	$post_id = is_numeric( $post_id ) ? absint( $post_id ) : 0;

	if ( $post_id <= 0 || 'apache_popup' !== get_post_type( $post_id ) ) {
		return;
	}

	$enabled = null;

	if (
		isset( $_POST['acf'] ) &&
		is_array( $_POST['acf'] ) &&
		array_key_exists( APACHE_2026_POPUP_ENABLED_FIELD_KEY, $_POST['acf'] )
	) {
		$enabled = wp_unslash( $_POST['acf'][ APACHE_2026_POPUP_ENABLED_FIELD_KEY ] );
	}

	if ( null === $enabled ) {
		$enabled = get_post_meta( $post_id, 'popup_enabled', true );
	}

	if ( '' === $enabled && function_exists( 'get_field' ) ) {
		$enabled = get_field( 'popup_enabled', $post_id );
	}

	$normalized = apache_2026_bool_from_mixed( $enabled ) ? '1' : '0';

	update_post_meta( $post_id, 'popup_enabled', $normalized );
	update_post_meta( $post_id, '_popup_enabled', APACHE_2026_POPUP_ENABLED_FIELD_KEY );
}
add_action( 'acf/save_post', 'apache_2026_sync_popup_enabled_meta', 20 );
add_action( 'save_post_apache_popup', 'apache_2026_sync_popup_enabled_meta', 20 );

function apache_2026_update_popup_enabled_value( mixed $value, int|string $post_id, array $field ): mixed {
	$normalized = apache_2026_bool_from_mixed( $value ) ? '1' : '0';
	$post_id    = is_numeric( $post_id ) ? absint( $post_id ) : 0;

	if ( $post_id > 0 && 'apache_popup' === get_post_type( $post_id ) ) {
		update_post_meta( $post_id, 'popup_enabled', $normalized );
		update_post_meta( $post_id, '_popup_enabled', APACHE_2026_POPUP_ENABLED_FIELD_KEY );
	}

	return $normalized;
}
add_filter( 'acf/update_value/name=popup_enabled', 'apache_2026_update_popup_enabled_value', 20, 3 );
add_filter( 'acf/update_value/key=field_apache_2026_popup_enabled', 'apache_2026_update_popup_enabled_value', 20, 3 );

function apache_2026_prepare_popup_trigger_class_field( array $field ): array {
	$post_id = get_the_ID();

	if ( $post_id > 0 ) {
		$field['value'] = apache_2026_get_popup_trigger_class( $post_id );
	}

	$field['readonly'] = 1;

	return $field;
}
add_filter( 'acf/prepare_field/name=trigger_css_class', 'apache_2026_prepare_popup_trigger_class_field' );

function apache_2026_is_popup_enabled( int $popup_id ): bool {
	$value = get_post_meta( $popup_id, 'popup_enabled', true );

	if ( '' === $value && function_exists( 'get_field' ) ) {
		$value = get_field( 'popup_enabled', $popup_id );
	}

	return apache_2026_bool_from_mixed( $value );
}

function apache_2026_should_show_popup_title( int $popup_id ): bool {
	$value = function_exists( 'get_field' )
		? get_field( 'show_popup_title', $popup_id )
		: get_post_meta( $popup_id, 'show_popup_title', true );

	return apache_2026_bool_from_mixed( $value );
}

function apache_2026_get_popup_auto_open_targets( int $popup_id ): array {
	$value = function_exists( 'get_field' ) ? get_field( 'auto_open_pages', $popup_id ) : get_post_meta( $popup_id, 'auto_open_pages', true );

	if ( ! is_array( $value ) ) {
		$value = array( $value );
	}

	$targets = array();

	foreach ( $value as $item ) {
		if ( $item instanceof WP_Post ) {
			$targets[] = (int) $item->ID;
		} elseif ( is_array( $item ) && isset( $item['ID'] ) ) {
			$targets[] = absint( $item['ID'] );
		} elseif ( is_numeric( $item ) ) {
			$targets[] = absint( $item );
		}
	}

	return array_values( array_unique( array_filter( $targets ) ) );
}

function apache_2026_should_popup_auto_open( int $popup_id ): bool {
	$enabled = function_exists( 'get_field' ) ? get_field( 'enable_auto_open', $popup_id ) : get_post_meta( $popup_id, 'enable_auto_open', true );

	if ( ! apache_2026_bool_from_mixed( $enabled ) ) {
		return false;
	}

	$current_id = get_queried_object_id();

	if ( $current_id <= 0 ) {
		return false;
	}

	return in_array( $current_id, apache_2026_get_popup_auto_open_targets( $popup_id ), true );
}

function apache_2026_get_popup_auto_open_delay( int $popup_id ): int {
	$value = function_exists( 'get_field' ) ? get_field( 'auto_open_delay', $popup_id ) : get_post_meta( $popup_id, 'auto_open_delay', true );

	return max( 0, absint( is_scalar( $value ) ? (string) $value : '' ) );
}

function apache_2026_get_enabled_popups(): array {
	static $popup_ids = null;

	if ( null !== $popup_ids ) {
		return $popup_ids;
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'apache_popup',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => array(
				'title' => 'ASC',
				'date'  => 'ASC',
			),
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => array(
				array(
					'key'   => 'popup_enabled',
					'value' => '1',
				),
			),
		)
	);

	$popup_ids = array_map( 'absint', $query->posts );

	return $popup_ids;
}

function apache_2026_has_enabled_popups(): bool {
	return ! empty( apache_2026_get_enabled_popups() );
}

function apache_2026_enqueue_popup_assets(): void {
	if ( ! apache_2026_has_enabled_popups() ) {
		return;
	}

	$popup_asset = apache_2026_vite_asset( 'src/js/popup-main.js' );

	if ( ! $popup_asset ) {
		return;
	}

	$style_deps = array();

	if ( wp_style_is( 'apache-2026-global', 'registered' ) || wp_style_is( 'apache-2026-global', 'enqueued' ) ) {
		$style_deps[] = 'apache-2026-global';
	} elseif ( wp_style_is( 'apache-2026-style', 'registered' ) || wp_style_is( 'apache-2026-style', 'enqueued' ) ) {
		$style_deps[] = 'apache-2026-style';
	}

	foreach ( $popup_asset['css'] as $index => $css_asset ) {
		wp_enqueue_style(
			'apache-2026-popup-' . $index,
			$css_asset['uri'],
			$style_deps,
			apache_2026_asset_version( $css_asset['path'] )
		);
	}

	wp_enqueue_script(
		'apache-2026-popup',
		$popup_asset['uri'],
		array( 'apache-2026-main' ),
		apache_2026_asset_version( $popup_asset['path'] ),
		array( 'in_footer' => true )
	);
}
add_action( 'wp_enqueue_scripts', 'apache_2026_enqueue_popup_assets', 20 );

function apache_2026_render_popups(): void {
	$popup_ids = apache_2026_get_enabled_popups();

	if ( empty( $popup_ids ) ) {
		return;
	}

	get_template_part(
		'template-parts/popups/render-popups',
		null,
		array(
			'popup_ids' => $popup_ids,
		)
	);
}
add_action( 'wp_footer', 'apache_2026_render_popups', 20 );
