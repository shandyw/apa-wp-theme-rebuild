<?php
/**
 * ACF field group usage reporting screen.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_register_field_group_usage_page(): void {
	add_management_page(
		__( 'Field Group Usage', 'apache-2026' ),
		__( 'Field Group Usage', 'apache-2026' ),
		'manage_options',
		'apache-2026-field-group-usage',
		'apache_2026_render_field_group_usage_page'
	);
}
add_action( 'admin_menu', 'apache_2026_register_field_group_usage_page' );

function apache_2026_get_field_group_usage_cache_key(): string {
	return 'apache_2026_acf_field_group_usage_cache';
}

function apache_2026_refresh_field_group_usage_report(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to refresh this report.', 'apache-2026' ) );
	}

	check_admin_referer( 'apache_2026_refresh_field_group_usage' );

	delete_transient( apache_2026_get_field_group_usage_cache_key() );

	$redirect_url = add_query_arg(
		array(
			'page'    => 'apache-2026-field-group-usage',
			'refreshed' => '1',
		),
		admin_url( 'tools.php' )
	);

	wp_safe_redirect( $redirect_url );
	exit;
}
add_action( 'admin_post_apache_2026_refresh_field_group_usage', 'apache_2026_refresh_field_group_usage_report' );

function apache_2026_get_page_edit_link( int $post_id ): string {
	$edit_link = get_edit_post_link( $post_id );

	return is_string( $edit_link ) ? $edit_link : '';
}

function apache_2026_format_field_group_location_rules( array $location ): array {
	$rules = array();

	foreach ( $location as $location_group ) {
		if ( ! is_array( $location_group ) ) {
			continue;
		}

		$parts = array();

		foreach ( $location_group as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}

			$param    = isset( $rule['param'] ) && is_string( $rule['param'] ) ? trim( $rule['param'] ) : '';
			$operator = isset( $rule['operator'] ) && is_string( $rule['operator'] ) ? trim( $rule['operator'] ) : '';
			$value    = isset( $rule['value'] ) && is_scalar( $rule['value'] ) ? trim( (string) $rule['value'] ) : '';

			if ( '' === $param || '' === $operator ) {
				continue;
			}

			$parts[] = sprintf( '%s %s %s', $param, $operator, $value );
		}

		if ( ! empty( $parts ) ) {
			$rules[] = implode( ' AND ', $parts );
		}
	}

	return $rules;
}

function apache_2026_extract_flexible_content_fields( array $fields, string $parent_name = '' ): array {
	$flexible_fields = array();

	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		$field_type = isset( $field['type'] ) && is_string( $field['type'] ) ? trim( $field['type'] ) : '';
		$field_key  = isset( $field['key'] ) && is_string( $field['key'] ) ? trim( $field['key'] ) : '';
		$field_name = isset( $field['name'] ) && is_string( $field['name'] ) ? trim( $field['name'] ) : '';
		$field_label = isset( $field['label'] ) && is_string( $field['label'] ) ? trim( $field['label'] ) : '';
		$full_name  = '' !== $parent_name && '' !== $field_name ? $parent_name . '_' . $field_name : $field_name;

		if ( 'flexible_content' === $field_type ) {
			$layouts = array();

			if ( ! empty( $field['layouts'] ) && is_array( $field['layouts'] ) ) {
				foreach ( $field['layouts'] as $layout ) {
					if ( ! is_array( $layout ) ) {
						continue;
					}

					$layouts[] = array(
						'label' => isset( $layout['label'] ) && is_string( $layout['label'] ) ? trim( $layout['label'] ) : '',
						'name'  => isset( $layout['name'] ) && is_string( $layout['name'] ) ? trim( $layout['name'] ) : '',
						'key'   => isset( $layout['key'] ) && is_string( $layout['key'] ) ? trim( $layout['key'] ) : '',
					);
				}
			}

			$flexible_fields[] = array(
				'label'   => $field_label,
				'name'    => $field_name,
				'full_name' => $full_name,
				'key'     => $field_key,
				'layouts' => $layouts,
			);
		}

		$nested_fields = array();

		if ( ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
			$nested_fields = $field['sub_fields'];
		} elseif ( ! empty( $field['layouts'] ) && is_array( $field['layouts'] ) ) {
			foreach ( $field['layouts'] as $layout ) {
				if ( ! is_array( $layout ) || empty( $layout['sub_fields'] ) || ! is_array( $layout['sub_fields'] ) ) {
					continue;
				}

				$nested_fields = array_merge( $nested_fields, $layout['sub_fields'] );
			}
		}

		if ( ! empty( $nested_fields ) ) {
			$flexible_fields = array_merge(
				$flexible_fields,
				apache_2026_extract_flexible_content_fields( $nested_fields, $full_name )
			);
		}
	}

	return $flexible_fields;
}

function apache_2026_field_has_saved_value( int $post_id, array $field, string $parent_name = '' ): bool {
	$field_type = isset( $field['type'] ) && is_string( $field['type'] ) ? trim( $field['type'] ) : '';
	$field_name = isset( $field['name'] ) && is_string( $field['name'] ) ? trim( $field['name'] ) : '';

	if ( '' === $field_name ) {
		return false;
	}

	$meta_name = '' !== $parent_name ? $parent_name . '_' . $field_name : $field_name;

	if ( 'group' === $field_type && ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
		foreach ( $field['sub_fields'] as $sub_field ) {
			if ( is_array( $sub_field ) && apache_2026_field_has_saved_value( $post_id, $sub_field, $meta_name ) ) {
				return true;
			}
		}

		return false;
	}

	if ( in_array( $field_type, array( 'repeater', 'flexible_content' ), true ) ) {
		$raw_value = get_post_meta( $post_id, $meta_name, true );

		if ( is_array( $raw_value ) ) {
			return ! empty( $raw_value );
		}

		$row_count = absint( $raw_value );

		return $row_count > 0;
	}

	return metadata_exists( 'post', $post_id, $meta_name );
}

function apache_2026_get_flexible_field_usage( int $post_id, array $flexible_field ): ?array {
	$flex_name = isset( $flexible_field['full_name'] ) && is_string( $flexible_field['full_name'] ) ? trim( $flexible_field['full_name'] ) : '';

	if ( '' === $flex_name ) {
		return null;
	}

	$raw_value = get_post_meta( $post_id, $flex_name, true );

	if ( is_array( $raw_value ) ) {
		$layouts_used = array();

		foreach ( $raw_value as $layout_name ) {
			if ( is_string( $layout_name ) && '' !== trim( $layout_name ) ) {
				$layouts_used[] = trim( $layout_name );
			}
		}

		$layouts_used = array_values( array_unique( $layouts_used ) );

		if ( empty( $layouts_used ) ) {
			return null;
		}

		return array(
			'label'        => isset( $flexible_field['label'] ) && is_string( $flexible_field['label'] ) ? trim( $flexible_field['label'] ) : '',
			'name'         => isset( $flexible_field['name'] ) && is_string( $flexible_field['name'] ) ? trim( $flexible_field['name'] ) : '',
			'full_name'    => $flex_name,
			'row_count'    => count( $raw_value ),
			'layouts_used' => $layouts_used,
		);
	}

	$row_count = absint( $raw_value );

	if ( $row_count <= 0 ) {
		return null;
	}

	$layouts_used = array();

	for ( $index = 0; $index < $row_count; $index++ ) {
		$layout_name = get_post_meta( $post_id, $flex_name . '_' . $index . '_acf_fc_layout', true );

		if ( is_string( $layout_name ) && '' !== trim( $layout_name ) ) {
			$layouts_used[] = trim( $layout_name );
		}
	}

	return array(
		'label'        => isset( $flexible_field['label'] ) && is_string( $flexible_field['label'] ) ? trim( $flexible_field['label'] ) : '',
		'name'         => isset( $flexible_field['name'] ) && is_string( $flexible_field['name'] ) ? trim( $flexible_field['name'] ) : '',
		'full_name'    => $flex_name,
		'row_count'    => $row_count,
		'layouts_used' => array_values( array_unique( array_filter( $layouts_used ) ) ),
	);
}

function apache_2026_scan_page_acf_usage( array $group, array $fields, array $pages, array $page_group_map, array $flexible_fields ): array {
	$group_key = isset( $group['key'] ) && is_string( $group['key'] ) ? trim( $group['key'] ) : '';

	if ( '' === $group_key ) {
		return array();
	}

	$used_pages = array();

	foreach ( $pages as $page ) {
		if ( ! $page instanceof WP_Post ) {
			continue;
		}

		$page_id = (int) $page->ID;
		$applies = isset( $page_group_map[ $page_id ] ) && in_array( $group_key, $page_group_map[ $page_id ], true );
		$group_used = false;
		$page_flexible_usage = array();

		foreach ( $flexible_fields as $flexible_field ) {
			$usage = apache_2026_get_flexible_field_usage( $page_id, $flexible_field );

			if ( null === $usage ) {
				continue;
			}

			$page_flexible_usage[] = $usage;

			$group_used = true;
		}

		if ( ! $group_used ) {
			foreach ( $fields as $field ) {
				if ( is_array( $field ) && apache_2026_field_has_saved_value( $page_id, $field ) ) {
					$group_used = true;
					break;
				}
			}
		}

		if ( ! $group_used && ! $applies ) {
			continue;
		}

		if ( ! $group_used ) {
			continue;
		}

		$used_pages[] = array(
			'id'             => $page_id,
			'title'          => get_the_title( $page_id ),
			'status'         => get_post_status( $page_id ),
			'edit_link'      => apache_2026_get_page_edit_link( $page_id ),
			'flexible_usage' => $page_flexible_usage,
		);
	}

	return $used_pages;
}

function apache_2026_scan_acf_field_groups(): array {
	if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
		return array();
	}

	$pages = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$page_group_map = array();

	foreach ( $pages as $page ) {
		if ( ! $page instanceof WP_Post ) {
			continue;
		}

		$applicable_groups = acf_get_field_groups(
			array(
				'post_id' => $page->ID,
			)
		);

		$page_group_map[ (int) $page->ID ] = array();

		if ( is_array( $applicable_groups ) ) {
			foreach ( $applicable_groups as $applicable_group ) {
				if ( isset( $applicable_group['key'] ) && is_string( $applicable_group['key'] ) && '' !== trim( $applicable_group['key'] ) ) {
					$page_group_map[ (int) $page->ID ][] = trim( $applicable_group['key'] );
				}
			}
		}
	}

	$report_groups = array();
	$field_groups  = acf_get_field_groups();

	foreach ( $field_groups as $group ) {
		if ( ! is_array( $group ) ) {
			continue;
		}

		$fields = acf_get_fields( $group );
		$fields = is_array( $fields ) ? $fields : array();

		$flexible_fields = apache_2026_extract_flexible_content_fields( $fields );

		$report_groups[] = array(
			'title'            => isset( $group['title'] ) && is_string( $group['title'] ) ? trim( $group['title'] ) : '',
			'key'              => isset( $group['key'] ) && is_string( $group['key'] ) ? trim( $group['key'] ) : '',
			'location_rules'   => apache_2026_format_field_group_location_rules( isset( $group['location'] ) && is_array( $group['location'] ) ? $group['location'] : array() ),
			'has_flexible'     => ! empty( $flexible_fields ),
			'flexible_fields'  => $flexible_fields,
			'used_pages'       => apache_2026_scan_page_acf_usage( $group, $fields, $pages, $page_group_map, $flexible_fields ),
		);
	}

	return $report_groups;
}

function apache_2026_get_field_group_usage_report( bool $force_refresh = false ): array {
	$cache_key = apache_2026_get_field_group_usage_cache_key();

	if ( ! $force_refresh ) {
		$cached_report = get_transient( $cache_key );

		if ( is_array( $cached_report ) ) {
			return $cached_report;
		}
	}

	$report = array(
		'acf_active'   => function_exists( 'acf_get_field_groups' ) && function_exists( 'acf_get_fields' ),
		'generated_at' => time(),
		'groups'       => array(),
	);

	if ( $report['acf_active'] ) {
		$report['groups'] = apache_2026_scan_acf_field_groups();
	}

	set_transient( $cache_key, $report, HOUR_IN_SECONDS );

	return $report;
}

function apache_2026_get_group_layout_usage_index( array $group ): array {
	$layout_index = array();

	if ( ! empty( $group['flexible_fields'] ) && is_array( $group['flexible_fields'] ) ) {
		foreach ( $group['flexible_fields'] as $flexible_field ) {
			if ( ! is_array( $flexible_field ) || empty( $flexible_field['layouts'] ) || ! is_array( $flexible_field['layouts'] ) ) {
				continue;
			}

			foreach ( $flexible_field['layouts'] as $layout ) {
				if ( ! is_array( $layout ) ) {
					continue;
				}

				$layout_name  = isset( $layout['name'] ) && is_string( $layout['name'] ) ? trim( $layout['name'] ) : '';
				$layout_label = isset( $layout['label'] ) && is_string( $layout['label'] ) ? trim( $layout['label'] ) : '';

				if ( '' === $layout_name ) {
					continue;
				}

				if ( ! isset( $layout_index[ $layout_name ] ) ) {
					$layout_index[ $layout_name ] = array(
						'label' => '' !== $layout_label ? $layout_label : $layout_name,
						'name'  => $layout_name,
						'pages' => array(),
					);
				}
			}
		}
	}

	if ( ! empty( $group['used_pages'] ) && is_array( $group['used_pages'] ) ) {
		foreach ( $group['used_pages'] as $page ) {
			if ( ! is_array( $page ) || empty( $page['flexible_usage'] ) || ! is_array( $page['flexible_usage'] ) ) {
				continue;
			}

			foreach ( $page['flexible_usage'] as $usage ) {
				if ( ! is_array( $usage ) || empty( $usage['layouts_used'] ) || ! is_array( $usage['layouts_used'] ) ) {
					continue;
				}

				foreach ( $usage['layouts_used'] as $layout_name ) {
					if ( ! is_string( $layout_name ) || '' === trim( $layout_name ) ) {
						continue;
					}

					$layout_name = trim( $layout_name );

					if ( ! isset( $layout_index[ $layout_name ] ) ) {
						$layout_index[ $layout_name ] = array(
							'label' => $layout_name,
							'name'  => $layout_name,
							'pages' => array(),
						);
					}

					$layout_index[ $layout_name ]['pages'][ (int) $page['id'] ] = array(
						'id'        => (int) $page['id'],
						'title'     => isset( $page['title'] ) && is_string( $page['title'] ) ? $page['title'] : '',
						'status'    => isset( $page['status'] ) && is_string( $page['status'] ) ? $page['status'] : '',
						'edit_link' => isset( $page['edit_link'] ) && is_string( $page['edit_link'] ) ? $page['edit_link'] : '',
					);
				}
			}
		}
	}

	ksort( $layout_index );

	return $layout_index;
}

function apache_2026_render_field_group_usage_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'apache-2026' ) );
	}

	$report = apache_2026_get_field_group_usage_report();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Field Group Usage', 'apache-2026' ); ?></h1>

		<p><?php esc_html_e( 'This report scans ACF field groups, identifies flexible content layouts, and lists published pages with saved field usage. Results are cached for one hour unless refreshed.', 'apache-2026' ); ?></p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="apache_2026_refresh_field_group_usage">
			<?php wp_nonce_field( 'apache_2026_refresh_field_group_usage' ); ?>
			<?php submit_button( __( 'Refresh Scan', 'apache-2026' ), 'button button-primary', 'submit', false ); ?>
		</form>

		<?php if ( ! empty( $_GET['refreshed'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Field group usage report refreshed.', 'apache-2026' ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! $report['acf_active'] ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'ACF is not active. Field group usage cannot be scanned.', 'apache-2026' ); ?></p></div>
			<?php
			return;
		endif;
		?>

		<p>
			<strong><?php esc_html_e( 'Last generated:', 'apache-2026' ); ?></strong>
			<?php echo esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $report['generated_at'] ) ); ?>
		</p>

		<?php if ( empty( $report['groups'] ) ) : ?>
			<p><?php esc_html_e( 'No ACF field groups were found.', 'apache-2026' ); ?></p>
			<?php
			return;
		endif;
		?>

		<div class="widefat striped" style="padding: 16px 20px;">
			<?php foreach ( $report['groups'] as $group ) : ?>
				<?php $layout_usage_index = apache_2026_get_group_layout_usage_index( $group ); ?>
				<details style="padding: 0 0 20px; margin: 0 0 20px; border-bottom: 1px solid #dcdcde;">
					<summary style="cursor: pointer; font-size: 18px; font-weight: 600; margin: 0 0 8px;">
						<?php echo esc_html( $group['title'] ? $group['title'] : __( '(Untitled field group)', 'apache-2026' ) ); ?>
					</summary>

					<div style="padding-top: 12px;">
						<p style="margin: 0 0 8px;">
							<code><?php echo esc_html( $group['key'] ); ?></code>
						</p>

						<?php if ( ! empty( $group['location_rules'] ) ) : ?>
							<p style="margin: 0 0 8px;">
								<strong><?php esc_html_e( 'Location Rules:', 'apache-2026' ); ?></strong>
							</p>
							<ul style="margin-top: 0;">
								<?php foreach ( $group['location_rules'] as $rule ) : ?>
									<li><?php echo esc_html( $rule ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( $group['has_flexible'] ) : ?>
							<ul style="margin-top: 0;">
								<?php foreach ( $layout_usage_index as $layout_name => $layout_data ) : ?>
									<li>
										<strong><?php echo esc_html( $layout_data['label'] ); ?></strong>
										<?php if ( $layout_data['label'] !== $layout_name ) : ?>
											<br><code><?php echo esc_html( $layout_name ); ?></code>
										<?php endif; ?>
										<?php if ( ! empty( $layout_data['pages'] ) ) : ?>
											<ul>
												<?php foreach ( $layout_data['pages'] as $layout_page ) : ?>
													<li>
														<?php echo esc_html( $layout_page['title'] ? $layout_page['title'] : __( '(No title)', 'apache-2026' ) ); ?>
														<?php if ( ! empty( $layout_page['edit_link'] ) ) : ?>
															(<a href="<?php echo esc_url( $layout_page['edit_link'] ); ?>"><?php esc_html_e( 'Edit', 'apache-2026' ); ?></a>)
														<?php endif; ?>
													</li>
												<?php endforeach; ?>
											</ul>
										<?php else : ?>
											<ul>
												<li><?php esc_html_e( 'No page usage detected.', 'apache-2026' ); ?></li>
											</ul>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php elseif ( ! empty( $group['used_pages'] ) ) : ?>
							<ul style="margin-top: 0;">
								<?php foreach ( $group['used_pages'] as $page ) : ?>
									<li>
										<?php echo esc_html( $page['title'] ? $page['title'] : __( '(No title)', 'apache-2026' ) ); ?>
										<?php if ( ! empty( $page['edit_link'] ) ) : ?>
											(<a href="<?php echo esc_url( $page['edit_link'] ); ?>"><?php esc_html_e( 'Edit', 'apache-2026' ); ?></a>)
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php else : ?>
							<p style="margin: 0;"><?php esc_html_e( 'No page usage detected.', 'apache-2026' ); ?></p>
						<?php endif; ?>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
