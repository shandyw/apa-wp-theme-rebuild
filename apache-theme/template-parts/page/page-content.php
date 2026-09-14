<?php
/**
 * Default page body.
 *
 * @package Apache_2026
 */
?>

<div class="entry-content site-container">
	<?php
	the_content();

	wp_link_pages(
		array(
			'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page navigation', 'apache-2026' ) . '">',
			'after'  => '</nav>',
		)
	);
	?>
</div>
