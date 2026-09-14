<?php
/**
 * Popup post type registration.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_register_popup_post_type(): void {
	if ( post_type_exists( 'apache_popup' ) ) {
		return;
	}

	register_post_type(
		'apache_popup',
		array(
			'labels' => array(
				'name'                  => __( 'Apache Popups', 'apache-2026' ),
				'singular_name'         => __( 'Apache Popup', 'apache-2026' ),
				'menu_name'             => __( 'Apache Popups', 'apache-2026' ),
				'name_admin_bar'        => __( 'Apache Popup', 'apache-2026' ),
				'add_new'               => __( 'Add New', 'apache-2026' ),
				'add_new_item'          => __( 'Add New Apache Popup', 'apache-2026' ),
				'edit_item'             => __( 'Edit Apache Popup', 'apache-2026' ),
				'new_item'              => __( 'New Apache Popup', 'apache-2026' ),
				'view_item'             => __( 'View Apache Popup', 'apache-2026' ),
				'search_items'          => __( 'Search Apache Popups', 'apache-2026' ),
				'not_found'             => __( 'No Apache popups found.', 'apache-2026' ),
				'not_found_in_trash'    => __( 'No Apache popups found in Trash.', 'apache-2026' ),
				'all_items'             => __( 'All Apache Popups', 'apache-2026' ),
				'archives'              => __( 'Apache Popup Archives', 'apache-2026' ),
				'attributes'            => __( 'Apache Popup Attributes', 'apache-2026' ),
				'insert_into_item'      => __( 'Insert into Apache popup', 'apache-2026' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Apache popup', 'apache-2026' ),
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
			'menu_icon'           => 'dashicons-welcome-view-site',
			'menu_position'       => 27,
			'supports'            => array( 'title' ),
		)
	);
}
add_action( 'init', 'apache_2026_register_popup_post_type' );
