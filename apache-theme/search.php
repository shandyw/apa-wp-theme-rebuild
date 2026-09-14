<?php
/**
 * Search results template.
 *
 * @package Apache_2026
 */

get_header();
?>

<section class="search-results">
	<div class="hero">
		
	</div>
	

	<div class="site-container">
		<header class="archive-header">
			<h1 class="archive-header__title">
				<span class="archive-header__title-text">
					<?php
					printf(
						/* translators: %s: Search query. */
						esc_html__( 'Search results for "%s"', 'apache-2026' ),
						esc_html( get_search_query() )
					);
					?>
				</span>
				<span class="archive-header__count">
					<?php
					printf(
						/* translators: %s: Number of search results. */
						esc_html( _n( '%s result', '%s results', (int) $wp_query->found_posts, 'apache-2026' ) ),
						number_format_i18n( (int) $wp_query->found_posts )
					);
					?>
				</span>
			</h1>
			<div class="search-box">

			<?php get_search_form(); ?>
			</div>
		</header>
	</div>
</section>

<?php
if ( have_posts() ) {
	get_template_part( 'template-parts/archive/archive-loop-list' );
} else {
	get_template_part( 'template-parts/content/content-none' );
}

get_footer();
