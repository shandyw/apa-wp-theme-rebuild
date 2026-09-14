<?php
/**
 * Sitemap shortcode registration.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_limited_sitemap_markup(): string {
	$excluded_ids = array();

	foreach ( array( 'sitemap', 'modules', 'sample-page' ) as $excluded_slug ) {
		$excluded_page = get_page_by_path( $excluded_slug );

		if ( $excluded_page instanceof WP_Post ) {
			$excluded_ids[] = (int) $excluded_page->ID;
		}
	}

	$excluded_ids = array_values( array_unique( array_filter( $excluded_ids ) ) );
	$list_markup  = wp_list_pages(
		array(
			'echo'        => false,
			'exclude'     => implode( ',', $excluded_ids ),
			'title_li'    => '',
			'sort_column' => 'menu_order,post_title',
		)
	);

	if ( ! is_string( $list_markup ) || '' === trim( $list_markup ) ) {
		return '';
	}

	return sprintf(
		'<div class="apache-sitemap apache-sitemap--limited"><ul class="apache-sitemap__list">%s</ul></div>',
		$list_markup
	);
}

function apache_2026_sitemap_shortcode_post_types( string $include = '', string $exclude = '' ): array {
	$post_types = get_post_types(
		array(
			'public' => true,
		),
		'objects'
	);

	$default_excluded = array(
		'attachment',
		'apache_popup',
		'apache_video',
		'acf-field',
		'acf-field-group',
	);

	$include_types = array_filter( array_map( 'sanitize_key', array_map( 'trim', explode( ',', $include ) ) ) );
	$exclude_types = array_unique(
		array_merge(
			$default_excluded,
			array_filter( array_map( 'sanitize_key', array_map( 'trim', explode( ',', $exclude ) ) ) )
		)
	);

	$selected = array();

	foreach ( $post_types as $post_type => $object ) {
		if ( in_array( $post_type, $exclude_types, true ) ) {
			continue;
		}

		if ( ! empty( $include_types ) && ! in_array( $post_type, $include_types, true ) ) {
			continue;
		}

		$selected[ $post_type ] = $object;
	}

	if ( empty( $include_types ) ) {
		return $selected;
	}

	$ordered = array();

	foreach ( $include_types as $post_type ) {
		if ( isset( $selected[ $post_type ] ) ) {
			$ordered[ $post_type ] = $selected[ $post_type ];
		}
	}

	return $ordered;
}

function apache_2026_sitemap_section_markup( WP_Post_Type $post_type ): string {
	$post_type_name = $post_type->name;
	$label          = isset( $post_type->labels->name ) ? (string) $post_type->labels->name : ucfirst( $post_type_name );
	$is_hierarchical = ! empty( $post_type->hierarchical );

	if ( $is_hierarchical ) {
		$list = wp_list_pages(
			array(
				'title_li'    => '',
				'echo'        => 0,
				'post_type'   => $post_type_name,
				'sort_column' => 'menu_order,post_title',
			)
		);
	} else {
		$posts = get_posts(
			array(
				'post_type'      => $post_type_name,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$list_items = array();

		foreach ( $posts as $post ) {
			if ( ! $post instanceof WP_Post ) {
				continue;
			}

			$permalink = get_permalink( $post );
			$title     = get_the_title( $post );

			if ( ! $permalink || '' === $title ) {
				continue;
			}

			$list_items[] = sprintf(
				'<li class="apache-sitemap__item"><a href="%1$s">%2$s</a></li>',
				esc_url( $permalink ),
				esc_html( $title )
			);
		}

		$list = implode( '', $list_items );
	}

	if ( '' === trim( $list ) ) {
		return '';
	}

	return sprintf(
		'<section class="apache-sitemap__section apache-sitemap__section--%1$s"><h2 class="apache-sitemap__title">%2$s</h2><ul class="apache-sitemap__list">%3$s</ul></section>',
		esc_attr( sanitize_html_class( $post_type_name ) ),
		esc_html( $label ),
		$list
	);
}

function apache_2026_sitemap_shortcode( array $atts = array() ): string {
	$atts = shortcode_atts(
		array(
			'include' => '',
			'exclude' => '',
			'class'   => '',
		),
		$atts,
		'apache_sitemap'
	);

	$post_types = apache_2026_sitemap_shortcode_post_types(
		(string) $atts['include'],
		(string) $atts['exclude']
	);

	if ( empty( $post_types ) ) {
		return current_user_can( 'edit_posts' ) ? '<p class="apache-sitemap__notice">' . esc_html__( 'No sitemap content found.', 'apache-2026' ) . '</p>' : '';
	}

	$sections = array();

	foreach ( $post_types as $post_type ) {
		if ( $post_type instanceof WP_Post_Type ) {
			$section = apache_2026_sitemap_section_markup( $post_type );

			if ( '' !== $section ) {
				$sections[] = $section;
			}
		}
	}

	if ( empty( $sections ) ) {
		return current_user_can( 'edit_posts' ) ? '<p class="apache-sitemap__notice">' . esc_html__( 'No sitemap content found.', 'apache-2026' ) . '</p>' : '';
	}

	$classes = array_filter(
		array(
			'apache-sitemap',
			is_string( $atts['class'] ) ? sanitize_html_class( $atts['class'] ) : '',
		)
	);

	return sprintf(
		'<div class="%1$s">%2$s</div>',
		esc_attr( implode( ' ', $classes ) ),
		implode( '', $sections )
	);
}
add_shortcode( 'apache_sitemap', 'apache_2026_sitemap_shortcode' );

function apache_2026_limited_sitemap_shortcode( array $atts = array() ): string {
	$atts = shortcode_atts(
		array(
			'class' => '',
		),
		$atts,
		'apache_limited_sitemap'
	);

	$markup = apache_2026_limited_sitemap_markup();

	if ( '' === $markup ) {
		return current_user_can( 'edit_posts' ) ? '<p class="apache-sitemap__notice">' . esc_html__( 'No sitemap content found.', 'apache-2026' ) . '</p>' : '';
	}

	$custom_class = is_string( $atts['class'] ) ? trim( $atts['class'] ) : '';

	if ( '' === $custom_class ) {
		return $markup;
	}

	return preg_replace(
		'/class=(["\'])([^"\']*)\1/',
		'class="$2 ' . esc_attr( sanitize_html_class( $custom_class ) ) . '"',
		$markup,
		1
	) ?: $markup;
}
add_shortcode( 'apache_limited_sitemap', 'apache_2026_limited_sitemap_shortcode' );
