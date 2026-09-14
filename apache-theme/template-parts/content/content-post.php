<?php
/**
 * Post content.
 *
 * @package Apache_2026
 */

$archive_label = '';

if ( 'post' === get_post_type() ) {
	$archive_label = 'ARCHIVE';
} else {
	$post_type_object = get_post_type_object( get_post_type() );
	$archive_label    = $post_type_object && ! empty( $post_type_object->labels->name )
		? (string) $post_type_object->labels->name
		: post_type_archive_title( '', false );
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-content' ); ?>>
	<header class="entry-header site-container">
		<?php if ( $archive_label ) : ?>
			<p class="h5 section-name gold"><?php echo esc_html( $archive_label ); ?></p>
		<?php endif; ?>
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		 <div class="entry-meta">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</div> 
	</header>

	<div class="entry-content site-container">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'large' );
		}

		the_content();

		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Post pages', 'apache-2026' ) . '">',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<nav class="pagination site-container" aria-label="<?php esc_attr_e( 'Posts pagination', 'apache-2026' ); ?>">
		<div class="nav-links">
			<?php previous_post_link( '<span class="nav-previous">%link</span>', esc_html__( 'Previous', 'apache-2026' ) ); ?>
			<?php next_post_link( '<span class="nav-next">%link</span>', esc_html__( 'Next', 'apache-2026' ) ); ?>
		</div>
	</nav>
</article>
