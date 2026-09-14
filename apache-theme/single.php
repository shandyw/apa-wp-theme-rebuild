<?php
/**
 * Single post template.
 *
 * @package Apache_2026
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/content-post' );
endwhile;

get_footer();
