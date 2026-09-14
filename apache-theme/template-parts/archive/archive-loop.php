<?php
/**
 * Archive loop.
 *
 * @package Apache_2026
 */
?>

<section class="apache-flex apache-flex--card-repeater card-repeater card-repeater--cols-3">
	<div class="card-repeater__inner">
		<div class="card-repeater__grid">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/content-summary' );
			endwhile;
			?>
		</div>


		<nav class="pagination site-container" aria-label="<?php esc_attr_e( 'Posts pagination', 'apache-2026' ); ?>">
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => esc_html__( 'Previous', 'apache-2026' ),
					'next_text' => esc_html__( 'Next', 'apache-2026' ),
				)
			);
			?>
		</nav>
	</div>
</section>
