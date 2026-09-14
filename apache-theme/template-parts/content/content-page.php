<?php
/**
 * Page content.
 *
 * @package Apache_2026
 */

$has_flexible_blocks = function_exists( 'have_rows' ) && have_rows( 'blocks' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-content' ); ?>>
	<?php apache_2026_render_hero(); ?>
	<?php apache_2026_render_timeline(); ?>
	<?php apache_2026_render_home_map(); ?>

	<?php if ( $has_flexible_blocks ) : ?>
		<?php get_template_part( 'template-parts/flexible/blocks' ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/page/page-content' ); ?>
	<?php endif; ?>

	<?php apache_2026_render_contact_information(); ?>
</article>
