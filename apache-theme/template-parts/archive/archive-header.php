<?php

/**
 * Archive header.
 *
 * @package Apache_2026
 */

$archive_hero_image = function_exists( 'get_field' ) ? get_field( 'archive_hero_image', 'option' ) : null;
?>
<header class="apache-hero apache-hero--standard apache-hero--has-media apache-hero--no-text-overlay apache-hero--short apache-hero--bottom-cut-enabled apache-hero--has-bottom-cut">
	<?php if ( is_array( $archive_hero_image ) && ! empty( $archive_hero_image['ID'] ) ) : ?>
		<?php
		echo wp_get_attachment_image(
			(int) $archive_hero_image['ID'],
			'full',
			false,
			array(
				'class'         => 'apache-hero__image',
				'alt'           => isset( $archive_hero_image['alt'] ) ? $archive_hero_image['alt'] : '',
				'fetchpriority' => 'high',
				'decoding'      => 'async',
			)
		);
		?>
	<?php elseif ( is_array( $archive_hero_image ) && ! empty( $archive_hero_image['url'] ) ) : ?>
		<img
			class="apache-hero__image"
			src="<?php echo esc_url( $archive_hero_image['url'] ); ?>"
			alt="<?php echo esc_attr( $archive_hero_image['alt'] ?? '' ); ?>"
			fetchpriority="high"
			decoding="async"
		>
	<?php endif; ?>
	<div class="apache-hero__inner">
		<div class="apache-hero__no-overlay-content apache-flex--apache-section-header none">
			<?php if (is_home() && ! is_front_page()) : ?>
				<p class="h5 section-name gold">ARCHIVE</p>
			<?php elseif (is_archive()) : ?>
				<?php the_archive_title('<p class="h5 section-name gold">', '</p>'); ?>
			<?php endif; ?>
			<div class="apache-flex--apache-section-header__wrapper">
				<?php if (is_home() && ! is_front_page()) : ?>
					<h1 class="apache-flex--apache-section-header__headline"><?php echo esc_html(get_the_title(get_option('page_for_posts'))); ?></h1>
				<?php elseif (is_archive()) : ?>
					<?php the_archive_title('<h1 class="apache-flex--apache-section-header__headline">', '</h1>'); ?>
					<?php the_archive_description('<div class="apache-flex--apache-section-header__description">', '</div>'); ?>
				<?php endif; ?>
			</div>
		</div>
</div>
	<div class="apache-hero__cut-overlay" aria-hidden="true"></div>
</header>
