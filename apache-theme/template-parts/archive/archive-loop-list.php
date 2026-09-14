<?php
/**
 * Archive loop list.
 *
 * @package Apache_2026
 */
?>

<section class="archive-loop-list">
	<div class="site-container">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content/content-summary-list' );
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
</section>
