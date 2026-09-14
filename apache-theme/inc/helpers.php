<?php
/**
 * General template helpers.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_section_open( array $args = array() ): void {
    $classes = [
        'ac-section',
        ! empty($args['class']) ? $args['class'] : '',
        ! empty($args['background']) ? 'ac-section--' . sanitize_html_class($args['background']) : '',
    ];

    printf(
        '<section class="%s"%s><div class="ac-section__inner">',
        esc_attr(trim(implode(' ', array_filter($classes)))),
        ! empty($args['id']) ? ' id="' . esc_attr($args['id']) . '"' : ''
    );
}

function apache_2026_section_close(): void {
    echo '</div></section>';
}

function apache_2026_sanitize_css_px_value( mixed $value, int $min = 0, int $max = 400 ): ?int {
	if ( is_array( $value ) || is_object( $value ) || null === $value || '' === $value ) {
		return null;
	}

	$value = trim( (string) $value );

	if ( ! preg_match( '/^-?\d+(?:\.\d+)?(?:px)?$/', $value ) ) {
		return null;
	}

	$value = (int) round( (float) str_replace( 'px', '', $value ) );

	return max( $min, min( $max, $value ) );
}

function apache_2026_get_image_dimensions_from_url( string $url ): array {
	$url = trim( $url );

	if ( '' === $url ) {
		return array(
			'width'  => 0,
			'height' => 0,
		);
	}

	$attachment_id = attachment_url_to_postid( $url );

	if ( $attachment_id <= 0 ) {
		return array(
			'width'  => 0,
			'height' => 0,
		);
	}

	$metadata = wp_get_attachment_metadata( $attachment_id );

	if ( ! is_array( $metadata ) ) {
		return array(
			'width'  => 0,
			'height' => 0,
		);
	}

	return array(
		'width'  => isset( $metadata['width'] ) ? absint( $metadata['width'] ) : 0,
		'height' => isset( $metadata['height'] ) ? absint( $metadata['height'] ) : 0,
	);
}

function apache_2026_bool_from_mixed( mixed $value ): bool {
	if ( is_bool( $value ) ) {
		return $value;
	}

	if ( is_numeric( $value ) ) {
		return 1 === absint( $value );
	}

	if ( is_string( $value ) ) {
		return in_array( strtolower( trim( $value ) ), array( '1', 'true', 'yes', 'on' ), true );
	}

	return false;
}

function apache_2026_allowed_content_html(): array {
	$allowed_html = wp_kses_allowed_html( 'post' );

	$allowed_html['br'] = array();
	$allowed_html['p']  = array(
		'class' => true,
		'id'    => true,
	);
	$allowed_html['code'] = array(
		'class' => true,
	);
	$allowed_html['pre'] = array(
		'class' => true,
	);
	$allowed_html['kbd'] = array(
		'class' => true,
	);
	$allowed_html['samp'] = array(
		'class' => true,
	);

	$allowed_html['iframe'] = array(
		'src'             => true,
		'title'           => true,
		'width'           => true,
		'height'          => true,
		'loading'         => true,
		'allow'           => true,
		'allowfullscreen' => true,
		'frameborder'     => true,
		'referrerpolicy'  => true,
		'class'           => true,
		'id'              => true,
	);

	$allowed_html['video'] = array(
		'class'       => true,
		'controls'    => true,
		'autoplay'    => true,
		'muted'       => true,
		'loop'        => true,
		'playsinline' => true,
		'poster'      => true,
		'preload'     => true,
		'width'       => true,
		'height'      => true,
	);

	$allowed_html['source'] = array(
		'src'  => true,
		'type' => true,
	);

	$allowed_html['track'] = array(
		'kind'    => true,
		'src'     => true,
		'srclang' => true,
		'label'   => true,
		'default' => true,
	);

	return $allowed_html;
}

function apache_2026_kses_content( string $content ): string {
	return wp_kses( $content, apache_2026_allowed_content_html() );
}

function apache_2026_escape_literal_code_tags( string $content ): string {
	return (string) preg_replace_callback(
		'#<(code|pre|kbd|samp)(\b[^>]*)>(.*?)</\1>#is',
		static function ( array $matches ): string {
			$tag_name    = strtolower( $matches[1] );
			$attributes  = isset( $matches[2] ) ? (string) $matches[2] : '';
			$inner_html  = isset( $matches[3] ) ? (string) $matches[3] : '';
			$escaped_html = esc_html( html_entity_decode( $inner_html, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

			return sprintf( '<%1$s%2$s>%3$s</%1$s>', $tag_name, $attributes, $escaped_html );
		},
		$content
	);
}

function apache_2026_preserve_literal_code_tags( string $content, array &$placeholders ): string {
	$placeholders = array();

	return (string) preg_replace_callback(
		'#<(code|pre|kbd|samp)\b[^>]*>.*?</\1>#is',
		static function ( array $matches ) use ( &$placeholders ): string {
			$token = '%%APACHE_LITERAL_CODE_' . count( $placeholders ) . '%%';
			$placeholders[ $token ] = $matches[0];

			return $token;
		},
		$content
	);
}

function apache_2026_kses_wysiwyg_content( string $content ): string {
	$content = trim( $content );

	if ( '' === $content ) {
		return '';
	}

	$has_block_html = (bool) preg_match( '#<(?:p|div|ul|ol|li|blockquote|h[1-6]|table|thead|tbody|tr|td|th|figure|figcaption|hr|pre)\b#i', $content );

	if ( ! $has_block_html ) {
		$content = wpautop( $content );
	}

	// Remove auto paragraph wrappers around standalone links/buttons.
	$content = (string) preg_replace(
		'#<p\b[^>]*>\s*((?:<a\b[^>]*>.*?</a>\s*)+)</p>#is',
		'$1',
		$content
	);

	$content = apache_2026_escape_literal_code_tags( $content );
	$content = apache_2026_kses_content( $content );

	if ( preg_match( '/' . get_shortcode_regex() . '/', $content ) ) {
		$literal_code_placeholders = array();
		$content                   = apache_2026_preserve_literal_code_tags( $content, $literal_code_placeholders );
		$content                   = do_shortcode( shortcode_unautop( $content ) );

		if ( ! empty( $literal_code_placeholders ) ) {
			$content = strtr( $content, $literal_code_placeholders );
		}
	}

	return $content;
}

function apache_2026_normalize_wysiwyg_markup( string $content ): string {
	$content = trim( $content );

	if ( '' === $content ) {
		return '';
	}

	return trim(
		(string) preg_replace(
			'#<p\b[^>]*>\s*((?:<a\b[^>]*>.*?</a>\s*)+)</p>#is',
			'$1',
			$content
		)
	);
}

function apache_2026_get_flexible_layout_options( string $layout = '' ): array {
	$options = array(
		'classes' => array(),
	);

	if ( ! function_exists( 'get_sub_field' ) ) {
		return $options;
	}

	$layout = trim( $layout );

	if ( in_array( $layout, array( 'features_list', 'stats', 'callout_box' ), true ) ) {
		return $options;
	}

	foreach ( array( 'remove_section_padding', 'remove_padding', 'remove_section_spacing' ) as $field_name ) {
		if ( apache_2026_bool_from_mixed( get_sub_field( $field_name ) ) ) {
			$options['classes'][] = 'apache-flex--no-section-padding';
			break;
		}
	}

	return $options;
}

function apache_2026_flexible_section_header_sub_field( string $key_prefix ): array {
	return array(
		'key'               => $key_prefix . '_section_header',
		'label'             => 'Section Header',
		'name'              => 'section_header',
		'aria-label'        => '',
		'type'              => 'group',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => 0,
		'wrapper'           => array(
			'width' => '',
			'class' => '',
			'id'    => '',
		),
		'layout'               => 'block',
		'acfe_seemless_style'  => 0,
		'acfe_group_modal'     => 0,
		'acfe_seamless_style'  => 0,
		'acfe_group_modal_close' => 0,
		'acfe_group_modal_button' => '',
		'acfe_group_modal_size'   => 'large',
		'sub_fields' => array(
			array(
				'key'               => $key_prefix . '_section_header_title',
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
				'placeholder'   => '',
				'prepend'       => '',
				'append'        => '',
				'maxlength'     => '',
			),
			array(
				'key'               => $key_prefix . '_section_header_line_color',
				'label'             => 'Line Color',
				'name'              => 'line_color',
				'aria-label'        => '',
				'type'              => 'select',
				'instructions'      => 'Please select a color',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices' => array(
					'gold'   => 'gold',
					'green'  => 'green',
					'yellow' => 'yellow',
				),
				'default_value'      => 'gold',
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
		),
	);
}

function apache_2026_flexible_section_id_sub_field( string $key_prefix ): array {
	return array(
		'key'               => $key_prefix . '_section_id',
		'label'             => 'Section ID',
		'name'              => 'section_id',
		'aria-label'        => '',
		'type'              => 'text',
		'instructions'      => 'Optional anchor ID for this section.',
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
	);
}

function apache_2026_flexible_remove_top_padding_sub_field( string $key ): array {
	return array(
		'key'               => $key,
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
	);
}

function apache_2026_is_valid_acf_field_definition( mixed $field ): bool {
	if ( ! is_array( $field ) ) {
		return false;
	}

	$key   = isset( $field['key'] ) ? $field['key'] : '';
	$type  = isset( $field['type'] ) ? $field['type'] : '';
	$label = isset( $field['label'] ) ? $field['label'] : '';

	return is_string( $key ) && '' !== trim( $key )
		&& is_string( $type ) && '' !== trim( $type )
		&& is_string( $label ) && '' !== trim( $label );
}

function apache_2026_is_acf_field_group_editor_screen(): bool {
	if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
		return false;
	}

	$screen = get_current_screen();

	return $screen instanceof WP_Screen && 'acf-field-group' === $screen->post_type;
}

function apache_2026_acf_fields_are_structurally_valid( mixed $fields ): bool {
	if ( ! is_array( $fields ) || empty( $fields ) ) {
		return false;
	}

	foreach ( $fields as $field ) {
		if ( ! apache_2026_is_valid_acf_field_definition( $field ) ) {
			return false;
		}

		$type = isset( $field['type'] ) && is_string( $field['type'] ) ? trim( $field['type'] ) : '';

		if ( in_array( $type, array( 'group', 'repeater', 'flexible_content' ), true ) ) {
			$nested_fields = $field['sub_fields'] ?? null;

			if ( ! apache_2026_acf_fields_are_structurally_valid( $nested_fields ) ) {
				return false;
			}
		}
	}

	return true;
}

function apache_2026_insert_layout_sub_field_after_key( array $layout, array $sub_field, string $after_key ): array {
	if ( empty( $layout['sub_fields'] ) || ! is_array( $layout['sub_fields'] ) ) {
		$layout['sub_fields'] = array( $sub_field );
		return $layout;
	}

	$sub_field_name = isset( $sub_field['name'] ) && is_string( $sub_field['name'] ) ? trim( $sub_field['name'] ) : '';

	foreach ( $layout['sub_fields'] as $existing_sub_field ) {
		$existing_name = isset( $existing_sub_field['name'] ) && is_string( $existing_sub_field['name'] ) ? trim( $existing_sub_field['name'] ) : '';

		if ( '' !== $sub_field_name && $existing_name === $sub_field_name ) {
			return $layout;
		}
	}

	$updated_sub_fields = array();
	$inserted           = false;

	foreach ( $layout['sub_fields'] as $existing_sub_field ) {
		$updated_sub_fields[] = $existing_sub_field;

		$existing_key = isset( $existing_sub_field['key'] ) && is_string( $existing_sub_field['key'] ) ? trim( $existing_sub_field['key'] ) : '';

		if ( ! $inserted && '' !== $after_key && $existing_key === $after_key ) {
			$updated_sub_fields[] = $sub_field;
			$inserted             = true;
		}
	}

	if ( ! $inserted ) {
		$updated_sub_fields[] = $sub_field;
	}

	$layout['sub_fields'] = $updated_sub_fields;

	return $layout;
}

function apache_2026_remove_layout_sub_field_by_name( array $layout, string $field_name ): array {
	if ( empty( $layout['sub_fields'] ) || ! is_array( $layout['sub_fields'] ) ) {
		return $layout;
	}

	$layout['sub_fields'] = array_values(
		array_filter(
			$layout['sub_fields'],
			static function ( $sub_field ) use ( $field_name ): bool {
				if ( ! is_array( $sub_field ) ) {
					return false;
				}

				$existing_name = isset( $sub_field['name'] ) && is_string( $sub_field['name'] ) ? trim( $sub_field['name'] ) : '';

				return $existing_name !== $field_name;
			}
		)
	);

	return $layout;
}

function apache_2026_ensure_runtime_block_module_fields( array $field ): array {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}

	foreach ( $field['layouts'] as $layout_key => $layout ) {
		if ( ! is_array( $layout ) || empty( $layout['name'] ) || ! is_string( $layout['name'] ) ) {
			continue;
		}

		if ( 'stats' === $layout['name'] ) {
			$field['layouts'][ $layout_key ] = apache_2026_insert_layout_sub_field_after_key(
				$layout,
				apache_2026_flexible_remove_top_padding_sub_field( 'field_6a29_stats_remove_padding' ),
				'field_6a2887d91e2dc'
			);
		}

		if ( 'callout_box' === $layout['name'] ) {
			$field['layouts'][ $layout_key ] = apache_2026_insert_layout_sub_field_after_key(
				$layout,
				apache_2026_flexible_remove_top_padding_sub_field( 'field_6a29_callout_box_remove_padding' ),
				'field_6a288af31e2e6'
			);
		}

	}

	return $field;
}
add_filter( 'acf/load_field/key=field_5e15ed5e87e67', 'apache_2026_ensure_runtime_block_module_fields', 20 );

function apache_2026_ensure_accordion_layout_fields( array $field ): array {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}

	$layout_key = 'layout_691f830a6c92e';

	if ( empty( $field['layouts'][ $layout_key ]['sub_fields'] ) || ! is_array( $field['layouts'][ $layout_key ]['sub_fields'] ) ) {
		return $field;
	}

	$sub_fields = $field['layouts'][ $layout_key ]['sub_fields'];
	$existing   = array();

	foreach ( $sub_fields as $sub_field ) {
		if ( isset( $sub_field['name'] ) && is_string( $sub_field['name'] ) ) {
			$existing[] = $sub_field['name'];
		}
	}

	$injected_fields = array();

	if ( ! in_array( 'margin_top', $existing, true ) ) {
		$injected_fields[] = array(
			'key'               => 'field_accordion_margin_top_2026',
			'label'             => 'Margin Top',
			'name'              => 'margin_top',
			'aria-label'        => '',
			'type'              => 'text',
			'instructions'      => 'Add margin to top of module as needed.',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'relevanssi_exclude' => 0,
			'default_value'      => 0,
			'maxlength'          => '',
			'allow_in_bindings'  => 0,
			'placeholder'        => '',
			'prepend'            => '',
			'append'             => 'px',
		);
	}

	if ( ! in_array( 'remove_padding', $existing, true ) ) {
		$injected_fields[] = array(
			'key'               => 'field_accordion_remove_padding_2026',
			'label'             => 'Remove Padding',
			'name'              => 'remove_padding',
			'aria-label'        => '',
			'type'              => 'true_false',
			'instructions'      => 'Remove default top and bottom section padding for this module.',
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
		);
	}

	if ( ! in_array( 'accordion_style', $existing, true ) ) {
		$injected_fields[] = array(
			'key'               => 'field_accordion_style_2026',
			'label'             => 'Accordion Style',
			'name'              => 'accordion_style',
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
			'relevanssi_exclude' => 0,
			'choices'            => array(
				'default'   => 'Default',
				'gray_bars' => 'Gray Bars',
			),
			'default_value'      => 'default',
			'return_format'      => 'value',
			'multiple'           => 0,
			'allow_null'         => 0,
			'ui'                 => 0,
			'ajax'               => 0,
			'placeholder'        => '',
			'allow_custom'       => 0,
			'search_placeholder' => '',
			'create_options'     => 0,
			'save_options'       => 0,
		);
	}

	if ( empty( $injected_fields ) ) {
		return $field;
	}

	$insertion_index = 0;

	foreach ( $sub_fields as $index => $sub_field ) {
		if ( isset( $sub_field['name'] ) && 'accordion_name' === $sub_field['name'] ) {
			$insertion_index = $index;
			break;
		}
	}

	array_splice( $sub_fields, $insertion_index, 0, $injected_fields );
	$field['layouts'][ $layout_key ]['sub_fields'] = $sub_fields;

	return $field;
}

add_filter( 'acf/load_field/key=field_5e15ed5e87e67', 'apache_2026_ensure_accordion_layout_fields' );

function apache_2026_persist_runtime_accordion_fields( mixed $post_id ): void {
	if ( ! function_exists( 'update_post_meta' ) ) {
		return;
	}

	$post_id = is_numeric( $post_id ) ? absint( $post_id ) : 0;

	if ( $post_id <= 0 || empty( $_POST['acf'] ) || ! is_array( $_POST['acf'] ) ) {
		return;
	}

	$blocks_field_key = 'field_5e15ed5e87e67';
	$blocks_rows      = $_POST['acf'][ $blocks_field_key ] ?? null;

	if ( ! is_array( $blocks_rows ) ) {
		return;
	}

	$row_index = 0;

	foreach ( $blocks_rows as $row ) {
		if ( ! is_array( $row ) ) {
			++$row_index;
			continue;
		}

		if ( ( $row['acf_fc_layout'] ?? '' ) !== 'accordion' ) {
			++$row_index;
			continue;
		}

		$style_value = $row['field_accordion_style_2026'] ?? null;

		if ( is_array( $style_value ) ) {
			$style_value = reset( $style_value );
		}

		if ( ! is_string( $style_value ) || '' === trim( $style_value ) ) {
			++$row_index;
			continue;
		}

		$style_value = trim( $style_value );
		$accordion_id = isset( $row['field_691f8dbd0b7e2'] ) && is_string( $row['field_691f8dbd0b7e2'] )
			? sanitize_title( $row['field_691f8dbd0b7e2'] )
			: '';

		update_post_meta( $post_id, 'blocks_' . $row_index . '_accordion_style', $style_value );
		update_post_meta( $post_id, '_blocks_' . $row_index . '_accordion_style', 'field_accordion_style_2026' );

		if ( '' !== $accordion_id ) {
			update_post_meta( $post_id, 'apache_2026_accordion_style_' . $accordion_id, $style_value );
		}

		++$row_index;
	}
}

add_action( 'acf/save_post', 'apache_2026_persist_runtime_accordion_fields', 20 );

function apache_2026_apply_flexible_layout_options( string $markup, array $options ): string {
	$classes = isset( $options['classes'] ) && is_array( $options['classes'] ) ? array_filter( $options['classes'] ) : array();

	if ( '' === trim( $markup ) || empty( $classes ) ) {
		return $markup;
	}

	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$processor = new WP_HTML_Tag_Processor( $markup );

		while ( $processor->next_tag( 'section' ) ) {
			$class_attribute = (string) $processor->get_attribute( 'class' );

			if ( ! str_contains( $class_attribute, 'apache-flex' ) ) {
				continue;
			}

			foreach ( $classes as $class ) {
				$processor->add_class( sanitize_html_class( $class ) );
			}

			return $processor->get_updated_html();
		}
	}

	return preg_replace_callback(
		'/<section\b([^>]*)class=(["\'])([^"\']*\bapache-flex\b[^"\']*)(\2)([^>]*)>/i',
		static function ( array $matches ) use ( $classes ): string {
			$merged_classes = trim( $matches[3] . ' ' . implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );

			return '<section' . $matches[1] . 'class=' . $matches[2] . esc_attr( $merged_classes ) . $matches[2] . $matches[5] . '>';
		},
		$markup,
		1
	) ?? $markup;
}

function apache_2026_get_flexible_layout_map(): array {
	return apply_filters(
		'apache_2026_flexible_layout_map',
		array(
			'apache_section_header' => 'apache-section-header',
			'wysiwyg_editor'      => 'wysiwyg-editor',
			'three_row_articles_color_split' => 'three-row-articles-color-split',
			'new_three_row_articles_color_split' => 'new-three-row-articles-color-split',
			'full_width_biography' => 'leadership-members',
			'new_boxed_content_media' => 'new-boxed-content-media',
			'two_column_split' => 'two-column-split',
			'two_row_image_right_content_left_color_split' => 'two-row-image-right-content-left-color-split',
			'full_width_one_col_bg_image' => 'full-width-one-col-bg-image',
			'icon_style_content_block' => 'icon-style-content-block',
			'accordion' => 'accordion',
			'featured_post_slider' => 'featured-post-slider',
			'gallery_module' => 'gallery',
			'video_module' => 'video',
			'hover_boxes' => 'hover-boxes',
			'features_list' => 'features-list',
			'stats' => 'stats',
			'callout_box' => 'callout-box',
			'map' => 'map',
		)
	);
}

function apache_2026_get_flexible_template_candidates( string $layout ): array {
	$layout = trim( $layout );

	if ( '' === $layout ) {
		return array();
	}

	$map        = apache_2026_get_flexible_layout_map();
	$candidates = array();

	if ( isset( $map[ $layout ] ) && is_string( $map[ $layout ] ) ) {
		$candidates[] = $map[ $layout ];
	}

	if ( preg_match( '/^[A-Za-z0-9_-]+$/', $layout ) ) {
		$candidates[] = $layout;
	}

	$candidates[] = sanitize_title( str_replace( '_', '-', $layout ) );

	return array_values( array_unique( array_filter( $candidates ) ) );
}

function apache_2026_get_id_by_slug( string $page_slug ): ?int {
	$page = get_page_by_path( $page_slug );

	return $page instanceof WP_Post ? (int) $page->ID : null;
}

function apache_2026_get_acf_field( string $field_name, int|string|null $post_id = null ): mixed {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	return get_field( $field_name, $post_id ?: false );
}

function apache_2026_normalize_acf_image( mixed $image, string $size = 'full' ): array {
	if ( empty( $image ) ) {
		return array();
	}

	$attachment_id = 0;
	$url           = '';
	$alt           = '';

	if ( is_numeric( $image ) ) {
		$attachment_id = absint( $image );
		$url           = (string) wp_get_attachment_image_url( $attachment_id, $size );
		$alt           = (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
	} elseif ( is_array( $image ) ) {
		$attachment_id = isset( $image['ID'] ) ? absint( $image['ID'] ) : 0;
		$url           = isset( $image['url'] ) && is_string( $image['url'] ) ? $image['url'] : '';
		$alt           = isset( $image['alt'] ) && is_string( $image['alt'] ) ? $image['alt'] : '';
	} elseif ( is_string( $image ) ) {
		$url = $image;
	}

	if ( ! $alt && $attachment_id ) {
		$alt = (string) get_the_title( $attachment_id );
	}

	return array(
		'id'  => $attachment_id,
		'url' => $url,
		'alt' => $alt,
	);
}

function apache_2026_get_hero_button_data( mixed $button, bool $is_slider = false ): array {
	if ( ! is_array( $button ) ) {
		return array();
	}

	$label  = isset( $button['button_text'] ) && is_string( $button['button_text'] ) ? trim( $button['button_text'] ) : '';
	$url    = '';
	$target = '_self';

	if ( $is_slider ) {
		$link_type = isset( $button['slick_link'] ) && is_string( $button['slick_link'] ) ? $button['slick_link'] : 'internal';

		if ( 'external' === $link_type ) {
			$link   = isset( $button['link'] ) && is_array( $button['link'] ) ? $button['link'] : array();
			$url    = isset( $link['url'] ) && is_string( $link['url'] ) ? trim( $link['url'] ) : '';
			$target = isset( $link['target'] ) && is_string( $link['target'] ) ? $link['target'] : '_blank';
		} else {
			$url = isset( $button['page'] ) && is_string( $button['page'] ) ? trim( $button['page'] ) : '';
		}
	} else {
		$url = isset( $button['url'] ) && is_string( $button['url'] ) ? trim( $button['url'] ) : '';
	}

	if ( ! in_array( $target, array( '_blank', '_self' ), true ) ) {
		$target = '_self';
	}

	if ( '' === $label || '' === $url ) {
		return array();
	}

	return array(
		'label'  => $label,
		'url'    => $url,
		'target' => $target,
	);
}

function apache_2026_get_hero_cta_data( mixed $cta ): array {
	if ( ! is_array( $cta ) ) {
		return array();
	}

	$url    = isset( $cta['url'] ) && is_string( $cta['url'] ) ? trim( $cta['url'] ) : '';
	$title  = isset( $cta['title'] ) && is_string( $cta['title'] ) ? trim( $cta['title'] ) : '';
	$target = isset( $cta['target'] ) && is_string( $cta['target'] ) ? $cta['target'] : '_self';

	if ( '' === $url || '' === $title ) {
		return array();
	}

	return array(
		'label'  => $title,
		'url'    => $url,
		'target' => apache_2026_sanitize_link_target( $target ),
	);
}

function apache_2026_normalize_link_field( mixed $link, string $class = '' ): array {
	if ( ! is_array( $link ) ) {
		return array();
	}

	$url    = isset( $link['url'] ) && is_string( $link['url'] ) ? trim( $link['url'] ) : '';
	$title  = isset( $link['title'] ) && is_string( $link['title'] ) ? trim( $link['title'] ) : '';
	$target = isset( $link['target'] ) && is_string( $link['target'] ) ? $link['target'] : '_self';

	if ( '' === $url || '' === $title ) {
		return array();
	}

	return array(
		'url'    => $url,
		'title'  => $title,
		'target' => apache_2026_sanitize_link_target( $target ),
		'class'  => $class,
	);
}

function apache_2026_get_section_header_data( mixed $section_header, int|string|null $post_id = null ): array {
	$data = array(
		'eyebrow'      => '',
		'line_color'   => 'gold',
		'headline'     => '',
		'cta_options'  => 'none',
		'link_cta'     => array(),
		'button_ctas'  => array(),
		'content'      => '',
		'has_content'  => false,
	);

	if ( ! is_array( $section_header ) ) {
		return $data;
	}

	$eyebrow              = isset( $section_header['eyebrow'] ) && is_array( $section_header['eyebrow'] ) ? $section_header['eyebrow'] : array();
	$use_parent_directory = ! empty( $eyebrow['use_parent_directory'] );
	$eyebrow_title        = isset( $eyebrow['title'] ) && is_string( $eyebrow['title'] ) ? trim( $eyebrow['title'] ) : '';
	$line_color           = isset( $eyebrow['line_color'] ) && is_string( $eyebrow['line_color'] ) ? trim( $eyebrow['line_color'] ) : 'gold';

	if ( $use_parent_directory ) {
		$current_post = get_post( $post_id ?: get_the_ID() );

		if ( $current_post instanceof WP_Post && ! empty( $current_post->post_parent ) ) {
			$parent_title = get_the_title( (int) $current_post->post_parent );

			if ( is_string( $parent_title ) && '' !== trim( $parent_title ) ) {
				$eyebrow_title = trim( $parent_title );
			}
		}
	}

	$cta_options = isset( $section_header['cta_options'] ) && is_string( $section_header['cta_options'] ) ? trim( $section_header['cta_options'] ) : 'none';
	$button_ctas = array();
	$button_rows = isset( $section_header['button_repeater'] ) && is_array( $section_header['button_repeater'] ) ? $section_header['button_repeater'] : array();

	foreach ( $button_rows as $button_row ) {
		if ( ! is_array( $button_row ) ) {
			continue;
		}

		$button_cta = apache_2026_normalize_link_field( $button_row['button'] ?? array(), 'btn btn-primary' );

		if ( ! empty( $button_cta ) ) {
			$button_ctas[] = $button_cta;
		}
	}

	$data['eyebrow']     = $eyebrow_title;
	$data['line_color']  = in_array( $line_color, array( 'gold', 'green', 'yellow' ), true ) ? $line_color : 'gold';
	$data['headline']    = isset( $section_header['headline'] ) && is_string( $section_header['headline'] ) ? trim( $section_header['headline'] ) : '';
	$data['cta_options'] = in_array( $cta_options, array( 'none', 'link', 'rightmulti', 'centermulti' ), true ) ? $cta_options : 'none';
	$data['link_cta']    = apache_2026_normalize_link_field( $section_header['link'] ?? array(), 'btn btn-more' );
	$data['button_ctas'] = $button_ctas;
	$data['content']     = isset( $section_header['content'] ) && is_string( $section_header['content'] ) ? trim( $section_header['content'] ) : '';
	$data['has_content'] = '' !== $data['eyebrow'] || '' !== $data['headline'] || '' !== $data['content'] || ! empty( $data['link_cta'] ) || ! empty( $data['button_ctas'] );

	return $data;
}

function apache_2026_get_hero_data( int|string|null $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	$data = array(
		'type'               => 'standard',
		'eyebrow'            => '',
		'title'              => '',
		'content'            => '',
		'button'             => array(),
		'image'              => array(),
		'slides'             => array(),
		'is_short'           => false,
		'has_text_overlay'   => false,
		'has_bottom_overlay' => false,
		'no_overlay_content' => array(),
	);

	if ( ! function_exists( 'get_field' ) ) {
		$data['type'] = 'fallback';
		return $data;
	}

	$data['is_short']           = (bool) get_field( 'short_hero_image', $post_id );
	$data['has_bottom_overlay'] = (bool) get_field( 'bottom_cut_box_overlay', $post_id );

	if ( get_field( 'hero_slick_slider', $post_id ) && have_rows( 'slick_slider_content', $post_id ) ) {
		while ( have_rows( 'slick_slider_content', $post_id ) ) {
			the_row();

			$headline = get_sub_field( 'headline' );
			$eyebrow  = get_sub_field( 'section_header' );
			$image    = apache_2026_normalize_acf_image( get_sub_field( 'image' ), 'full' );
			$button   = apache_2026_get_hero_button_data( get_sub_field( 'button' ), true );

			$headline = is_string( $headline ) ? trim( $headline ) : '';
			$eyebrow  = is_string( $eyebrow ) ? trim( $eyebrow ) : '';

			if ( '' === $headline && '' === $eyebrow && empty( $image['url'] ) && empty( $button ) ) {
				continue;
			}

			$data['slides'][] = array(
				'eyebrow' => $eyebrow,
				'title'   => $headline,
				'image'   => $image,
				'button'  => $button,
			);
		}

		if ( ! empty( $data['slides'] ) ) {
			$data['type'] = 'slider';
			return $data;
		}
	}

	$hero_content             = get_field( 'hero_content', $post_id );
	$no_overlay_content       = get_field( 'no_overlay_content', $post_id );
	$data['has_text_overlay'] = (bool) get_field( 'text_overlay', $post_id );
	$data['image']            = apache_2026_normalize_acf_image( get_field( 'hero_image', $post_id ), 'full' );
	$data['no_overlay_content'] = apache_2026_get_section_header_data( $no_overlay_content, $post_id );

	if ( is_array( $no_overlay_content ) ) {
		$formatted_no_overlay_content = trim( (string) get_post_meta( (int) $post_id, 'no_overlay_content_content', true ) );

		if ( '' === $formatted_no_overlay_content ) {
			$formatted_no_overlay_content = isset( $no_overlay_content['content'] ) && is_string( $no_overlay_content['content'] )
				? apache_2026_normalize_wysiwyg_markup( $no_overlay_content['content'] )
				: '';
		}

		$data['no_overlay_content']['content'] = $formatted_no_overlay_content;
		$data['no_overlay_content']['has_content'] = '' !== $data['no_overlay_content']['eyebrow']
			|| '' !== $data['no_overlay_content']['headline']
			|| '' !== $formatted_no_overlay_content
			|| ! empty( $data['no_overlay_content']['link_cta'] )
			|| ! empty( $data['no_overlay_content']['button_ctas'] );
	}

	if ( is_array( $hero_content ) ) {
		$eyebrow  = $hero_content['section_header'] ?? '';
		$headline = $hero_content['headline'] ?? '';

		$data['eyebrow'] = is_string( $eyebrow ) ? trim( $eyebrow ) : '';

		if ( is_string( $headline ) && '' !== trim( $headline ) ) {
			$data['title'] = trim( $headline );
		}

		$data['content'] = trim( (string) get_post_meta( (int) $post_id, 'hero_content_content', true ) );

		if ( '' === $data['content'] ) {
			$data['content'] = isset( $hero_content['content'] ) && is_string( $hero_content['content'] )
				? apache_2026_normalize_wysiwyg_markup( $hero_content['content'] )
				: '';
		}

		$data['button']  = apache_2026_get_hero_cta_data( $hero_content['cta'] ?? array() );
	}

	if ( empty( $data['image']['url'] ) && ! $data['has_text_overlay'] && empty( $data['no_overlay_content']['has_content'] ) ) {
		$data['type'] = 'fallback';
	}

	return $data;
}

function apache_2026_render_hero( array $args = array() ): void {
	$args = wp_parse_args(
		$args,
		array(
			'post_id'       => get_the_ID(),
			'heading_level' => 1,
		)
	);

	if ( function_exists( 'get_field' ) && ! get_field( 'show_hero', $args['post_id'] ) ) {
		return;
	}

	get_template_part(
		'template-parts/global/hero',
		null,
		array(
			'data'          => apache_2026_get_hero_data( $args['post_id'] ),
			'heading_level' => absint( $args['heading_level'] ),
		)
	);
}

function apache_2026_get_announcement_banner_data(): array {
	$data = array(
		'is_active' => false,
		'message'   => '',
	);

	if ( ! function_exists( 'get_field' ) ) {
		return $data;
	}

	$is_active = (bool) get_field( 'activate_announcement', 'option' );
	$message   = get_field( 'banner_message', 'option' );
	$message   = is_string( $message ) ? trim( $message ) : '';

	if ( ! $is_active || '' === $message ) {
		return $data;
	}

	return array(
		'is_active' => true,
		'message'   => $message,
	);
}

function apache_2026_render_announcement_banner(): void {
	get_template_part(
		'template-parts/global/announcement-banner',
		null,
		array(
			'data' => apache_2026_get_announcement_banner_data(),
		)
	);
}

function apache_2026_get_timeline_data( int|string|null $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	$data = array(
		'section_header' => array(
			'title'      => '',
			'line_color' => 'gold',
		),
		'intro_header'   => '',
		'intro_copy'     => '',
		'groups'         => array(),
	);

	if ( ! function_exists( 'get_field' ) ) {
		return $data;
	}

	$section_header = get_field( 'section_header', $post_id );

	if ( is_array( $section_header ) ) {
		$title      = $section_header['title'] ?? '';
		$line_color = $section_header['line_color'] ?? '';

		$data['section_header']['title'] = is_string( $title ) ? trim( $title ) : '';

		if ( is_string( $line_color ) && '' !== trim( $line_color ) ) {
			$data['section_header']['line_color'] = sanitize_html_class( $line_color );
		}
	}

	$intro_header = get_field( 'intro_header', $post_id );
	$intro_copy   = get_field( 'intro_copy', $post_id );

	$data['intro_header'] = is_string( $intro_header ) ? trim( $intro_header ) : '';
	$data['intro_copy']   = is_string( $intro_copy ) ? trim( $intro_copy ) : '';

	if ( have_rows( 'timeline', $post_id ) ) {
		while ( have_rows( 'timeline', $post_id ) ) {
			the_row();

			$year    = get_sub_field( 'year' );
			$entries = array();

			if ( have_rows( 'timeline_entry' ) ) {
				while ( have_rows( 'timeline_entry' ) ) {
					the_row();

					$event_year    = get_sub_field( 'event_year' );
					$event_content = get_sub_field( 'event_content' );
					$event_image   = apache_2026_normalize_acf_image( get_sub_field( 'event_image' ), 'large' );

					$event_year    = is_string( $event_year ) ? trim( $event_year ) : '';
					$event_content = is_string( $event_content ) ? trim( $event_content ) : '';

					if ( '' === $event_year && '' === $event_content && empty( $event_image['url'] ) ) {
						continue;
					}

					$entries[] = array(
						'year'    => $event_year,
						'content' => $event_content,
						'image'   => $event_image,
					);
				}
			}

			$year = is_string( $year ) ? trim( $year ) : '';

			if ( '' === $year && empty( $entries ) ) {
				continue;
			}

			$data['groups'][] = array(
				'year'    => $year,
				'entries' => $entries,
			);
		}
	}

	return $data;
}

function apache_2026_has_timeline_data( array $data ): bool {
	return '' !== $data['section_header']['title']
		|| '' !== $data['intro_header']
		|| '' !== $data['intro_copy']
		|| ! empty( $data['groups'] );
}

function apache_2026_render_timeline( array $args = array() ): void {
	$args = wp_parse_args(
		$args,
		array(
			'post_id' => get_the_ID(),
		)
	);

	$data = apache_2026_get_timeline_data( $args['post_id'] );

	if ( ! apache_2026_has_timeline_data( $data ) ) {
		return;
	}

	get_template_part(
		'template-parts/global/timeline',
		null,
		array(
			'data' => $data,
		)
	);
}

function apache_2026_get_home_map_data( int|string|null $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	$data = array(
		'margin_top'     => null,
		'header_content' => '',
		'footer_title'   => '',
		'footer_cta'     => array(),
		'locations'      => array(),
	);

	if ( ! function_exists( 'get_field' ) ) {
		return $data;
	}

	$data['margin_top'] = apache_2026_sanitize_css_px_value( get_field( 'home_map_margin_top', $post_id ), 0, 600 );
	$data['header_content'] = get_field( 'home_map_header_content', $post_id );
	$data['header_content'] = is_string( $data['header_content'] ) ? trim( $data['header_content'] ) : '';
	$data['footer_title'] = get_field( 'home_map_footer_title', $post_id );
	$data['footer_title'] = is_string( $data['footer_title'] ) ? trim( $data['footer_title'] ) : '';

	$footer_cta = get_field( 'home_map_footer_cta', $post_id );

	if ( is_array( $footer_cta ) ) {
		$url    = isset( $footer_cta['url'] ) && is_string( $footer_cta['url'] ) ? trim( $footer_cta['url'] ) : '';
		$title  = isset( $footer_cta['title'] ) && is_string( $footer_cta['title'] ) ? trim( $footer_cta['title'] ) : '';
		$target = isset( $footer_cta['target'] ) && is_string( $footer_cta['target'] ) ? trim( $footer_cta['target'] ) : '_self';

		if ( '' !== $url && '' !== $title ) {
			$data['footer_cta'] = array(
				'url'    => $url,
				'title'  => $title,
				'target' => in_array( $target, array( '_self', '_blank' ), true ) ? $target : '_self',
			);
		}
	}

	$locations = get_field( 'home_map_locations', $post_id );
	$locations = is_array( $locations ) ? $locations : array();

	foreach ( $locations as $location ) {
		if ( ! is_array( $location ) ) {
			continue;
		}

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
			$location_url = trim( $link );
		}

		if ( '' === $title && '' === $caption && '' === $region && null === $lat && null === $lng && '' === $location_url ) {
			continue;
		}

		$data['locations'][] = array(
			'title'   => $title,
			'caption' => $caption,
			'region'  => $region,
			'lat'     => $lat,
			'lng'     => $lng,
			'url'     => $location_url,
			'target'  => '',
		);
	}

	return $data;
}

function apache_2026_has_home_map_data( array $data ): bool {
	return ! empty( $data['locations'] );
}

function apache_2026_render_home_map( array $args = array() ): void {
	$args = wp_parse_args(
		$args,
		array(
			'post_id' => get_the_ID(),
		)
	);

	$data = apache_2026_get_home_map_data( $args['post_id'] );

	if ( ! apache_2026_has_home_map_data( $data ) ) {
		return;
	}

	get_template_part(
		'template-parts/global/home-map',
		null,
		array(
			'data' => $data,
		)
	);
}

function apache_2026_get_contact_department_key( string $name, array $used_keys = array() ): string {
	$key = sanitize_title( $name );

	if ( '' === $key ) {
		$key = 'department';
	}

	$base_key = $key;
	$index    = 2;

	while ( in_array( $key, $used_keys, true ) ) {
		$key = $base_key . '-' . $index;
		++$index;
	}

	return $key;
}

function apache_2026_get_contact_information_data( int|string|null $post_id = null ): array {
	$post_id = $post_id ?: get_the_ID();

	$data = array(
		'contact_blocks'   => array(),
		'department_block' => array(
			'margin_top'   => null,
			'headline'     => '',
			'sub_headline' => '',
			'departments'  => array(),
		),
	);

	if ( ! function_exists( 'have_rows' ) ) {
		return $data;
	}

	if ( have_rows( 'contact_block', $post_id ) ) {
		while ( have_rows( 'contact_block', $post_id ) ) {
			the_row();

			$headline       = get_sub_field( 'headline' );
			$content        = get_sub_field( 'content' );
			$margin_top     = apache_2026_sanitize_css_px_value( get_sub_field( 'margin_top' ), 0, 600 );
			$bottom_divider = (bool) get_sub_field( 'bottom_divider' );

			$headline = is_string( $headline ) ? trim( $headline ) : '';
			$content  = is_string( $content ) ? trim( $content ) : '';

			if ( '' === $headline && '' === $content && ! $bottom_divider ) {
				continue;
			}

			$data['contact_blocks'][] = array(
				'margin_top'     => $margin_top,
				'headline'       => $headline,
				'content'        => $content,
				'bottom_divider' => $bottom_divider,
			);
		}
	}

	if ( have_rows( 'department_block', $post_id ) ) {
		while ( have_rows( 'department_block', $post_id ) ) {
			the_row();

			$headline     = get_sub_field( 'headline' );
			$sub_headline = get_sub_field( 'sub_headline' );
			$used_keys    = array();
			$departments  = array();

			if ( have_rows( 'department' ) ) {
				while ( have_rows( 'department' ) ) {
					the_row();

					$name    = get_sub_field( 'department_name' );
					$content = get_sub_field( 'content' );

					$name    = is_string( $name ) ? trim( $name ) : '';
					$content = is_string( $content ) ? trim( $content ) : '';

					if ( '' === $name && '' === $content ) {
						continue;
					}

					$name        = $name ?: __( 'Department', 'apache-2026' );
					$key         = apache_2026_get_contact_department_key( $name, $used_keys );
					$used_keys[] = $key;

					$departments[] = array(
						'key'     => $key,
						'name'    => $name,
						'content' => $content,
					);
				}
			}

			$data['department_block'] = array(
				'margin_top'   => apache_2026_sanitize_css_px_value( get_sub_field( 'margin_top' ), 0, 600 ),
				'headline'     => is_string( $headline ) ? trim( $headline ) : '',
				'sub_headline' => is_string( $sub_headline ) ? trim( $sub_headline ) : '',
				'departments'  => $departments,
			);

			break;
		}
	}

	return $data;
}

function apache_2026_has_contact_information_data( array $data ): bool {
	$department_block = $data['department_block'] ?? array();

	return ! empty( $data['contact_blocks'] )
		|| ! empty( $department_block['headline'] )
		|| ! empty( $department_block['sub_headline'] )
		|| ! empty( $department_block['departments'] );
}

function apache_2026_render_contact_information( array $args = array() ): void {
	$args = wp_parse_args(
		$args,
		array(
			'post_id' => get_the_ID(),
		)
	);

	$data = apache_2026_get_contact_information_data( $args['post_id'] );

	if ( ! apache_2026_has_contact_information_data( $data ) ) {
		return;
	}

	get_template_part(
		'template-parts/global/contact-information',
		null,
		array(
			'data' => $data,
		)
	);
}

function apache_2026_render_flexible_content( string $field_name = 'modules', mixed $post_id = null ): void {
	if ( ! function_exists( 'have_rows' ) || ! have_rows( $field_name, $post_id ) ) {
		return;
	}

	while ( have_rows( $field_name, $post_id ) ) {
		the_row();

		$layout = get_row_layout();

		if ( ! $layout ) {
			continue;
		}

		$template = '';

		foreach ( apache_2026_get_flexible_template_candidates( $layout ) as $candidate ) {
			$template = locate_template( "template-parts/flexible/{$candidate}.php" );

			if ( $template ) {
				include $template;
				break;
			}
		}
	}
}
