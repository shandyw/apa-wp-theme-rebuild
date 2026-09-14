<?php
/**
 * Page header.
 *
 * @package Apache_2026
 */

if ( is_front_page() ) {
	return;
}
?>

<header class="page-header site-container">
	<?php the_title( '<h1 class="page-title">', '</h1>' ); ?>
</header>
