<?php
/**
 * Summary content for list loops.
 *
 * @package Apache_2026
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'archive-card-list' ); ?>>
	
	<div class="archive-card__body">
		<?php the_title( '<h2 class="archive-card__title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
		<div class="archive-card__excerpt">
			<?php the_excerpt(); ?>
		</div>
	</div>
</article>
