<?php
/**
 * 404 template.
 *
 * @package Apache_2026
 */

get_header();
?>

<section class="not-found search-results">
	<div class="hero">
		
	</div>
	<div class="site-container">
		<header class="archive-header">
			<h1 class="archive-header__title">
				<span class="archive-header__title-text">
					<?php esc_html_e( 'Page not found', 'apache-2026' ); ?>
				</span>
				
			</h1>
			
			<p><?php esc_html_e( 'The page you are looking for could not be found. Try searching or return to the home page.', 'apache-2026' ); ?></p>
			
			<div class="search-box">

			<?php get_search_form(); ?>
			
			
			
			</div>
		</header>
		<p><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to home page', 'apache-2026' ); ?></a></p>
	</div>
	
</section>

<?php
get_footer();
