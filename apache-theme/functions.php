<?php
/**
 * Apache 2026 theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_theme_file_path( 'inc/helpers.php' );
require_once get_theme_file_path( 'inc/acf.php' );
require_once get_theme_file_path( 'inc/compat.php' );
require_once get_theme_file_path( 'inc/security.php' );
require_once get_theme_file_path( 'inc/post-types/video.php' );
require_once get_theme_file_path( 'inc/video.php' );
require_once get_theme_file_path( 'inc/shortcodes/video-shortcode.php' );
require_once get_theme_file_path( 'inc/shortcodes/sitemap-shortcode.php' );
require_once get_theme_file_path( 'inc/admin/video-admin-columns.php' );
require_once get_theme_file_path( 'inc/post-types/gallery.php' );
require_once get_theme_file_path( 'inc/gallery.php' );
require_once get_theme_file_path( 'inc/features-list.php' );
require_once get_theme_file_path( 'inc/post-types/popup.php' );
require_once get_theme_file_path( 'inc/popup.php' );
require_once get_theme_file_path( 'inc/admin/popup-admin-columns.php' );
require_once get_theme_file_path( 'inc/admin/field-group-usage.php' );

function apache_2026_vite_asset( string $entry ): ?array {
	$manifest = apache_2026_vite_manifest();

	if ( ! isset( $manifest[ $entry ] ) || ! is_array( $manifest[ $entry ] ) ) {
		return null;
	}

	$asset = $manifest[ $entry ];
	$file  = isset( $asset['file'] ) ? ltrim( (string) $asset['file'], '/' ) : '';

	if ( '' === $file ) {
		return null;
	}

	return array(
		'path' => get_theme_file_path( 'assets/build/' . $file ),
		'uri'  => get_theme_file_uri( 'assets/build/' . $file ),
		'css'  => apache_2026_vite_css_dependencies( $entry, $manifest ),
	);
}

function apache_2026_vite_manifest(): array {
	static $manifest = null;

	if ( null !== $manifest ) {
		return $manifest;
	}

	$manifest_path = get_theme_file_path( 'assets/build/.vite/manifest.json' );

	if ( ! file_exists( $manifest_path ) ) {
		$manifest = array();
		return $manifest;
	}

	$contents = file_get_contents( $manifest_path );
	$decoded  = json_decode( (string) $contents, true );

	$manifest = is_array( $decoded ) ? $decoded : array();

	return $manifest;
}

function apache_2026_vite_css_dependencies( string $entry, array $manifest, array $seen = array() ): array {
	if ( isset( $seen[ $entry ] ) || ! isset( $manifest[ $entry ] ) || ! is_array( $manifest[ $entry ] ) ) {
		return array();
	}

	$seen[ $entry ] = true;
	$asset          = $manifest[ $entry ];
	$css            = array();

	if ( isset( $asset['css'] ) && is_array( $asset['css'] ) ) {
		foreach ( $asset['css'] as $css_file ) {
			$css_file = ltrim( (string) $css_file, '/' );
			$css[]    = array(
				'path' => get_theme_file_path( 'assets/build/' . $css_file ),
				'uri'  => get_theme_file_uri( 'assets/build/' . $css_file ),
			);
		}
	}

	if ( isset( $asset['imports'] ) && is_array( $asset['imports'] ) ) {
		foreach ( $asset['imports'] as $import ) {
			$css = array_merge(
				$css,
				apache_2026_vite_css_dependencies( (string) $import, $manifest, $seen )
			);
		}
	}

	return $css;
}

function apache_2026_asset_version( string $path ): string {
	if ( file_exists( $path ) ) {
		return (string) filemtime( $path );
	}

	$theme = wp_get_theme();

	return (string) $theme->get( 'Version' );
}

function apache_2026_register_vendor_assets(): void {
	wp_register_style(
		'apache-2026-typekit-wzu4vwv',
		'https://use.typekit.net/wzu4vwv.css',
		array(),
		null
	);

	wp_register_style(
		'apache-2026-bootstrap',
		get_theme_file_uri( 'assets/vendor/bootstrap/css/bootstrap.min.css' ),
		array(),
		apache_2026_asset_version( get_theme_file_path( 'assets/vendor/bootstrap/css/bootstrap.min.css' ) )
	);

	wp_register_script(
		'apache-2026-bootstrap',
		get_theme_file_uri( 'assets/vendor/bootstrap/js/bootstrap.bundle.min.js' ),
		array( 'jquery' ),
		apache_2026_asset_version( get_theme_file_path( 'assets/vendor/bootstrap/js/bootstrap.bundle.min.js' ) ),
		array( 'in_footer' => true )
	);

	wp_register_script(
		'apache-2026-gtag',
		'https://www.googletagmanager.com/gtag/js?id=G-RCRCZJVSWE',
		array(),
		null,
		array(
			'in_footer' => false,
			'strategy'  => 'async',
		)
	);

	wp_register_script(
		'apache-2026-hotjar',
		get_theme_file_uri( 'assets/js/hotjar-init.js' ),
		array(),
		apache_2026_asset_version( get_theme_file_path( 'assets/js/hotjar-init.js' ) ),
		array( 'in_footer' => false )
	);

	wp_register_script(
		'apache-2026-gtag-init',
		get_theme_file_uri( 'assets/js/gtag-init.js' ),
		array( 'apache-2026-gtag' ),
		apache_2026_asset_version( get_theme_file_path( 'assets/js/gtag-init.js' ) ),
		array( 'in_footer' => false )
	);
}

function apache_2026_should_enqueue_bootstrap(): bool {
	return (bool) apply_filters( 'apache_2026_enqueue_bootstrap', false );
}

function apache_2026_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'caption', 'gallery', 'script', 'search-form', 'style' ) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 88,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary'        => __( 'Primary Menu', 'apache-2026' ),
			'utility'        => __( 'Utility Menu', 'apache-2026' ),
			'footer_primary' => __( 'Footer Primary Menu', 'apache-2026' ),
			'footer_legal'   => __( 'Footer Legal Menu', 'apache-2026' ),
		)
	);
}
add_action( 'after_setup_theme', 'apache_2026_setup' );

function apache_2026_enable_excerpt_support(): void {
	foreach ( array( 'post', 'page', 'leaderships', 'gallery', 'apache_video', 'apache_popup' ) as $post_type ) {
		if ( post_type_exists( $post_type ) ) {
			add_post_type_support( $post_type, 'excerpt' );
		}
	}
}
add_action( 'init', 'apache_2026_enable_excerpt_support', 20 );

function apache_2026_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => __( 'Primary Sidebar', 'apache-2026' ),
			'id'            => 'primary-widget-area',
			'description'   => __( 'Legacy-compatible sidebar widget area.', 'apache-2026' ),
			'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Brand Text', 'apache-2026' ),
			'id'            => 'footer-brand-text',
			'description'   => __( 'Text displayed below the footer logo or site name.', 'apache-2026' ),
			'before_widget' => '<div id="%1$s" class="footer-brand-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="footer-brand-widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'apache_2026_widgets_init' );

function apache_2026_migrate_legacy_menu_locations(): void {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$map       = array(
		'main-menu'    => 'primary',
		'utility-menu' => 'utility',
		'footer-menu'  => 'footer_primary',
	);

	foreach ( $map as $legacy_location => $new_location ) {
		if ( empty( $locations[ $legacy_location ] ) || ! empty( $locations[ $new_location ] ) ) {
			continue;
		}

		$locations[ $new_location ] = absint( $locations[ $legacy_location ] );
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}
add_action( 'after_switch_theme', 'apache_2026_migrate_legacy_menu_locations' );

function apache_2026_enqueue_assets(): void {
	apache_2026_register_vendor_assets();

	$global_css = apache_2026_vite_asset( 'src/css/global.css' );
	$main_js    = apache_2026_vite_asset( 'src/js/main.js' );
	$style_deps = array( 'apache-2026-typekit-wzu4vwv' );
	$script_deps = array();
	$main_style_handle = 'apache-2026-global';

	wp_enqueue_style( 'apache-2026-typekit-wzu4vwv' );
	wp_enqueue_script( 'apache-2026-gtag' );
	wp_enqueue_script( 'apache-2026-gtag-init' );
	wp_enqueue_script( 'apache-2026-hotjar' );

	if ( apache_2026_should_enqueue_bootstrap() ) {
		wp_enqueue_style( 'apache-2026-bootstrap' );
		wp_enqueue_script( 'apache-2026-bootstrap' );

		$style_deps[]  = 'apache-2026-bootstrap';
		$script_deps[] = 'apache-2026-bootstrap';
	}

	if ( $global_css ) {
		wp_enqueue_style(
			'apache-2026-global',
			$global_css['uri'],
			$style_deps,
			apache_2026_asset_version( $global_css['path'] )
		);
	} else {
		$main_style_handle = 'apache-2026-style';

		wp_enqueue_style(
			'apache-2026-style',
			get_stylesheet_uri(),
			$style_deps,
			apache_2026_asset_version( get_stylesheet_directory() . '/style.css' )
		);
	}

	if ( $main_js ) {
		foreach ( $main_js['css'] as $index => $css_asset ) {
			wp_enqueue_style(
				'apache-2026-main-' . $index,
				$css_asset['uri'],
				array( $main_style_handle ),
				apache_2026_asset_version( $css_asset['path'] )
			);
		}

		wp_enqueue_script(
			'apache-2026-main',
			$main_js['uri'],
			array_unique( $script_deps ),
			apache_2026_asset_version( $main_js['path'] ),
			array( 'in_footer' => true )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'apache_2026_enqueue_assets' );

function apache_2026_script_loader_tag( string $tag, string $handle, string $src ): string {
	if ( ! in_array( $handle, array( 'apache-2026-main', 'apache-2026-popup' ), true ) ) {
		return $tag;
	}

	return sprintf(
		'<script type="module" src="%1$s" id="%2$s-js"></script>',
		esc_url( $src ),
		esc_attr( $handle )
	);
}
add_filter( 'script_loader_tag', 'apache_2026_script_loader_tag', 10, 3 );

function apache_2026_is_featured_post( int $post_id ): bool {
	return '1' === get_post_meta( $post_id, 'apache_2026_featured_post', true );
}

function apache_2026_featured_star_icon( bool $is_featured ): string {
	$fill = $is_featured ? 'currentColor' : 'none';

	return sprintf(
		'<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2.75l2.85 5.78 6.38.93-4.62 4.5 1.09 6.35L12 17.32 6.3 20.31l1.09-6.35-4.62-4.5 6.38-.93L12 2.75z" fill="%1$s" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>',
		esc_attr( $fill )
	);
}

function apache_2026_manage_post_columns( array $columns ): array {
	$offset = array_search( 'title', array_keys( $columns ), true );

	if ( false === $offset ) {
		$columns['apache_2026_featured_post'] = __( 'Featured', 'apache-2026' );
		return $columns;
	}

	$before = array_slice( $columns, 0, $offset + 1, true );
	$after  = array_slice( $columns, $offset + 1, null, true );

	return $before + array(
		'apache_2026_featured_post' => __( 'Featured', 'apache-2026' ),
	) + $after;
}
add_filter( 'manage_post_posts_columns', 'apache_2026_manage_post_columns' );

function apache_2026_render_post_column( string $column, int $post_id ): void {
	if ( 'apache_2026_featured_post' !== $column ) {
		return;
	}

	$is_featured = apache_2026_is_featured_post( $post_id );
	$label       = $is_featured ? __( 'Unfeature post', 'apache-2026' ) : __( 'Feature post', 'apache-2026' );
	?>
	<button
		type="button"
		class="apache-admin-featured-post-toggle<?php echo $is_featured ? ' is-featured' : ''; ?>"
		data-post-id="<?php echo esc_attr( (string) $post_id ); ?>"
		aria-pressed="<?php echo $is_featured ? 'true' : 'false'; ?>"
		aria-label="<?php echo esc_attr( $label ); ?>"
		title="<?php echo esc_attr( $label ); ?>"
	>
		<span class="apache-admin-featured-post-toggle__icon" aria-hidden="true"><?php echo apache_2026_featured_star_icon( $is_featured ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	</button>
	<?php
}
add_action( 'manage_posts_custom_column', 'apache_2026_render_post_column', 10, 2 );

function apache_2026_admin_featured_posts_assets( string $hook_suffix ): void {
	if ( 'edit.php' !== $hook_suffix ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script( 'jquery' );

	$script = <<<JS
window.apache2026FeaturedPost = {
  ajaxUrl: %s,
  nonce: %s,
  featuredLabel: %s,
  unfeaturedLabel: %s,
  filledIcon: %s,
  outlineIcon: %s
};

document.addEventListener('click', async (event) => {
  const button = event.target.closest('.apache-admin-featured-post-toggle');

  if (!button || button.dataset.loading === 'true') {
    return;
  }

  event.preventDefault();

  const postId = button.getAttribute('data-post-id');

  if (!postId) {
    return;
  }

  button.dataset.loading = 'true';

  try {
    const response = await fetch(window.apache2026FeaturedPost.ajaxUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: new URLSearchParams({
        action: 'apache_2026_toggle_featured_post',
        nonce: window.apache2026FeaturedPost.nonce,
        post_id: postId
      })
    });

    const payload = await response.json();

    if (!response.ok || !payload.success) {
      throw new Error(payload && payload.data && payload.data.message ? payload.data.message : 'Request failed');
    }

    const isFeatured = !!(payload.data && payload.data.is_featured);
    const label = isFeatured ? window.apache2026FeaturedPost.unfeaturedLabel : window.apache2026FeaturedPost.featuredLabel;
    const icon = isFeatured ? window.apache2026FeaturedPost.filledIcon : window.apache2026FeaturedPost.outlineIcon;
    const iconWrapper = button.querySelector('.apache-admin-featured-post-toggle__icon');

    button.classList.toggle('is-featured', isFeatured);
    button.setAttribute('aria-pressed', String(isFeatured));
    button.setAttribute('aria-label', label);
    button.setAttribute('title', label);

    if (iconWrapper) {
      iconWrapper.innerHTML = icon;
    }
  } catch (error) {
    window.console.error(error);
  } finally {
    button.dataset.loading = 'false';
  }
});
JS;

	wp_add_inline_script(
		'jquery',
		sprintf(
			$script,
			wp_json_encode( admin_url( 'admin-ajax.php' ) ),
			wp_json_encode( wp_create_nonce( 'apache_2026_toggle_featured_post' ) ),
			wp_json_encode( __( 'Feature post', 'apache-2026' ) ),
			wp_json_encode( __( 'Unfeature post', 'apache-2026' ) ),
			wp_json_encode( apache_2026_featured_star_icon( true ) ),
			wp_json_encode( apache_2026_featured_star_icon( false ) )
		)
	);

	wp_add_inline_style(
		'common',
		'.column-apache_2026_featured_post{width:88px}.apache-admin-featured-post-toggle{display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;padding:0;border:0;background:transparent;color:#D9AE3B;cursor:pointer}.apache-admin-featured-post-toggle svg{display:block;width:20px;height:20px}.apache-admin-featured-post-toggle[data-loading="true"]{opacity:.55;pointer-events:none}.apache-admin-featured-post-toggle:focus-visible{outline:2px solid #D9AE3B;outline-offset:2px}'
	);
}
add_action( 'admin_enqueue_scripts', 'apache_2026_admin_featured_posts_assets' );

function apache_2026_toggle_featured_post(): void {
	check_ajax_referer( 'apache_2026_toggle_featured_post', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'You cannot update this post.', 'apache-2026' ),
			),
			403
		);
	}

	$is_featured = ! apache_2026_is_featured_post( $post_id );

	if ( $is_featured ) {
		update_post_meta( $post_id, 'apache_2026_featured_post', '1' );
	} else {
		delete_post_meta( $post_id, 'apache_2026_featured_post' );
	}

	wp_send_json_success(
		array(
			'is_featured' => $is_featured,
		)
	);
}
add_action( 'wp_ajax_apache_2026_toggle_featured_post', 'apache_2026_toggle_featured_post' );


function apache_2026_component( string $name, array $args = array() ): void {
	$path = get_theme_file_path( "components/{$name}/{$name}.php" );

	if ( ! file_exists( $path ) ) {
		return;
	}

	extract( $args, EXTR_SKIP );
	include $path;
}

function apache_2026_social_links(): array {
	return array(
		'facebook'  => array(
			'label' => __( 'Facebook', 'apache-2026' ),
			'url'   => get_theme_mod( 'facebook_link' ),
		),
		'twitter'   => array(
			'label' => __( 'X', 'apache-2026' ),
			'url'   => get_theme_mod( 'twitter_link' ),
		),
		'linkedin'  => array(
			'label' => __( 'LinkedIn', 'apache-2026' ),
			'url'   => get_theme_mod( 'linkedin_link' ),
		),
		'instagram' => array(
			'label' => __( 'Instagram', 'apache-2026' ),
			'url'   => get_theme_mod( 'instagram_link' ),
		),
	);
}

function apache_2026_social_icon( string $network ): string {
	$icons = array(
		'facebook'  => '<svg class="site-icon" aria-hidden="true" focusable="false" viewBox="0 0 320 512"><path fill="currentColor" d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>',
		'twitter'   => '<svg class="site-icon" aria-hidden="true" focusable="false" viewBox="0 0 512 512"><path fill="currentColor" d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>',
		'linkedin'  => '<svg class="site-icon" aria-hidden="true" focusable="false" viewBox="0 0 448 512"><path fill="currentColor" d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zM384.3 416h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"/></svg>',
		'instagram' => '<svg class="site-icon" aria-hidden="true" focusable="false" viewBox="0 0 448 512"><path fill="currentColor" d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>',
	);

	return $icons[ $network ] ?? '';
}

function apache_2026_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'apache_2026_footer',
		array(
			'title'    => __( 'Footer', 'apache-2026' ),
			'priority' => 119,
		)
	);

	$wp_customize->add_setting(
		'apache_2026_hide_footer_logo',
		array(
			'default'           => false,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);

	$wp_customize->add_control(
		'apache_2026_hide_footer_logo',
		array(
			'label'   => __( 'Hide footer logo', 'apache-2026' ),
			'section' => 'apache_2026_footer',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_section(
		'apache_2026_social_links',
		array(
			'title'    => __( 'Social Links', 'apache-2026' ),
			'priority' => 120,
		)
	);

	foreach ( apache_2026_social_links() as $network => $link ) {
		$setting_id = $network . '_link';

		$wp_customize->add_setting(
			$setting_id,
			array(
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => sprintf(
					/* translators: %s: Social network name. */
					__( '%s URL', 'apache-2026' ),
					$link['label']
				),
				'section' => 'apache_2026_social_links',
				'type'    => 'url',
			)
		);
	}
}
add_action( 'customize_register', 'apache_2026_customize_register' );

class Apache_2026_Mobile_Nav_Walker extends Walker_Nav_Menu {
	/**
	 * Tracks whether the current item has children.
	 *
	 * @var bool
	 */
	public $has_children = false;

	/**
	 * ID for the submenu currently being rendered.
	 *
	 * @var string
	 */
	private string $current_submenu_id = '';

	public function start_lvl( &$output, $depth = 0, $args = null ): void {
		$indent  = str_repeat( "\t", $depth );
		$id      = 0 === $depth && '' !== $this->current_submenu_id ? sprintf( ' id="%s"', esc_attr( $this->current_submenu_id ) ) : '';
		$classes = 0 === $depth ? 'mobile-nav__submenu mobile-nav__submenu--panel sub-menu' : 'mobile-nav__submenu mobile-nav__submenu--nested sub-menu';
		$hidden  = 0 === $depth ? ' hidden' : '';

		$output .= "\n{$indent}<ul{$id} class=\"" . esc_attr( $classes ) . "\"{$hidden}>\n";
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ): void {
		$classes            = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[]          = 'mobile-nav__item';
		$should_add_toggle  = 0 === $depth && $this->has_children;
		$has_nested_content = $this->has_children;

		if ( $has_nested_content ) {
			$classes[] = 'mobile-nav__item--has-children';
		}

		if ( $should_add_toggle ) {
			$classes[] = 'mobile-nav__item--has-toggle';
		}

		$class_names = implode( ' ', array_filter( array_map( 'sanitize_html_class', $classes ) ) );
		$item_id     = 'mobile-menu-item-' . absint( $item->ID );
		$submenu_id  = 'mobile-submenu-' . absint( $item->ID );
		$title       = apply_filters( 'the_title', $item->title, $item->ID );

		$this->current_submenu_id = $should_add_toggle ? $submenu_id : '';

		$atts           = array();
		$atts['href']   = ! empty( $item->url ) ? esc_url( $item->url ) : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['class']  = 'mobile-nav__link';

		if ( '_blank' === $atts['target'] && '' === $atts['rel'] ) {
			$atts['rel'] = 'noopener';
		}

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( '' === $value ) {
				continue;
			}

			$attributes .= sprintf( ' %s="%s"', esc_attr( $attr ), esc_attr( $value ) );
		}

		$output .= sprintf(
			'<li id="%1$s" class="%2$s"><div class="mobile-nav__row"><a%3$s>%4$s</a>',
			esc_attr( $item_id ),
			esc_attr( $class_names ),
			$attributes,
			esc_html( $title )
		);

		if ( $should_add_toggle ) {
			$output .= sprintf(
				'<button class="mobile-nav__submenu-toggle" type="button" aria-expanded="false" aria-controls="%1$s"><span class="screen-reader-text">%2$s</span><span class="mobile-nav__submenu-icon" aria-hidden="true"></span></button>',
				esc_attr( $submenu_id ),
				esc_html__( 'Toggle submenu', 'apache-2026' )
			);
		}

		$output .= '</div>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ): void {
		$output .= "</li>\n";
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ): void {
		$this->has_children = ! empty( $children_elements[ $element->ID ] );

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}


// Leaderships CPT + taxonomy

function cptui_register_my_cpts() {

	/**
	 * Post Type: Leaderships.
	 */

	$labels = [
		"name" => esc_html__( "Leaderships", "apache-2026" ),
		"singular_name" => esc_html__( "Leadership", "apache-2026" ),
	];

	$args = [
		"label" => esc_html__( "Leaderships", "apache-2026" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => true,
		"rewrite" => [ "slug" => "leaderships", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "page-attributes" ],
		"taxonomies" => [ "leadership_category" ],
		"show_in_graphql" => false,
	];

	register_post_type( "leaderships", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );

function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Leadership Categories.
	 */

	$labels = [
		"name" => esc_html__( "Leadership Categories", "apache-2026" ),
		"singular_name" => esc_html__( "Leadership Category", "apache-2026" ),
	];

	$args = [
		"label" => esc_html__( "Leadership Categories", "apache-2026" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => false,
		"query_var" => true,
		"rewrite" => [ "slug" => "leadership-category", "with_front" => true ],
		"show_admin_column" => true,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"show_in_quick_edit" => true,
		"show_in_graphql" => false,
	];

	register_taxonomy( "leadership_category", [ "leaderships" ], $args );
}

add_action( 'init', 'cptui_register_my_taxes' );

function apache_2026_register_leadership_category_acf_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_apache_2026_leadership_category_settings',
			'title'                 => __( 'Leadership Category Settings', 'apache-2026' ),
			'fields'                => array(
				array(
					'key'               => 'field_apache_2026_leadership_category_ordered_members',
					'label'             => __( 'Ordered Members', 'apache-2026' ),
					'name'              => 'ordered_members',
					'type'              => 'relationship',
					'instructions'      => __( 'Drag selected leadership posts into the order this category should display. Members in this category that are not selected here will appear afterward.', 'apache-2026' ),
					'required'          => 0,
					'post_type'         => array( 'leaderships' ),
					'taxonomy'          => '',
					'filters'           => array( 'search' ),
					'elements'          => array(),
					'min'               => 0,
					'max'               => 0,
					'return_format'     => 'id',
					'bidirectional'     => 0,
					'allow_in_bindings' => 0,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'taxonomy',
						'operator' => '==',
						'value'    => 'leadership_category',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
			'show_in_rest'          => 0,
		)
	);
}

add_action( 'acf/init', 'apache_2026_register_leadership_category_acf_fields' );

function apache_2026_sort_leaderships_by_menu_order( WP_Query $query ): void {
	if ( is_admin() ) {
		$screen_post_type = $query->get( 'post_type' );

		if ( 'leaderships' !== $screen_post_type || ! $query->is_main_query() ) {
			return;
		}

		if ( '' === (string) $query->get( 'orderby' ) ) {
			$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
			$query->set( 'order', 'ASC' );
		}

		return;
	}

	if ( $query->is_main_query() && $query->is_post_type_archive( 'leaderships' ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
		$query->set( 'order', 'ASC' );
	}
}

add_action( 'pre_get_posts', 'apache_2026_sort_leaderships_by_menu_order' );

function apache_2026_get_leadership_category_ordered_member_ids( int $term_id ): array {
	if ( ! function_exists( 'get_field' ) || $term_id <= 0 ) {
		return array();
	}

	$ordered_members = get_field( 'ordered_members', 'leadership_category_' . $term_id );

	if ( ! is_array( $ordered_members ) ) {
		return array();
	}

	$member_ids = array();

	foreach ( $ordered_members as $member ) {
		if ( $member instanceof WP_Post ) {
			$member_ids[] = (int) $member->ID;
		} elseif ( is_numeric( $member ) ) {
			$member_ids[] = absint( $member );
		}
	}

	return array_values( array_unique( array_filter( $member_ids ) ) );
}

function apache_2026_get_leadership_member_category_order( int $post_id, int $term_id ): ?int {
	if ( ! function_exists( 'get_field' ) || $post_id <= 0 || $term_id <= 0 ) {
		return null;
	}

	$category_orders = get_field( 'category_order', $post_id );

	if ( ! is_array( $category_orders ) ) {
		return null;
	}

	foreach ( $category_orders as $category_order ) {
		if ( ! is_array( $category_order ) ) {
			continue;
		}

		$category = $category_order['leadership_category'] ?? 0;

		if ( $category instanceof WP_Term ) {
			$category = $category->term_id;
		}

		if ( absint( $category ) !== $term_id || ! isset( $category_order['order'] ) || ! is_numeric( $category_order['order'] ) ) {
			continue;
		}

		return (int) $category_order['order'];
	}

	return null;
}

function apache_2026_get_leadership_category_display_member_ids( int $term_id ): array {
	if ( $term_id <= 0 ) {
		return array();
	}

	$member_posts = get_posts(
		array(
			'post_type'              => 'leaderships',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'tax_query'              => array(
				array(
					'taxonomy' => 'leadership_category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			),
		)
	);

	if ( empty( $member_posts ) ) {
		return array();
	}

	$category_orders = array();

	foreach ( $member_posts as $member_post ) {
		if ( $member_post instanceof WP_Post ) {
			$category_orders[ $member_post->ID ] = apache_2026_get_leadership_member_category_order( (int) $member_post->ID, $term_id );
		}
	}

	usort(
		$member_posts,
		static function ( WP_Post $first, WP_Post $second ) use ( $category_orders ): int {
			$first_order  = $category_orders[ $first->ID ] ?? null;
			$second_order = $category_orders[ $second->ID ] ?? null;

			if ( null === $first_order && null !== $second_order ) {
				return 1;
			}

			if ( null !== $first_order && null === $second_order ) {
				return -1;
			}

			if ( null !== $first_order && null !== $second_order && $first_order !== $second_order ) {
				return $first_order <=> $second_order;
			}

			if ( (int) $first->menu_order !== (int) $second->menu_order ) {
				return (int) $first->menu_order <=> (int) $second->menu_order;
			}

			$title_comparison = strcasecmp( get_the_title( $first ), get_the_title( $second ) );

			return 0 !== $title_comparison ? $title_comparison : (int) $first->ID <=> (int) $second->ID;
		}
	);

	return array_values(
		array_map(
			static function ( WP_Post $member_post ): int {
				return (int) $member_post->ID;
			},
			$member_posts
		)
	);
}

function apache_2026_get_leadership_category_member_position( int $post_id, int $term_id ): ?int {
	$ordered_member_ids = apache_2026_get_leadership_category_display_member_ids( $term_id );

	if ( empty( $ordered_member_ids ) ) {
		return null;
	}

	$position = array_search( $post_id, $ordered_member_ids, true );

	if ( false === $position ) {
		return null;
	}

	return $position + 1;
}

function apache_2026_register_leadership_query_vars( array $query_vars ): array {
	$query_vars[] = 'leadership_group';

	return $query_vars;
}

add_filter( 'query_vars', 'apache_2026_register_leadership_query_vars' );

function apache_2026_get_leadership_navigation_term( int $post_id ): ?WP_Term {
	if ( $post_id <= 0 ) {
		return null;
	}

	$requested_slug = sanitize_title( (string) get_query_var( 'leadership_group' ) );

	if ( '' !== $requested_slug ) {
		$requested_term = get_term_by( 'slug', $requested_slug, 'leadership_category' );

		if ( $requested_term instanceof WP_Term && has_term( (int) $requested_term->term_id, 'leadership_category', $post_id ) ) {
			return $requested_term;
		}
	}

	$terms = get_the_terms( $post_id, 'leadership_category' );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}

	usort(
		$terms,
		static function ( WP_Term $first, WP_Term $second ): int {
			return $first->term_id <=> $second->term_id;
		}
	);

	return $terms[0] instanceof WP_Term ? $terms[0] : null;
}

function apache_2026_get_leadership_category_adjacent_posts( int $post_id, int $term_id ): array {
	$member_ids = apache_2026_get_leadership_category_display_member_ids( $term_id );
	$position   = array_search( $post_id, $member_ids, true );

	if ( false === $position ) {
		return array(
			'previous' => null,
			'next'     => null,
		);
	}

	$previous_id = $position > 0 ? $member_ids[ $position - 1 ] : 0;
	$next_id     = isset( $member_ids[ $position + 1 ] ) ? $member_ids[ $position + 1 ] : 0;

	return array(
		'previous' => $previous_id > 0 ? get_post( $previous_id ) : null,
		'next'     => $next_id > 0 ? get_post( $next_id ) : null,
	);
}

function apache_2026_leadership_admin_columns( array $columns ): array {
	if ( isset( $columns['taxonomy-leadership_category'] ) ) {
		$columns['taxonomy-leadership_category'] = __( 'Leadership Categories', 'apache-2026' );
		return $columns;
	}

	$updated_columns = array();

	foreach ( $columns as $key => $label ) {
		$updated_columns[ $key ] = $label;

		if ( 'title' === $key ) {
			$updated_columns['taxonomy-leadership_category'] = __( 'Leadership Categories', 'apache-2026' );
		}
	}

	return $updated_columns;
}

add_filter( 'manage_edit-leaderships_columns', 'apache_2026_leadership_admin_columns' );

function apache_2026_render_leadership_admin_column( string $column, int $post_id ): void {
	if ( 'taxonomy-leadership_category' !== $column ) {
		return;
	}

	$terms = get_the_terms( $post_id, 'leadership_category' );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		echo '&mdash;';
		return;
	}

	$items     = array();

	foreach ( $terms as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		$label = $term->name;

		$position = apache_2026_get_leadership_category_member_position( $post_id, (int) $term->term_id );

		if ( null !== $position ) {
			$label .= ' (' . $position . ')';
		}

		$items[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'edit.php?post_type=leaderships&leadership_category=' . $term->slug ) ),
			esc_html( $label )
		);
	}

	if ( empty( $items ) ) {
		echo '&mdash;';
		return;
	}

	echo implode( ', ', $items ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

add_action( 'manage_leaderships_posts_custom_column', 'apache_2026_render_leadership_admin_column', 10, 2 );
