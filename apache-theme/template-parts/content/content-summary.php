<?php
/**
 * Summary content for loops.
 *
 * @package Apache_2026
 */
?>
<div class="card-repeater__item">
<article id="post-<?php the_ID(); ?>" <?php post_class( 'featured-post-slider__card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="featured-post-slider__image-link" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'medium_large', array( 'class' => 'featured-post-slider__image' ) ); ?>
		</a>
	<?php endif; ?>

	<div class="featured-post-slider__content">
		<?php the_title( '<h2 class="featured-post-slider__post-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
			<?php if ( get_the_excerpt() ) : ?>
				<p class="featured-post-slider__excerpt">
					<?php echo esc_html( get_the_excerpt() ); ?>
				</p>
			<?php endif; ?>

		<a class="featured-post-slider__read-more btn btn-more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Learn More', 'apache-2026' ); ?>
		</a>
	</div>
</article>
</div>
