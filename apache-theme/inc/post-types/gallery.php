<?php
/**
 * Gallery post type registration.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_register_gallery_post_type(): void {
	register_post_type(
		'gallery',
		array(
			'labels' => array(
				'name'                  => __( 'Galleries', 'apache-2026' ),
				'singular_name'         => __( 'Gallery', 'apache-2026' ),
				'menu_name'             => __( 'Galleries', 'apache-2026' ),
				'name_admin_bar'        => __( 'Gallery', 'apache-2026' ),
				'add_new'               => __( 'Add New', 'apache-2026' ),
				'add_new_item'          => __( 'Add New Gallery', 'apache-2026' ),
				'edit_item'             => __( 'Edit Gallery', 'apache-2026' ),
				'new_item'              => __( 'New Gallery', 'apache-2026' ),
				'view_item'             => __( 'View Gallery', 'apache-2026' ),
				'search_items'          => __( 'Search Galleries', 'apache-2026' ),
				'not_found'             => __( 'No galleries found.', 'apache-2026' ),
				'not_found_in_trash'    => __( 'No galleries found in Trash.', 'apache-2026' ),
				'all_items'             => __( 'All Galleries', 'apache-2026' ),
				'archives'              => __( 'Gallery Archives', 'apache-2026' ),
				'attributes'            => __( 'Gallery Attributes', 'apache-2026' ),
				'insert_into_item'      => __( 'Insert into gallery', 'apache-2026' ),
				'uploaded_to_this_item' => __( 'Uploaded to this gallery', 'apache-2026' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'menu_icon'           => 'dashicons-format-gallery',
			'menu_position'       => 25,
			'supports'            => array( 'title', 'thumbnail', 'editor' ),
		)
	);
}
add_action( 'init', 'apache_2026_register_gallery_post_type' );
