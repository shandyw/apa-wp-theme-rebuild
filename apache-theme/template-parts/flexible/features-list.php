<?php
/**
 * Flexible layout: Features List.
 *
 * ACF layout key: features_list
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

$features = array();

if ( function_exists( 'have_rows' ) && have_rows( 'features' ) ) {
	while ( have_rows( 'features' ) ) {
		the_row();

		$feature_title = get_sub_field( 'feature_title' );
		$feature_copy  = get_sub_field( 'feature_copy' );

		$feature_title = is_string( $feature_title ) ? trim( $feature_title ) : '';
		$feature_copy  = is_string( $feature_copy ) ? trim( $feature_copy ) : '';

		if ('' === $section_title && '' === $feature_title && '' === $feature_copy ) {
			continue;
		}

		$features[] = array(
			'title' => $feature_title,
			'copy'  => $feature_copy,
		);
	}
}

if ( '' === $title && '' === $copy && empty( $features ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		?>
		<section class="<?php echo esc_attr( implode( ' ', array_filter( array( 'apache-flex', 'apache-flex--features-list', 'features-list', $remove_padding ? 'apache-flex--no-section-top-padding' : '' ) ) ) ); ?>">
			<div class="features-list__inner">
				<p class="features-list__notice"><?php esc_html_e( 'Add content or at least one feature to display this module.', 'apache-2026' ); ?></p>
			</div>
		</section>
		<?php
	}

	return;
}
?>

<section class="<?php echo esc_attr( implode( ' ', array_filter( array( 'apache-flex', 'apache-flex--features-list', 'features-list', $remove_padding ? 'apache-flex--no-section-top-padding' : '' ) ) ) ); ?>">
	<div class="features-list__inner">
		<?php if ( $section_title ) : ?>
			<p class="h5 section-name <?php echo esc_attr( $line_color ); ?>">
				<?php echo esc_html( $section_title ); ?>
			</p>
		<?php endif; ?>
		
		<?php if ( '' !== $title || '' !== $copy ) : ?>
			<div class="features-list__intro">
				<?php if ( '' !== $title ) : ?>
					<h2 class="features-list__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $copy ) : ?>
					<div class="features-list__copy">
						<?php echo apache_2026_kses_wysiwyg_content( $copy ); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $features ) ) : ?>
			<div class="features-list__grid">
				<?php foreach ( $features as $feature ) : ?>
					<article class="features-list__item">
						<?php if ( '' !== $feature['title'] ) : ?>
							<h3 class="features-list__item-title"><?php echo esc_html( $feature['title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( '' !== $feature['copy'] ) : ?>
							<div class="features-list__item-copy">
								<?php echo apache_2026_kses_wysiwyg_content( $feature['copy'] ); ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
