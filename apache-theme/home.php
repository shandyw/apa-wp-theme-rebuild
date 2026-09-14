<?php
/**
 * Blog index template.
 *
 * @package Apache_2026
 */

get_header();

get_template_part( 'template-parts/archive/archive-header' );

if ( have_posts() ) {
	get_template_part( 'template-parts/archive/archive-loop' );
} else {
	get_template_part( 'template-parts/content/content-none' );
}

get_footer();
