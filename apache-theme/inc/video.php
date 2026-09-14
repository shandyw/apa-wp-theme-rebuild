<?php
/**
 * Video helpers and ACF registration.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_register_video_acf_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'    => 'group_apache_2026_video_cpt',
			'title'  => __( 'Video Details', 'apache-2026' ),
			'fields' => array(
				array(
					'key'           => 'field_apache_2026_video_source_type',
					'label'         => __( 'Video Source Type', 'apache-2026' ),
					'name'          => 'video_source_type',
					'type'          => 'select',
					'choices'       => array(
						'vimeo'       => __( 'Vimeo', 'apache-2026' ),
						'youtube'     => __( 'YouTube', 'apache-2026' ),
						'self_hosted' => __( 'Self-hosted Video', 'apache-2026' ),
						'embed'       => __( 'Custom Embed', 'apache-2026' ),
					),
					'default_value' => 'vimeo',
					'ui'            => 1,
					'return_format' => 'value',
				),
				array(
					'key'               => 'field_apache_2026_vimeo_video_id',
					'label'             => __( 'Vimeo Video ID', 'apache-2026' ),
					'name'              => 'vimeo_video_id',
					'type'              => 'text',
					'instructions'      => __( 'Format must be like: https://player.vimeo.com/video/...', 'apache-2026' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_apache_2026_video_source_type',
								'operator' => '==',
								'value'    => 'vimeo',
							),
						),
					),
				),
				array(
					'key'               => 'field_apache_2026_youtube_video_id',
					'label'             => __( 'YouTube Video ID', 'apache-2026' ),
					'name'              => 'youtube_video_id',
					'type'              => 'text',
					'instructions'      => __( 'Enter only the YouTube video ID, not the full URL.', 'apache-2026' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_apache_2026_video_source_type',
								'operator' => '==',
								'value'    => 'youtube',
							),
						),
					),
				),
				array(
					'key'               => 'field_apache_2026_self_hosted_video_file',
					'label'             => __( 'Self-hosted Video File', 'apache-2026' ),
					'name'              => 'self_hosted_video_file',
					'type'              => 'file',
					'return_format'     => 'array',
					'mime_types'        => 'mp4,webm',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_apache_2026_video_source_type',
								'operator' => '==',
								'value'    => 'self_hosted',
							),
						),
					),
				),
				array(
					'key'               => 'field_apache_2026_custom_embed_code',
					'label'             => __( 'Custom Embed Code', 'apache-2026' ),
					'name'              => 'custom_embed_code',
					'type'              => 'textarea',
					'instructions'      => __( 'Use only trusted embed code. Output is sanitized before rendering.', 'apache-2026' ),
					'rows'              => 4,
					'new_lines'         => 'br',
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_apache_2026_video_source_type',
								'operator' => '==',
								'value'    => 'embed',
							),
						),
					),
				),
				array(
					'key'           => 'field_apache_2026_video_poster_image',
					'label'         => __( 'Poster', 'apache-2026' ),
					'name'          => 'video_poster_image',
					'type'          => 'image',
					'instructions'  => __( 'Optional poster image shown for the video when available.', 'apache-2026' ),
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
				),
				array(
					'key'           => 'field_apache_2026_video_caption',
					'label'         => __( 'Caption / Transcript', 'apache-2026' ),
					'name'          => 'video_caption',
					'type'          => 'wysiwyg',
					'tabs'          => 'all',
					'toolbar'       => 'basic',
					'media_upload'  => 0,
					'delay'         => 0,
				),
				array(
					'key'        => 'field_apache_2026_video_settings_tab',
					'label'      => __( 'Video Settings', 'apache-2026' ),
					'name'       => '',
					'type'       => 'tab',
					'placement'  => 'top',
					'endpoint'   => 0,
				),
				array(
					'key'           => 'field_apache_2026_video_autoplay',
					'label'         => __( 'Autoplay', 'apache-2026' ),
					'name'          => 'video_autoplay',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui'            => 1,
				),
				array(
					'key'           => 'field_apache_2026_video_muted',
					'label'         => __( 'Muted', 'apache-2026' ),
					'name'          => 'video_muted',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui'            => 1,
				),
				array(
					'key'           => 'field_apache_2026_video_loop',
					'label'         => __( 'Loop', 'apache-2026' ),
					'name'          => 'video_loop',
					'type'          => 'true_false',
					'default_value' => 0,
					'ui'            => 1,
				),
				array(
					'key'           => 'field_apache_2026_video_controls',
					'label'         => __( 'Controls', 'apache-2026' ),
					'name'          => 'video_controls',
					'type'          => 'true_false',
					'default_value' => 1,
					'ui'            => 1,
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'apache_video',
					),
				),
			),
			'acfe'     => array(
				'autosync' => array( 'json' ),
			),
		)
	);
}
add_action( 'acf/init', 'apache_2026_register_video_acf_fields' );

function apache_2026_get_video_select_choices(): array {
	$videos = get_posts(
		array(
			'post_type'              => 'apache_video',
			'post_status'            => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'posts_per_page'         => -1,
			'orderby'                => array(
				'title' => 'ASC',
				'date'  => 'DESC',
			),
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$choices = array();

	foreach ( $videos as $video ) {
		if ( ! $video instanceof WP_Post ) {
			continue;
		}

		$title = trim( (string) get_the_title( $video ) );
		$title = '' !== $title ? $title : sprintf( __( 'Video #%d', 'apache-2026' ), (int) $video->ID );

		$choices[ (string) $video->ID ] = $title;
	}

	return $choices;
}

function apache_2026_get_video_flexible_layout_definition(): array {
	$section_header = apache_2026_flexible_section_header_sub_field( 'field_apache_2026_video_module' );

	if ( ! empty( $section_header['sub_fields'] ) && is_array( $section_header['sub_fields'] ) ) {
		foreach ( $section_header['sub_fields'] as &$sub_field ) {
			if ( isset( $sub_field['name'] ) && 'line_color' === $sub_field['name'] ) {
				$sub_field['default_value'] = '';
			}
		}
		unset( $sub_field );
	}

	return array(
		'key'        => 'layout_apache_2026_video_module',
		'name'       => 'video_module',
		'label'      => 'Video',
		'display'    => 'block',
		'sub_fields' => array(
			$section_header,
			array(
				'key'               => 'field_apache_2026_video_module_title',
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
				'key'               => 'field_apache_2026_video_module_selected_video',
				'label'             => 'Selected Video',
				'name'              => 'selected_video',
				'aria-label'        => '',
				'type'              => 'select',
				'instructions'      => 'Select the Video to display.',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices'           => apache_2026_get_video_select_choices(),
				'default_value'     => '',
				'allow_null'        => 0,
				'multiple'          => 0,
				'ui'                => 0,
				'return_format'     => 'value',
				'ajax'              => 0,
				'placeholder'       => '',
			),
			apache_2026_flexible_section_id_sub_field( 'field_apache_2026_video_module' ),
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

function apache_2026_normalize_video_flexible_layout( array $layout ): array {
	$canonical = apache_2026_get_video_flexible_layout_definition();

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

function apache_2026_register_video_flexible_layout( array $field ): array {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}

	foreach ( $field['layouts'] as $layout_key => $layout ) {
		if ( isset( $layout['name'] ) && 'video_module' === $layout['name'] ) {
			if (
				apache_2026_is_acf_field_group_editor_screen()
				&& apache_2026_acf_fields_are_structurally_valid( $layout['sub_fields'] ?? null )
			) {
				return $field;
			}

			$field['layouts'][ $layout_key ] = apache_2026_normalize_video_flexible_layout( $layout );
			return $field;
		}
	}

	$field['layouts']['layout_apache_2026_video_module'] = apache_2026_get_video_flexible_layout_definition();

	return $field;
}
add_filter( 'acf/load_field/key=field_5e15ed5e87e67', 'apache_2026_register_video_flexible_layout' );

function apache_2026_get_video_post_id( mixed $video_value ): int {
	if ( $video_value instanceof WP_Post ) {
		return 'apache_video' === $video_value->post_type ? (int) $video_value->ID : 0;
	}

	if ( is_numeric( $video_value ) ) {
		$video_id = absint( $video_value );
		$video    = get_post( $video_id );

		return $video instanceof WP_Post && 'apache_video' === $video->post_type ? $video_id : 0;
	}

	if ( is_array( $video_value ) && isset( $video_value['ID'] ) ) {
		return apache_2026_get_video_post_id( $video_value['ID'] );
	}

	return 0;
}

function apache_2026_sanitize_video_id_string( mixed $value ): string {
	if ( is_scalar( $value ) ) {
		$value = trim( (string) $value );
	} else {
		$value = '';
	}

	if ( '' === $value ) {
		return '';
	}

	return preg_replace( '/[^A-Za-z0-9_-]/', '', $value ) ?? '';
}

function apache_2026_normalize_vimeo_video_data( mixed $value ): array {
	if ( is_scalar( $value ) ) {
		$value = trim( (string) $value );
	} else {
		$value = '';
	}

	if ( '' === $value ) {
		return array(
			'id'   => '',
			'hash' => '',
		);
	}

	$hash = '';

	$parsed_url = wp_parse_url( $value );

	if ( is_array( $parsed_url ) ) {
		if ( ! empty( $parsed_url['query'] ) && is_string( $parsed_url['query'] ) ) {
			parse_str( $parsed_url['query'], $query_args );

			if ( ! empty( $query_args['h'] ) && is_scalar( $query_args['h'] ) ) {
				$hash = apache_2026_sanitize_video_id_string( $query_args['h'] );
			}
		}

		$path = isset( $parsed_url['path'] ) && is_string( $parsed_url['path'] ) ? trim( $parsed_url['path'], '/' ) : '';

		if ( preg_match( '~(?:video/)?(\d+)(?:/([A-Za-z0-9]+))?~', $path, $matches ) ) {
			if ( '' === $hash && ! empty( $matches[2] ) ) {
				$hash = apache_2026_sanitize_video_id_string( $matches[2] );
			}

			return array(
				'id'   => $matches[1],
				'hash' => $hash,
			);
		}
	}

	if ( preg_match( '~/(\d+)(?:$|[?/])~', $value, $matches ) ) {
		return array(
			'id'   => $matches[1],
			'hash' => $hash,
		);
	}

	return array(
		'id'   => apache_2026_sanitize_video_id_string( $value ),
		'hash' => $hash,
	);
}

function apache_2026_normalize_youtube_video_id( mixed $value ): string {
	if ( is_scalar( $value ) ) {
		$value = trim( (string) $value );
	} else {
		$value = '';
	}

	if ( '' === $value ) {
		return '';
	}

	$parsed_url = wp_parse_url( $value );

	if ( is_array( $parsed_url ) ) {
		$host = isset( $parsed_url['host'] ) && is_string( $parsed_url['host'] ) ? strtolower( $parsed_url['host'] ) : '';
		$path = isset( $parsed_url['path'] ) && is_string( $parsed_url['path'] ) ? trim( $parsed_url['path'], '/' ) : '';

		if ( false !== strpos( $host, 'youtu.be' ) && '' !== $path ) {
			return apache_2026_sanitize_video_id_string( strtok( $path, '/' ) );
		}

		if ( false !== strpos( $host, 'youtube.com' ) ) {
			if ( 'watch' === $path && ! empty( $parsed_url['query'] ) && is_string( $parsed_url['query'] ) ) {
				parse_str( $parsed_url['query'], $query_args );

				if ( ! empty( $query_args['v'] ) ) {
					return apache_2026_sanitize_video_id_string( $query_args['v'] );
				}
			}

			if ( preg_match( '~^(embed|shorts)/([^/?#]+)~', $path, $matches ) ) {
				return apache_2026_sanitize_video_id_string( $matches[2] );
			}
		}
	}

	return apache_2026_sanitize_video_id_string( $value );
}

function apache_2026_sanitize_video_classes( mixed $classes ): array {
	if ( is_array( $classes ) ) {
		$class_list = $classes;
	} elseif ( is_string( $classes ) ) {
		$class_list = preg_split( '/\s+/', trim( $classes ) ) ?: array();
	} else {
		$class_list = array();
	}

	$sanitized = array();

	foreach ( $class_list as $class_name ) {
		$class_name = sanitize_html_class( is_string( $class_name ) ? $class_name : '' );

		if ( '' !== $class_name ) {
			$sanitized[] = $class_name;
		}
	}

	return array_values( array_unique( $sanitized ) );
}

function apache_2026_video_embed_allowed_html(): array {
	return array(
		'iframe' => array(
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
		),
		'div'    => array(
			'class' => true,
		),
		'span'   => array(
			'class'       => true,
			'aria-hidden' => true,
		),
		'p'      => array(
			'class' => true,
		),
	);
}

function apache_2026_get_video_component_data( int $video_id, array $args = array() ): array {
	$video = get_post( $video_id );

	if ( ! $video instanceof WP_Post || 'apache_video' !== $video->post_type ) {
		return array();
	}

	if ( 'publish' !== $video->post_status && ! current_user_can( 'read_post', $video_id ) ) {
		return array();
	}

	$source_type = function_exists( 'get_field' ) ? get_field( 'video_source_type', $video_id ) : '';
	$source_type = is_string( $source_type ) ? trim( $source_type ) : '';

	if ( ! in_array( $source_type, array( 'vimeo', 'youtube', 'self_hosted', 'embed' ), true ) ) {
		return array();
	}

	$title   = get_the_title( $video_id );
	$title   = is_string( $title ) && '' !== trim( $title ) ? trim( $title ) : __( 'Video', 'apache-2026' );
	$caption = function_exists( 'get_field' ) ? get_field( 'video_caption', $video_id ) : '';
	$caption = is_string( $caption ) ? trim( $caption ) : '';
	$poster  = function_exists( 'get_field' ) ? get_field( 'video_poster_image', $video_id ) : array();
	$poster  = is_array( $poster ) ? $poster : array();

	$poster_url = isset( $poster['url'] ) && is_string( $poster['url'] ) ? trim( $poster['url'] ) : '';
	$poster_alt = isset( $poster['alt'] ) && is_string( $poster['alt'] ) ? trim( $poster['alt'] ) : '';

	if ( '' === $poster_alt ) {
		$poster_alt = $title;
	}

	$autoplay = function_exists( 'get_field' ) ? apache_2026_bool_from_mixed( get_field( 'video_autoplay', $video_id ) ) : false;
	$muted    = function_exists( 'get_field' ) ? apache_2026_bool_from_mixed( get_field( 'video_muted', $video_id ) ) : false;
	$loop     = function_exists( 'get_field' ) ? apache_2026_bool_from_mixed( get_field( 'video_loop', $video_id ) ) : false;
	$controls = function_exists( 'get_field' ) ? apache_2026_bool_from_mixed( get_field( 'video_controls', $video_id ) ) : true;

	if ( $autoplay ) {
		$muted = true;
	}

	$classes = array_merge(
		array(
			'apache-video',
			'apache-video--' . sanitize_html_class( $source_type ),
		),
		apache_2026_sanitize_video_classes( $args['class'] ?? array() )
	);

	$data = array(
		'video_id'   => $video_id,
		'post_title' => $title,
		'source_type'=> $source_type,
		'classes'    => $classes,
		'caption'    => '' !== $caption ? wp_kses_post( $caption ) : '',
		'poster'     => array(
			'url' => $poster_url,
			'alt' => $poster_alt,
		),
		'media'      => array(),
	);

	if ( 'vimeo' === $source_type ) {
		$vimeo_value = function_exists( 'get_field' ) ? get_field( 'vimeo_video_id', $video_id ) : '';
		$vimeo_data  = apache_2026_normalize_vimeo_video_data( $vimeo_value );
		$vimeo_id    = isset( $vimeo_data['id'] ) && is_string( $vimeo_data['id'] ) ? $vimeo_data['id'] : '';
		$vimeo_hash  = isset( $vimeo_data['hash'] ) && is_string( $vimeo_data['hash'] ) ? $vimeo_data['hash'] : '';

		if ( '' === $vimeo_id ) {
			return array();
		}

		$query_args = array_filter(
			array(
				'autoplay' => $autoplay ? '1' : '0',
				'muted'    => $muted ? '1' : '0',
				'loop'     => $loop ? '1' : '0',
				'controls' => '0',
				'api'      => '1',
				'badge'    => '0',
				'byline'   => '0',
				'portrait' => '0',
				'title'    => '0',
				'dnt'      => '1',
				'h'        => '' !== $vimeo_hash ? $vimeo_hash : null,
			)
		);

		$data['media'] = array(
			'iframe_src'   => add_query_arg( $query_args, 'https://player.vimeo.com/video/' . rawurlencode( $vimeo_id ) ),
			'iframe_allow' => 'autoplay; fullscreen; picture-in-picture',
			'player_url'   => add_query_arg(
				array_filter(
					array(
						'h' => '' !== $vimeo_hash ? $vimeo_hash : null,
					)
				),
				'https://player.vimeo.com/video/' . rawurlencode( $vimeo_id )
			),
			'autoplay'     => $autoplay,
			'muted'        => $muted,
			'loop'         => $loop,
		);

		return $data;
	}

	if ( 'youtube' === $source_type ) {
		$youtube_id = function_exists( 'get_field' ) ? get_field( 'youtube_video_id', $video_id ) : '';
		$youtube_id = apache_2026_normalize_youtube_video_id( $youtube_id );

		if ( '' === $youtube_id ) {
			return array();
		}

		$query_args = array_filter(
			array(
				'autoplay' => $autoplay ? '1' : '0',
				'mute'     => $muted ? '1' : '0',
				'loop'     => $loop ? '1' : '0',
				'playlist' => $loop ? $youtube_id : null,
				'rel'      => '0',
				'controls' => '0',
				'enablejsapi' => '1',
				'playsinline' => '1',
				'modestbranding' => '1',
			),
			static fn ( $value ) => null !== $value
		);

		$data['media'] = array(
			'iframe_src'   => add_query_arg( $query_args, 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $youtube_id ) ),
			'iframe_allow' => 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
		);

		return $data;
	}

	if ( 'self_hosted' === $source_type ) {
		$file = function_exists( 'get_field' ) ? get_field( 'self_hosted_video_file', $video_id ) : array();
		$file = is_array( $file ) ? $file : array();

		$file_url = isset( $file['url'] ) && is_string( $file['url'] ) ? trim( $file['url'] ) : '';
		$mime     = isset( $file['mime_type'] ) && is_string( $file['mime_type'] ) ? trim( $file['mime_type'] ) : '';

		if ( '' === $file_url ) {
			return array();
		}

		if ( '' === $mime ) {
			$checked = wp_check_filetype( $file_url );
			$mime    = isset( $checked['type'] ) && is_string( $checked['type'] ) ? $checked['type'] : '';
		}

		if ( ! in_array( $mime, array( 'video/mp4', 'video/webm' ), true ) ) {
			return array();
		}

		$data['media'] = array(
			'file_url' => $file_url,
			'mime'     => $mime,
			'poster'   => $poster_url,
			'autoplay' => $autoplay,
			'muted'    => $muted,
			'loop'     => $loop,
			'controls' => $controls,
		);

		return $data;
	}

	$embed_code = function_exists( 'get_field' ) ? get_field( 'custom_embed_code', $video_id ) : '';
	$embed_code = is_string( $embed_code ) ? trim( $embed_code ) : '';

	if ( '' === $embed_code ) {
		return array();
	}

	$sanitized_embed = wp_kses( $embed_code, apache_2026_video_embed_allowed_html() );

	if ( '' === trim( $sanitized_embed ) ) {
		return array();
	}

	$data['media'] = array(
		'embed_html' => $sanitized_embed,
	);

	return $data;
}

function apache_2026_render_video( int $video_id, array $args = array() ): string {
	$video_id = absint( $video_id );

	if ( $video_id <= 0 ) {
		return '';
	}

	$data = apache_2026_get_video_component_data( $video_id, $args );

	if ( empty( $data ) ) {
		if ( current_user_can( 'edit_post', $video_id ) ) {
			return '<p class="apache-video__notice">' . esc_html__( 'Video unavailable.', 'apache-2026' ) . '</p>';
		}

		return '';
	}

	$template = get_theme_file_path( 'template-parts/components/video/video.php' );

	if ( ! file_exists( $template ) ) {
		return '';
	}

	ob_start();
	include $template;
	return (string) ob_get_clean();
}
