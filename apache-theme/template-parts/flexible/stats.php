<?php
/**
 * Flexible layout: Stats.
 *
 * ACF layout key: stats
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$section_header = get_sub_field( 'section_header' );
$remove_padding = apache_2026_bool_from_mixed( get_sub_field( 'remove_padding' ) );

$section_title = '';
$line_color    = 'gold';

if ( is_array( $section_header ) ) {
	$section_title = isset( $section_header['title'] ) && is_string( $section_header['title'] )
		? trim( $section_header['title'] )
		: '';

	$line_color = isset( $section_header['line_color'] ) && is_string( $section_header['line_color'] ) && '' !== trim( $section_header['line_color'] )
		? trim( $section_header['line_color'] )
		: 'gold';
}

$title = get_sub_field( 'title' );
$copy  = get_sub_field( 'copy' );

$title = is_string( $title ) ? trim( $title ) : '';
$copy  = is_string( $copy ) ? trim( $copy ) : '';

$stats = array();

if ( function_exists( 'have_rows' ) && have_rows( 'stat_items' ) ) {
	while ( have_rows( 'stat_items' ) ) {
		the_row();

		$stat_title = get_sub_field( 'stat_title' );
		$stat_copy  = get_sub_field( 'stat_copy' );

		$stat_title = is_string( $stat_title ) ? trim( $stat_title ) : '';
		$stat_copy  = is_string( $stat_copy ) ? trim( $stat_copy ) : '';

		if ('' === $section_title && '' === $stat_title && '' === $stat_copy ) {
			continue;
		}

		$stats[] = array(
			'title' => $stat_title,
			'copy'  => $stat_copy,
		);
	}
}

if ( '' === $title && '' === $copy && empty( $stats ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		?>
		<section class="<?php echo esc_attr( implode( ' ', array_filter( array( 'apache-flex', 'apache-flex--stats', 'stats', $remove_padding ? 'apache-flex--no-section-top-padding' : '' ) ) ) ); ?>">
			<div class="stats__inner">
				<p class="stats__notice"><?php esc_html_e( 'Add content or at least one feature to display this module.', 'apache-2026' ); ?></p>
			</div>
		</section>
		<?php
	}

	return;
}
?>

<section class="<?php echo esc_attr( implode( ' ', array_filter( array( 'apache-flex', 'apache-flex--stats', 'stats', $remove_padding ? 'apache-flex--no-section-top-padding' : '' ) ) ) ); ?>">
	<div class="stats__inner">
		<?php if ( $section_title ) : ?>
			<p class="h5 section-name <?php echo esc_attr( $line_color ); ?>">
				<?php echo esc_html( $section_title ); ?>
			</p>
		<?php endif; ?>
		
		<?php if ( '' !== $title || '' !== $copy ) : ?>
			<div class="stats__intro">
				<?php if ( '' !== $title ) : ?>
					<h2 class="stats__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $copy ) : ?>
					<div class="stats__copy">
						<?php echo apache_2026_kses_wysiwyg_content( $copy ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $stats ) ) : ?>
			<div class="stats__grid">
				<?php foreach ( $stats as $stat ) : ?>
					<article class="stats__item">
						<?php if ( '' !== $stat['title'] ) : ?>
							<h3 class="stats__item-title"><?php echo esc_html( $stat['title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( '' !== $stat['copy'] ) : ?>
							<div class="stats__item-copy">
								<?php echo apache_2026_kses_wysiwyg_content( $stat['copy'] ); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
