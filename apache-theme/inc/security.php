<?php
/**
 * Theme-level security hardening.
 *
 * Conservative, WordPress-standard protections that should not interfere with
 * public rendering, embeds, media, or existing flexible content modules.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_register_security_hooks(): void {
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
	remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
	remove_action( 'wp_head', 'index_rel_link' );
	remove_action( 'wp_head', 'parent_post_rel_link', 10 );
	remove_action( 'wp_head', 'start_post_rel_link', 10 );
	remove_action( 'template_redirect', 'rest_output_link_header', 11 );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
add_action( 'after_setup_theme', 'apache_2026_register_security_hooks', 20 );

function apache_2026_remove_generator_tag( string $generator ): string {
	return '';
}
add_filter( 'the_generator', 'apache_2026_remove_generator_tag' );

function apache_2026_disable_xmlrpc( bool $is_enabled ): bool {
	return false;
}
add_filter( 'xmlrpc_enabled', 'apache_2026_disable_xmlrpc' );

function apache_2026_remove_x_pingback_header( array $headers ): array {
	unset( $headers['X-Pingback'] );

	return $headers;
}
add_filter( 'wp_headers', 'apache_2026_remove_x_pingback_header' );

function apache_2026_strip_wp_version_query_arg( string $src ): string {
	if ( '' === $src ) {
		return $src;
	}

	$query = wp_parse_url( $src, PHP_URL_QUERY );

	if ( ! is_string( $query ) || '' === $query ) {
		return $src;
	}

	parse_str( $query, $query_args );

	if ( empty( $query_args['ver'] ) ) {
		return $src;
	}

	$wp_version = get_bloginfo( 'version' );

	if ( ! is_string( $wp_version ) || (string) $query_args['ver'] !== $wp_version ) {
		return $src;
	}

	return remove_query_arg( 'ver', $src );
}
add_filter( 'script_loader_src', 'apache_2026_strip_wp_version_query_arg', 20 );
add_filter( 'style_loader_src', 'apache_2026_strip_wp_version_query_arg', 20 );

function apache_2026_disable_public_rest_user_endpoints( array $endpoints ): array {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}

	unset( $endpoints['/wp/v2/users'] );
	unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );

	return $endpoints;
}
add_filter( 'rest_endpoints', 'apache_2026_disable_public_rest_user_endpoints' );

function apache_2026_disable_author_archives(): void {
	if ( is_admin() || ! is_author() ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'apache_2026_disable_author_archives' );

function apache_2026_send_security_headers(): void {
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Content-Security-Policy-Report-Only: ' . apache_2026_get_csp_report_only_policy() );
}
add_action( 'send_headers', 'apache_2026_send_security_headers' );

function apache_2026_get_csp_report_only_policy(): string {
	$directives = array(
		"default-src 'self'",
		"base-uri 'self'",
		"object-src 'none'",
		"frame-ancestors 'self'",
		"form-action 'self'",
		"img-src 'self' data: blob: https:",
		"media-src 'self' blob: https:",
		"font-src 'self' data: https://use.typekit.net https://p.typekit.net",
		"style-src 'self' 'unsafe-inline' https://use.typekit.net https://p.typekit.net https://cdnjs.cloudflare.com",
		"script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://static.hotjar.com https://script.hotjar.com https://player.vimeo.com https://www.youtube.com https://www.youtube-nocookie.com",
		"connect-src 'self' https://www.google-analytics.com https://region1.google-analytics.com https://*.hotjar.com wss://*.hotjar.com https://player.vimeo.com https://vimeo.com https://*.vimeo.com https://www.youtube.com https://www.youtube-nocookie.com",
		"frame-src 'self' https://player.vimeo.com https://www.youtube.com https://www.youtube-nocookie.com",
	);

	return implode( '; ', $directives );
}

function apache_2026_sanitize_link_target( mixed $target ): string {
	$target = is_string( $target ) ? trim( $target ) : '';

	if ( '' === $target ) {
		return '_self';
	}

	$allowed_targets = array( '_self', '_blank', '_parent', '_top' );

	return in_array( $target, $allowed_targets, true ) ? $target : '_self';
}

function apache_2026_link_rel( mixed $target ): string {
	return '_blank' === apache_2026_sanitize_link_target( $target ) ? 'noopener noreferrer' : '';
}
