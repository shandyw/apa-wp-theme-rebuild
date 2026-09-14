<?php
/**
 * Video post type registration.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_register_video_post_type(): void {
	register_post_type(
		'apache_video',
		array(
			'labels' => array(
				'name'                  => __( 'Videos', 'apache-2026' ),
				'singular_name'         => __( 'Video', 'apache-2026' ),
				'menu_name'             => __( 'Videos', 'apache-2026' ),
				'name_admin_bar'        => __( 'Video', 'apache-2026' ),
				'add_new'               => __( 'Add New', 'apache-2026' ),
				'add_new_item'          => __( 'Add New Video', 'apache-2026' ),
				'edit_item'             => __( 'Edit Video', 'apache-2026' ),
				'new_item'              => __( 'New Video', 'apache-2026' ),
				'view_item'             => __( 'View Video', 'apache-2026' ),
				'search_items'          => __( 'Search Videos', 'apache-2026' ),
				'not_found'             => __( 'No videos found.', 'apache-2026' ),
				'not_found_in_trash'    => __( 'No videos found in Trash.', 'apache-2026' ),
				'all_items'             => __( 'All Videos', 'apache-2026' ),
				'archives'              => __( 'Video Archives', 'apache-2026' ),
				'attributes'            => __( 'Video Attributes', 'apache-2026' ),
				'insert_into_item'      => __( 'Insert into video', 'apache-2026' ),
				'uploaded_to_this_item' => __( 'Uploaded to this video', 'apache-2026' ),
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
			'menu_icon'           => 'dashicons-video-alt3',
			'menu_position'       => 26,
			'supports'            => array( 'title', 'thumbnail' ),
		)
	);
}
add_action( 'init', 'apache_2026_register_video_post_type' );
