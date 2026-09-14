<?php
/**
 * Legacy-compatible behavior carried forward from the Apache theme.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_content_width(): void {
	$GLOBALS['content_width'] = $GLOBALS['content_width'] ?? 1920;
}
add_action( 'after_setup_theme', 'apache_2026_content_width', 0 );

function apache_2026_document_title_separator( string $separator ): string {
	return '|';
}
add_filter( 'document_title_separator', 'apache_2026_document_title_separator' );

function apache_2026_empty_title_fallback( string $title ): string {
	return '' === $title ? '...' : $title;
}
add_filter( 'the_title', 'apache_2026_empty_title_fallback' );

function apache_2026_content_more_link(): string {
	if ( is_admin() ) {
		return '';
	}

	return sprintf(
		' <a href="%s" class="more-link">...</a>',
		esc_url( get_permalink() )
	);
}
add_filter( 'the_content_more_link', 'apache_2026_content_more_link' );

function apache_2026_excerpt_more_link( string $more ): string {
	if ( is_admin() ) {
		return $more;
	}

	return sprintf(
		' <a href="%s" class="more-link">...</a>',
		esc_url( get_permalink() )
	);
}
add_filter( 'excerpt_more', 'apache_2026_excerpt_more_link' );

function apache_2026_disable_comments_post_types(): void {
	foreach ( get_post_types_by_support( 'comments' ) as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'apache_2026_disable_comments_post_types' );

function apache_2026_disable_comments_open( bool $open ): bool {
	return false;
}
add_filter( 'comments_open', 'apache_2026_disable_comments_open', 20 );
add_filter( 'pings_open', 'apache_2026_disable_comments_open', 20 );

function apache_2026_hide_comments( array $comments ): array {
	return array();
}
add_filter( 'comments_array', 'apache_2026_hide_comments', 10, 1 );

function apache_2026_remove_comments_admin_menu(): void {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'apache_2026_remove_comments_admin_menu' );

function apache_2026_redirect_comments_admin(): void {
	global $pagenow;

	if ( 'edit-comments.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'apache_2026_redirect_comments_admin' );

function apache_2026_remove_comments_admin_bar( WP_Admin_Bar $admin_bar ): void {
	$admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'apache_2026_remove_comments_admin_bar', 999 );

function apache_2026_single_slug_body_class( array $classes ): array {
	if ( ! is_single() ) {
		return $classes;
	}

	$post = get_post();

	if ( $post instanceof WP_Post ) {
		$classes[] = sanitize_html_class( $post->post_type . '-slug-' . $post->post_name );
	}

	return $classes;
}
add_filter( 'body_class', 'apache_2026_single_slug_body_class' );

function apache_2026_page_surface_body_class( array $classes ): array {
	if ( ! is_page() || ! function_exists( 'get_field' ) ) {
		return $classes;
	}

	$surface = get_field( 'page_background_color' );
	$surface = is_string( $surface ) ? sanitize_key( $surface ) : 'white';
	$surface = in_array( $surface, array( 'white', 'light_blue' ), true ) ? $surface : 'white';

	$classes[] = 'page-surface-' . $surface;

	return $classes;
}
add_filter( 'body_class', 'apache_2026_page_surface_body_class' );

function apache_2026_rest_cors_headers( $served ) {
	$origin_url = '*';

	if ( defined( 'ENVIRONMENT' ) && 'production' === ENVIRONMENT ) {
		$origin_url = 'https://apachesite.wpengine.com';
	}

	header( 'Access-Control-Allow-Origin: ' . $origin_url );
	header( 'Access-Control-Allow-Methods: GET' );

	if ( '*' !== $origin_url ) {
		header( 'Access-Control-Allow-Credentials: true' );
	}

	return $served;
}

function apache_2026_register_rest_cors_headers(): void {
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_pre_serve_request', 'apache_2026_rest_cors_headers' );
}
add_action( 'rest_api_init', 'apache_2026_register_rest_cors_headers', 15 );
