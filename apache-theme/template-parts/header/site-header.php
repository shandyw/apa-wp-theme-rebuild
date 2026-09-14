<?php
/**
 * Site header.
 *
 * @package Apache_2026
 */

$home_url  = home_url( '/' );
$site_name = get_bloginfo( 'name' );
?>

<?php apache_2026_render_announcement_banner(); ?>

<header class="site-header" data-site-header>
	<div class="site-header__inner">
		<div class="site-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-header__logo-link" href="<?php echo esc_url( $home_url ); ?>" rel="home">
					<span class="site-header__site-name"><?php echo esc_html( $site_name ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<span class="stock-ticker site-header__ticker site-header__ticker--mobile">
			<span class="apa-header-ticker" data-apa-ticker><?php esc_html_e( 'Retrieving market data...', 'apache-2026' ); ?></span>
		</span>

		<div class="site-header__desktop">
			<?php get_template_part( 'template-parts/header/primary-nav' ); ?>

			<div class="site-header__utility">
				<span class="stock-ticker site-header__ticker site-header__ticker--desktop">
					<span class="apa-header-ticker" data-apa-ticker><?php esc_html_e( 'Retrieving market data...', 'apache-2026' ); ?></span>
				</span>

				<?php if ( has_nav_menu( 'utility' ) ) : ?>
					<nav class="utility-nav" aria-label="<?php esc_attr_e( 'Utility menu', 'apache-2026' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'utility',
								'menu_class'     => 'utility-nav__menu',
								'container'      => false,
								'depth'          => 1,
							)
						);
						?>
					</nav>
				<?php endif; ?>

				<form class="site-header__search search-form" role="search" method="get" action="<?php echo esc_url( $home_url ); ?>" data-expandable-search>
					<label class="screen-reader-text" for="site-header-search"><?php esc_html_e( 'Search', 'apache-2026' ); ?></label>
					<input id="site-header-search" class="site-header__search-input" type="search" name="s" placeholder="" value="<?php echo esc_attr( get_search_query() ); ?>">
					<button class="site-header__search-submit" type="submit" aria-label="<?php esc_attr_e( 'Search', 'apache-2026' ); ?>"></button>
				</form>
			</div>
		</div>

		<button class="site-header__toggle" type="button" aria-controls="mobile-navigation" aria-expanded="false" data-mobile-menu-toggle>
			<span class="site-header__toggle-line" aria-hidden="true"></span>
			<span class="site-header__toggle-text screen-reader-text"><?php esc_html_e( 'Menu', 'apache-2026' ); ?></span>
		</button>
	</div>

	<?php get_template_part( 'template-parts/header/mobile-nav' ); ?>
</header>
