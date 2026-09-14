<?php
/**
 * Footer primary navigation.
 *
 * @package Apache_2026
 */

if ( ! has_nav_menu( 'footer_primary' ) ) {
	return;
}
?>

<nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer menu', 'apache-2026' ); ?>">
	<?php
	wp_nav_menu(
		array(
			'theme_location' => 'footer_primary',
			'menu_class'     => 'footer-nav__menu',
			'container'      => false,
			'depth'          => 2,
		)
	);
	?>
</nav>
