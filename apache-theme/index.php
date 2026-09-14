<?php
/**
 * Fallback template.
 *
 * @package Apache_2026
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php get_template_part( 'template-parts/archive/archive-loop' ); ?>
<?php else : ?>
	<?php get_template_part( 'template-parts/content/content-none' ); ?>
<?php endif; ?>

<?php
get_footer();
