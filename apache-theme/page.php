<?php
/**
 * Page template.
 *
 * @package Apache_2026
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/content-page' );
endwhile;

get_footer();
