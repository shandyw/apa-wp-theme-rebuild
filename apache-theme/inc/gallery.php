<?php
/**
 * Gallery module helpers and ACF registration.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_get_gallery_post_id( mixed $gallery_value ): int {
	if ( $gallery_value instanceof WP_Post ) {
		return 'gallery' === $gallery_value->post_type ? (int) $gallery_value->ID : 0;
	}

	if ( is_numeric( $gallery_value ) ) {
		$gallery_id = absint( $gallery_value );
		$gallery    = get_post( $gallery_id );

		return $gallery instanceof WP_Post && 'gallery' === $gallery->post_type ? $gallery_id : 0;
	}

	if ( is_array( $gallery_value ) && isset( $gallery_value['ID'] ) ) {
		return apache_2026_get_gallery_post_id( $gallery_value['ID'] );
	}

	return 0;
}

function apache_2026_normalize_gallery_columns( mixed $value, int $fallback = 3 ): int {
	$columns = absint( is_scalar( $value ) ? (string) $value : '' );

	if ( $columns < 2 || $columns > 4 ) {
		return max( 2, min( 4, $fallback ) );
	}

	return $columns;
}

function apache_2026_normalize_gallery_layout( mixed $value, string $fallback = 'grid' ): string {
	$value = is_string( $value ) ? trim( $value ) : '';

	if ( in_array( $value, array( 'grid', 'carousel' ), true ) ) {
		return $value;
	}

	return in_array( $fallback, array( 'grid', 'carousel' ), true ) ? $fallback : 'grid';
}

function apache_2026_normalize_gallery_images( mixed $images ): array {
	if ( ! is_array( $images ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $images as $image ) {
		$image_id = 0;

		if ( is_array( $image ) && isset( $image['ID'] ) ) {
			$image_id = absint( $image['ID'] );
		} elseif ( is_numeric( $image ) ) {
			$image_id = absint( $image );
		}

		if ( $image_id <= 0 || ! wp_attachment_is_image( $image_id ) ) {
			continue;
		}

		$normalized[] = $image_id;
	}

	return array_values( array_unique( $normalized ) );
}

function apache_2026_get_gallery_download_file( mixed $file_value ): array {
	$file_id = 0;

	if ( is_array( $file_value ) && isset( $file_value['ID'] ) ) {
		$file_id = absint( $file_value['ID'] );
	} elseif ( is_numeric( $file_value ) ) {
		$file_id = absint( $file_value );
	}

	if ( $file_id <= 0 || 'attachment' !== get_post_type( $file_id ) ) {
		return array();
	}

	$url = wp_get_attachment_url( $file_id );

	if ( ! is_string( $url ) || '' === $url ) {
		return array();
	}

	$url = trim( $url );

	if ( ! wp_http_validate_url( $url ) ) {
		return array();
	}

	return array(
		'id'    => $file_id,
		'url'   => $url,
		'title' => get_the_title( $file_id ),
	);
}

function apache_2026_get_gallery_attachment_file( int $attachment_id ): array {
	$attachment_id = absint( $attachment_id );

	if ( $attachment_id <= 0 || 'attachment' !== get_post_type( $attachment_id ) ) {
		return array();
	}

	$url = wp_get_attachment_url( $attachment_id );

	if ( ! is_string( $url ) || '' === $url ) {
		return array();
	}

	$url = trim( $url );

	if ( ! wp_http_validate_url( $url ) ) {
		return array();
	}

	return array(
		'id'    => $attachment_id,
		'url'   => $url,
		'title' => get_the_title( $attachment_id ),
	);
}

function apache_2026_should_add_download_attribute( string $url ): bool {
	$url_host  = wp_parse_url( $url, PHP_URL_HOST );
	$site_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

	if ( ! is_string( $url_host ) || ! is_string( $site_host ) || '' === $url_host || '' === $site_host ) {
		return false;
	}

	return strtolower( $url_host ) === strtolower( $site_host );
}

function apache_2026_get_gallery_flexible_layout_definition(): array {
	return array(
		'key'        => 'layout_apache_2026_gallery_module',
		'name'       => 'gallery_module',
		'label'      => 'Gallery',
		'display'    => 'block',
		'sub_fields' => array(
			apache_2026_flexible_section_header_sub_field( 'field_apache_2026_gallery_module' ),
			array(
				'key'               => 'field_apache_2026_gallery_module_title',
				'label'             => 'Module Title',
				'name'              => 'module_title',
				'aria-label'        => '',
				'type'              => 'text',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value' => '',
				'maxlength'     => '',
				'placeholder'   => '',
				'prepend'       => '',
				'append'        => '',
			),
			array(
				'key'               => 'field_apache_2026_gallery_module_selected_gallery',
				'label'             => 'Selected Gallery',
				'name'              => 'selected_gallery',
				'aria-label'        => '',
				'type'              => 'post_object',
				'instructions'      => 'Select the Gallery to display.',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'post_type'         => array( 'gallery' ),
				'taxonomy'          => '',
				'allow_null'        => 0,
				'multiple'          => 0,
				'return_format'     => 'object',
				'ui'                => 1,
			),
			array(
				'key'               => 'field_apache_2026_gallery_module_display_style',
				'label'             => 'Display Style',
				'name'              => 'display_style',
				'aria-label'        => '',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices' => array(
					'default'  => 'Default',
					'grid'     => 'Grid',
					'carousel' => 'Carousel',
				),
				'default_value'      => 'default',
				'allow_null'         => 0,
				'multiple'           => 0,
				'ui'                 => 0,
				'return_format'      => 'value',
				'ajax'               => 0,
				'placeholder'        => '',
				'create_options'     => 0,
				'save_options'       => 0,
				'allow_custom'       => 0,
				'search_placeholder' => '',
			),
			array(
				'key'               => 'field_apache_2026_gallery_module_columns',
				'label'             => 'Columns',
				'name'              => 'columns',
				'aria-label'        => '',
				'type'              => 'select',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices' => array(
					'default' => 'Default',
					'2'       => '2',
					'3'       => '3',
					'4'       => '4',
				),
				'default_value'      => 'default',
				'allow_null'         => 0,
				'multiple'           => 0,
				'ui'                 => 0,
				'return_format'      => 'value',
				'ajax'               => 0,
				'placeholder'        => '',
				'create_options'     => 0,
				'save_options'       => 0,
				'allow_custom'       => 0,
				'search_placeholder' => '',
			),
			array(
				'key'               => 'field_apache_2026_gallery_module_show_captions',
				'label'             => 'Show Captions',
				'name'              => 'show_captions',
				'aria-label'        => '',
				'type'              => 'true_false',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'message'           => '',
				'default_value'     => 1,
				'allow_in_bindings' => 0,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
				'ui'                => 1,
			),
			apache_2026_flexible_section_id_sub_field( 'field_apache_2026_gallery_module' ),
		),
		'min'                           => '',
		'max'                           => '',
		'acfe_flexible_thumbnail'       => '596',
		'acfe_flexible_render_template' => false,
		'acfe_flexible_render_style'    => false,
		'acfe_flexible_render_script'   => false,
		'acfe_flexible_settings'        => false,
		'acfe_flexible_settings_size'   => 'medium',
		'acfe_flexible_modal_edit_size' => false,
		'acfe_flexible_category'        => false,
	);
}

function apache_2026_normalize_gallery_flexible_layout( array $layout ): array {
	$canonical = apache_2026_get_gallery_flexible_layout_definition();

	if ( isset( $layout['acfe_flexible_thumbnail'] ) && '' !== (string) $layout['acfe_flexible_thumbnail'] ) {
		$canonical['acfe_flexible_thumbnail'] = $layout['acfe_flexible_thumbnail'];
	}

	if ( empty( $layout['sub_fields'] ) || ! is_array( $layout['sub_fields'] ) ) {
		return array_replace( $layout, $canonical );
	}

	$canonical_fields = $canonical['sub_fields'];
	$canonical_keys   = array();

	foreach ( $canonical_fields as $sub_field ) {
		if ( isset( $sub_field['key'] ) && is_string( $sub_field['key'] ) ) {
			$canonical_keys[] = $sub_field['key'];
		}
	}

	$extra_fields = array();

	foreach ( $layout['sub_fields'] as $sub_field ) {
		if ( ! apache_2026_is_valid_acf_field_definition( $sub_field ) ) {
			continue;
		}

		$sub_field_key = isset( $sub_field['key'] ) && is_string( $sub_field['key'] ) ? $sub_field['key'] : '';

		if ( '' !== $sub_field_key && in_array( $sub_field_key, $canonical_keys, true ) ) {
			continue;
		}

		$extra_fields[] = $sub_field;
	}

	$canonical['sub_fields'] = array_merge( $canonical_fields, $extra_fields );

	return array_replace( $layout, $canonical );
}

function apache_2026_register_gallery_flexible_layout( array $field ): array {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}

	foreach ( $field['layouts'] as $layout_key => $layout ) {
		if ( isset( $layout['name'] ) && 'gallery_module' === $layout['name'] ) {
			if (
				apache_2026_is_acf_field_group_editor_screen()
				&& apache_2026_acf_fields_are_structurally_valid( $layout['sub_fields'] ?? null )
			) {
				return $field;
			}

			$field['layouts'][ $layout_key ] = apache_2026_normalize_gallery_flexible_layout( $layout );
			return $field;
		}
	}

	$field['layouts']['layout_apache_2026_gallery_module'] = apache_2026_get_gallery_flexible_layout_definition();

	return $field;
}
add_filter( 'acf/load_field/key=field_5e15ed5e87e67', 'apache_2026_register_gallery_flexible_layout' );
