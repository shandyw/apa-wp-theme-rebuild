<?php
/**
 * Mobile navigation panel.
 *
 * @package Apache_2026
 */
?>

<div id="mobile-navigation" class="mobile-nav" data-mobile-menu hidden>
	<nav class="mobile-nav__primary" aria-label="<?php esc_attr_e( 'Mobile primary menu', 'apache-2026' ); ?>">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'mobile-nav__menu',
					'container'      => false,
					'depth'          => 3,
					'walker'         => new Apache_2026_Mobile_Nav_Walker(),
				)
			);
		}
		?>
	</nav>

	<?php if ( has_nav_menu( 'utility' ) ) : ?>
		<nav class="mobile-nav__utility" aria-label="<?php esc_attr_e( 'Mobile utility menu', 'apache-2026' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'utility',
					'menu_class'     => 'mobile-nav__utility-menu',
					'container'      => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>
	<?php endif; ?>

	<form class="mobile-nav__search search-form is-search-open" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-expandable-search>
		<label class="screen-reader-text" for="mobile-navigation-search"><?php esc_html_e( 'Search', 'apache-2026' ); ?></label>
		<input id="mobile-navigation-search" class="mobile-nav__search-input" type="search" name="s" placeholder="" value="<?php echo esc_attr( get_search_query() ); ?>">
		<button class="mobile-nav__search-submit" type="submit" aria-label="<?php esc_attr_e( 'Search', 'apache-2026' ); ?>"></button>
	</form>
</div>
