<?php
/**
 * Features List module helpers and ACF registration.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_get_features_list_flexible_layout_definition(): array {
	return array(
		'key'        => 'layout_apache_2026_features_list',
		'name'       => 'features_list',
		'label'      => 'Features List',
		'display'    => 'block',
		'sub_fields' => array(
			apache_2026_flexible_section_header_sub_field( 'field_6a26f775aa276' ),
			array(
				'key'               => 'field_apache_2026_features_list_title',
				'label'             => 'Title',
				'name'              => 'title',
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
				'key'               => 'field_6a29_features_list_remove_padding',
				'label'             => 'Remove Top Padding',
				'name'              => 'remove_padding',
				'aria-label'        => '',
				'type'              => 'true_false',
				'instructions'      => 'Remove the top padding for this module.',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'message'           => '',
				'default_value'     => 0,
				'allow_in_bindings' => 0,
				'ui_on_text'        => '',
				'ui_off_text'       => '',
				'ui'                => 1,
			),
			array(
				'key'               => 'field_apache_2026_features_list_copy',
				'label'             => 'Copy',
				'name'              => 'copy',
				'aria-label'        => '',
				'type'              => 'wysiwyg',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value' => '',
				'allow_in_bindings' => 0,
				'tabs'          => 'all',
				'toolbar'       => 'full',
				'media_upload'  => 1,
				'delay'         => 0,
			),
			array(
				'key'               => 'field_apache_2026_features_list_items',
				'label'             => 'Features',
				'name'              => 'features',
				'aria-label'        => '',
				'type'              => 'repeater',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'layout'        => 'block',
				'pagination'    => 0,
				'min'           => 0,
				'max'           => 0,
				'collapsed'     => 'field_apache_2026_features_list_item_title',
				'button_label'  => 'Add Feature',
				'rows_per_page' => 20,
				'sub_fields'    => array(
					array(
						'key'               => 'field_apache_2026_features_list_item_title',
						'label'             => 'Feature Title',
						'name'              => 'feature_title',
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
						'parent_repeater' => 'field_apache_2026_features_list_items',
					),
					array(
						'key'               => 'field_apache_2026_features_list_item_copy',
						'label'             => 'Feature Copy',
						'name'              => 'feature_copy',
						'aria-label'        => '',
						'type'              => 'wysiwyg',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'default_value' => '',
						'allow_in_bindings' => 0,
						'tabs'          => 'all',
						'toolbar'       => 'full',
						'media_upload'  => 1,
						'delay'         => 0,
						'parent_repeater' => 'field_apache_2026_features_list_items',
					),
				),
			),
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

function apache_2026_normalize_features_list_flexible_layout( array $layout ): array {
	$canonical = apache_2026_get_features_list_flexible_layout_definition();

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

function apache_2026_register_features_list_flexible_layout( array $field ): array {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}

	foreach ( $field['layouts'] as $layout_key => $layout ) {
		if ( isset( $layout['name'] ) && 'features_list' === $layout['name'] ) {
			$layout = apache_2026_insert_layout_sub_field_after_key(
				$layout,
				apache_2026_flexible_remove_top_padding_sub_field( 'field_6a29_features_list_remove_padding' ),
				'field_apache_2026_features_list_title'
			);

			if ( apache_2026_acf_fields_are_structurally_valid( $layout['sub_fields'] ?? null ) ) {
				$field['layouts'][ $layout_key ] = $layout;
				return $field;
			}

			if ( apache_2026_is_acf_field_group_editor_screen() ) {
				if ( wp_doing_ajax() ) {
					return $field;
				}

				$field['layouts'][ $layout_key ] = apache_2026_get_features_list_flexible_layout_definition();
				return $field;
			}

			$field['layouts'][ $layout_key ] = apache_2026_normalize_features_list_flexible_layout( $layout );
			return $field;
		}
	}

	$field['layouts']['layout_apache_2026_features_list'] = apache_2026_get_features_list_flexible_layout_definition();

	return $field;
}
add_filter( 'acf/load_field/key=field_5e15ed5e87e67', 'apache_2026_register_features_list_flexible_layout' );
