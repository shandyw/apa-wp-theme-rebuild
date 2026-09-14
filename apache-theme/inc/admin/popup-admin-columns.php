<?php
/**
 * Popup admin columns and inline actions.
 *
 * @package Apache_2026
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function apache_2026_popup_admin_columns( array $columns ): array {
	$updated_columns = array();

	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$updated_columns['title']                 = __( 'Name', 'apache-2026' );
			$updated_columns['apache_popup_enabled']  = __( 'Enabled', 'apache-2026' );
			$updated_columns['apache_popup_css_class'] = __( 'CSS Class', 'apache-2026' );
			$updated_columns['apache_popup_copy']     = __( 'Copy Class', 'apache-2026' );
			continue;
		}

		$updated_columns[ $key ] = $label;
	}

	return $updated_columns;
}
add_filter( 'manage_edit-apache_popup_columns', 'apache_2026_popup_admin_columns' );

function apache_2026_render_popup_admin_column( string $column, int $post_id ): void {
	if ( 'apache_popup_enabled' === $column ) {
		$is_enabled = apache_2026_is_popup_enabled( $post_id );
		$label      = $is_enabled ? __( 'Disable popup', 'apache-2026' ) : __( 'Enable popup', 'apache-2026' );
		$classes    = 'apache-admin-popup-toggle' . ( $is_enabled ? ' is-enabled' : '' );
		$action_url = wp_nonce_url(
			add_query_arg(
				array(
					'action'   => 'apache_2026_toggle_popup_enabled_redirect',
					'popup_id' => $post_id,
				),
				admin_url( 'admin-post.php' )
			),
			'apache_2026_toggle_popup_enabled_' . $post_id
		);
		?>
		<form method="post" action="<?php echo esc_url( $action_url ); ?>" class="apache-admin-popup-toggle-form">
			<button
				type="submit"
				class="<?php echo esc_attr( $classes ); ?>"
				data-enabled="<?php echo $is_enabled ? 'true' : 'false'; ?>"
				aria-pressed="<?php echo $is_enabled ? 'true' : 'false'; ?>"
				aria-label="<?php echo esc_attr( $label ); ?>"
				title="<?php echo esc_attr( $label ); ?>"
			>
				<span class="apache-admin-popup-toggle__track" aria-hidden="true">
					<span class="apache-admin-popup-toggle__thumb"></span>
				</span>
			</button>
		</form>
		<?php
		return;
	}

	if ( 'apache_popup_css_class' === $column ) {
		$trigger_class = apache_2026_get_popup_trigger_class( $post_id );

		if ( '' !== $trigger_class ) {
			echo '<code>' . esc_html( $trigger_class ) . '</code>';
		}
		return;
	}

	if ( 'apache_popup_copy' === $column ) {
		$trigger_class = apache_2026_get_popup_trigger_class( $post_id );

		if ( '' === $trigger_class ) {
			return;
		}
		?>
		<button
			type="button"
			class="button apache-copy-popup-class"
			data-popup-class="<?php echo esc_attr( $trigger_class ); ?>"
			data-default-label="<?php echo esc_attr__( 'Copy class', 'apache-2026' ); ?>"
			data-copied-label="<?php echo esc_attr__( 'Copied', 'apache-2026' ); ?>"
		>
			<?php esc_html_e( 'Copy class', 'apache-2026' ); ?>
		</button>
		<?php
	}
}
add_action( 'manage_apache_popup_posts_custom_column', 'apache_2026_render_popup_admin_column', 10, 2 );

function apache_2026_enqueue_popup_admin_assets( string $hook_suffix ): void {
	if ( 'edit.php' !== $hook_suffix ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'apache_popup' !== $screen->post_type ) {
		return;
	}

	$script_path = get_theme_file_path( 'assets/js/admin-popup-list.js' );

	if ( ! file_exists( $script_path ) ) {
		return;
	}

	wp_enqueue_script(
		'apache-2026-admin-popup-list',
		get_theme_file_uri( 'assets/js/admin-popup-list.js' ),
		array(),
		apache_2026_asset_version( $script_path ),
		true
	);

	wp_add_inline_script(
		'apache-2026-admin-popup-list',
		sprintf(
			'window.apache2026PopupAdmin = %s;',
			wp_json_encode(
				array(
					'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
					'nonce'         => wp_create_nonce( 'apache_2026_toggle_popup_enabled' ),
					'enableLabel'   => __( 'Enable popup', 'apache-2026' ),
					'disableLabel'  => __( 'Disable popup', 'apache-2026' ),
					'copyFallback'  => __( 'Copy class', 'apache-2026' ),
					'copiedLabel'   => __( 'Copied', 'apache-2026' ),
				)
			)
		),
		'before'
	);

	wp_add_inline_style(
		'common',
		'.column-apache_popup_enabled{width:92px}.column-apache_popup_copy{width:120px}.apache-admin-popup-toggle-form{margin:0}.apache-admin-popup-toggle{display:inline-flex;align-items:center;justify-content:center;padding:0;border:0;background:transparent;cursor:pointer;-webkit-appearance:none;appearance:none}.apache-admin-popup-toggle__track{position:relative;display:inline-flex;width:42px;height:24px;padding:2px;border-radius:999px;background:#ccd0d4;transition:background .2s ease}.apache-admin-popup-toggle__thumb{display:block;width:20px;height:20px;border-radius:50%;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.2);transform:translateX(0);transition:transform .2s ease}.apache-admin-popup-toggle.is-enabled .apache-admin-popup-toggle__track,.apache-admin-popup-toggle[data-enabled=\"true\"] .apache-admin-popup-toggle__track{background:#2271b1}.apache-admin-popup-toggle.is-enabled .apache-admin-popup-toggle__thumb,.apache-admin-popup-toggle[data-enabled=\"true\"] .apache-admin-popup-toggle__thumb{transform:translateX(18px)}.apache-admin-popup-toggle:focus-visible{outline:2px solid #2271b1;outline-offset:2px}'
	);
}
add_action( 'admin_enqueue_scripts', 'apache_2026_enqueue_popup_admin_assets' );

function apache_2026_toggle_popup_enabled(): void {
	check_ajax_referer( 'apache_2026_toggle_popup_enabled', 'nonce' );

	$popup_id = isset( $_POST['popup_id'] ) ? absint( $_POST['popup_id'] ) : 0;

	if ( ! $popup_id || 'apache_popup' !== get_post_type( $popup_id ) || ! current_user_can( 'edit_post', $popup_id ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'You cannot update this popup.', 'apache-2026' ),
			),
			403
		);
	}

	$enabled = ! apache_2026_is_popup_enabled( $popup_id );

	update_post_meta( $popup_id, 'popup_enabled', $enabled ? '1' : '0' );
	update_post_meta( $popup_id, '_popup_enabled', APACHE_2026_POPUP_ENABLED_FIELD_KEY );

	wp_send_json_success(
		array(
			'enabled' => $enabled,
		)
	);
}
add_action( 'wp_ajax_apache_2026_toggle_popup_enabled', 'apache_2026_toggle_popup_enabled' );

function apache_2026_toggle_popup_enabled_redirect(): void {
	$popup_id = isset( $_REQUEST['popup_id'] ) ? absint( $_REQUEST['popup_id'] ) : 0;

	if ( ! $popup_id ) {
		wp_safe_redirect( admin_url( 'edit.php?post_type=apache_popup' ) );
		exit;
	}

	check_admin_referer( 'apache_2026_toggle_popup_enabled_' . $popup_id );

	if ( 'apache_popup' !== get_post_type( $popup_id ) || ! current_user_can( 'edit_post', $popup_id ) ) {
		wp_die( esc_html__( 'You cannot update this popup.', 'apache-2026' ), 403 );
	}

	$enabled = ! apache_2026_is_popup_enabled( $popup_id );

	update_post_meta( $popup_id, 'popup_enabled', $enabled ? '1' : '0' );
	update_post_meta( $popup_id, '_popup_enabled', APACHE_2026_POPUP_ENABLED_FIELD_KEY );

	$redirect_url = wp_get_referer();

	if ( ! is_string( $redirect_url ) || '' === $redirect_url ) {
		$redirect_url = admin_url( 'edit.php?post_type=apache_popup' );
	}

	wp_safe_redirect( $redirect_url );
	exit;
}
add_action( 'admin_post_apache_2026_toggle_popup_enabled_redirect', 'apache_2026_toggle_popup_enabled_redirect' );
