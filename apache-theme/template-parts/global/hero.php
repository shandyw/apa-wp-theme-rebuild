<?php
/**
 * Global page hero.
 *
 * ACF field group: Hero
 *
 * @package Apache_2026
 */

$data           = isset( $args['data'] ) && is_array( $args['data'] ) ? $args['data'] : apache_2026_get_hero_data();
$heading_level  = isset( $args['heading_level'] ) ? absint( $args['heading_level'] ) : 1;
$heading_level  = min( 6, max( 1, $heading_level ) );
$heading_tag    = 'h' . (string) $heading_level;
$type           = isset( $data['type'] ) && is_string( $data['type'] ) ? $data['type'] : 'standard';
$title          = isset( $data['title'] ) && is_string( $data['title'] ) ? trim( $data['title'] ) : '';
$eyebrow        = isset( $data['eyebrow'] ) && is_string( $data['eyebrow'] ) ? trim( $data['eyebrow'] ) : '';
$content        = isset( $data['content'] ) && is_string( $data['content'] ) ? trim( $data['content'] ) : '';
$image          = isset( $data['image'] ) && is_array( $data['image'] ) ? $data['image'] : array();
$button         = isset( $data['button'] ) && is_array( $data['button'] ) ? $data['button'] : array();
$slides         = isset( $data['slides'] ) && is_array( $data['slides'] ) ? $data['slides'] : array();
$no_overlay     = isset( $data['no_overlay_content'] ) && is_array( $data['no_overlay_content'] ) ? $data['no_overlay_content'] : array();
$slide_count    = count( $slides );
$is_short       = ! empty( $data['is_short'] );
$has_text_overlay = 'slider' === $type || ! empty( $data['has_text_overlay'] );
$has_media      = ( 'slider' === $type && ! empty( $slides ) ) || ! empty( $image['url'] );
$wants_bottom_cut = ! empty( $data['has_bottom_overlay'] );
$has_bottom_cut = $wants_bottom_cut && $has_media;
$show_cut_overlay = $has_bottom_cut && ! ( $has_media && $has_text_overlay );
$show_wavy_lines = $has_bottom_cut && ! $is_short;
$button_classes = ( $has_media && $has_text_overlay ) ? 'btn btn-light' : 'btn btn-more-dk';
$no_overlay_eyebrow = isset( $no_overlay['eyebrow'] ) && is_string( $no_overlay['eyebrow'] ) ? trim( $no_overlay['eyebrow'] ) : '';
$no_overlay_line_color = isset( $no_overlay['line_color'] ) && is_string( $no_overlay['line_color'] ) ? sanitize_html_class( $no_overlay['line_color'] ) : 'gold';
$no_overlay_headline = isset( $no_overlay['headline'] ) && is_string( $no_overlay['headline'] ) ? trim( $no_overlay['headline'] ) : '';
$no_overlay_content = isset( $no_overlay['content'] ) && is_string( $no_overlay['content'] ) ? trim( $no_overlay['content'] ) : '';
$no_overlay_cta_options = isset( $no_overlay['cta_options'] ) && is_string( $no_overlay['cta_options'] ) ? sanitize_html_class( $no_overlay['cta_options'] ) : 'none';
$no_overlay_link_cta = isset( $no_overlay['link_cta'] ) && is_array( $no_overlay['link_cta'] ) ? $no_overlay['link_cta'] : array();
$no_overlay_button_ctas = isset( $no_overlay['button_ctas'] ) && is_array( $no_overlay['button_ctas'] ) ? $no_overlay['button_ctas'] : array();
$has_no_overlay_content = ! empty( $no_overlay['has_content'] );

$render_wavy_lines = static function (): void {
	?>
	<div class="wavy-lines-container apache-hero__wavy-lines" aria-hidden="true">
		<svg version="10" id="wavyLines" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 238 1138" style="enable-background:new 0 0 238 1138;" xml:space="preserve">
			<style type="text/css">
				.st1{fill:none;stroke:#74BCD7;stroke-width:6;stroke-linecap:round;stroke-miterlimit:10;}
			</style>
			<path id="line17" class="st1" d="M-3,1134.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5"></path>
			<path id="line16" class="st1" d="M-3,1074.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5"></path>
			<path id="line15" class="st1" d="M-3,1014.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5"></path>
			<path id="line14" class="st1" d="M-3,954.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5"></path>
			<path id="line13" class="st1" d="M-3,894.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5"></path>
			<path id="line12" class="st1" d="M-3,834.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
			<path id="line11" class="st1" d="M-3,774.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
			<path id="line10" class="st1" d="M-3,714.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
			<path id="line9" class="st1" d="M-3,654.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5"></path>
			<path id="line8" class="st1" d="M-3,594.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
			<path id="line7" class="st1" d="M-3,534.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
			<path id="line6" class="st1" d="M-3,474.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
			<path id="line5" class="st1" d="M-3,414.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
			<path id="line4" class="st1" d="M-3,354.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5"></path>
			<path id="line3" class="st1" d="M-3,294.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5"></path>
			<path id="line2" class="st1" d="M-3,234.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
			<path id="line1" class="st1" d="M-3,174.25c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5
				c17-12.7,11-20.7,28-33.5c17-12.7,23-4.7,40-17.5c17-12.7,11-20.7,28-33.5s23-4.7,40-17.5"></path>
		</svg>
	</div>
	<?php
};

if ( 'slider' !== $type && empty( $image['url'] ) && ! $has_no_overlay_content ) {
	return;
}

$classes = array(
	'apache-hero',
	'apache-hero--' . sanitize_html_class( $type ),
	$has_media ? 'apache-hero--has-media' : 'apache-hero--text-only',
);

if ( $has_media && $has_text_overlay ) {
	$classes[] = 'apache-hero--text-overlay';
}

if ( $has_media && ! $has_text_overlay && 'slider' !== $type ) {
	$classes[] = 'apache-hero--no-text-overlay';
}

if ( $is_short ) {
	$classes[] = 'apache-hero--short';
}

if ( 'slider' === $type && 2 > $slide_count ) {
	$classes[] = 'apache-hero--single-slide';
}

if ( $wants_bottom_cut ) {
	$classes[] = 'apache-hero--bottom-cut-enabled';
}

if ( $has_bottom_cut ) {
	$classes[] = 'apache-hero--has-bottom-cut';
}
?>

<?php if ( 'slider' === $type && ! empty( $slides ) ) : ?>
	<header class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>">
		<div class="apache-hero__slider apache-swiper swiper" data-apache-swiper="hero" data-hero-slider aria-roledescription="<?php esc_attr_e( 'carousel', 'apache-2026' ); ?>">
			<div class="swiper-wrapper">
				<?php foreach ( $slides as $slide_index => $slide ) : ?>
					<?php
					$slide_title   = isset( $slide['title'] ) && is_string( $slide['title'] ) ? trim( $slide['title'] ) : $title;
					$slide_eyebrow = isset( $slide['eyebrow'] ) && is_string( $slide['eyebrow'] ) ? trim( $slide['eyebrow'] ) : '';
					$slide_image   = isset( $slide['image'] ) && is_array( $slide['image'] ) ? $slide['image'] : array();
					$slide_button  = isset( $slide['button'] ) && is_array( $slide['button'] ) ? $slide['button'] : array();
					$slide_heading = 0 === $slide_index ? $heading_tag : 'h' . (string) min( 6, $heading_level );
					?>
					<div class="apache-hero__slide swiper-slide" role="group" aria-label="<?php echo esc_attr( sprintf( __( 'Slide %1$d of %2$d', 'apache-2026' ), $slide_index + 1, $slide_count ) ); ?>">
						<?php if ( ! empty( $slide_image['id'] ) ) : ?>
							<?php
							$image_attrs = array(
								'class' => 'apache-hero__image',
								'alt'   => isset( $slide_image['alt'] ) ? $slide_image['alt'] : '',
							);

							if ( 0 === $slide_index ) {
								$image_attrs['fetchpriority'] = 'high';
								$image_attrs['loading']       = 'eager';
								$image_attrs['decoding']      = 'async';
							} else {
								$image_attrs['loading']  = 'lazy';
								$image_attrs['decoding'] = 'async';
							}

							echo wp_get_attachment_image(
								(int) $slide_image['id'],
								'2048x2048',
								false,
								$image_attrs
							);
							?>
						<?php elseif ( ! empty( $slide_image['url'] ) ) : ?>
							<img
								class="apache-hero__image"
								src="<?php echo esc_url( $slide_image['url'] ); ?>"
								alt="<?php echo esc_attr( $slide_image['alt'] ?? '' ); ?>"
								<?php if ( ! empty( $slide_image['width'] ) && ! empty( $slide_image['height'] ) ) : ?>
									width="<?php echo esc_attr( absint( $slide_image['width'] ) ); ?>"
									height="<?php echo esc_attr( absint( $slide_image['height'] ) ); ?>"
								<?php endif; ?>
								<?php echo 0 === $slide_index ? 'fetchpriority="high" loading="eager" decoding="async"' : 'loading="lazy" decoding="async"'; ?>
							>
						<?php endif; ?>

						<div class="apache-hero__inner">
							<div class="apache-hero__content">
								<?php if ( $slide_eyebrow ) : ?>
									<p class="apache-hero__eyebrow section-name light"><?php echo esc_html( $slide_eyebrow ); ?></p>
								<?php endif; ?>

								<?php if ( $slide_title ) : ?>
									<<?php echo esc_attr( $slide_heading ); ?> class="apache-hero__title"><?php echo esc_html( $slide_title ); ?></<?php echo esc_attr( $slide_heading ); ?>>
								<?php endif; ?>

								<?php if ( ! empty( $slide_button['label'] ) && ! empty( $slide_button['url'] ) ) : ?>
									<p class="apache-hero__actions">
										<a
											class="btn btn-more-dk"
											href="<?php echo esc_url( $slide_button['url'] ); ?>"
											target="<?php echo esc_attr( apache_2026_sanitize_link_target( $slide_button['target'] ?? '_self' ) ); ?>"
											<?php if ( apache_2026_link_rel( $slide_button['target'] ?? '_self' ) ) : ?>
												rel="noopener noreferrer"
											<?php endif; ?>
										>
											<?php echo esc_html( $slide_button['label'] ); ?>
										</a>
									</p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( 1 < $slide_count ) : ?>
				<?php
				get_template_part(
					'template-parts/global/swiper-controls',
					null,
					array(
						'wrapper_class' => 'apache-hero__nav',
						'button_class'  => 'apache-hero__button',
						'prev_label'    => __( 'Previous slide', 'apache-2026' ),
						'next_label'    => __( 'Next slide', 'apache-2026' ),
					)
				);
				?>
			<?php endif; ?>
		</div>

		<?php if ( $show_cut_overlay ) : ?>
			<div class="apache-hero__cut-overlay" aria-hidden="true"></div>
		<?php endif; ?>
		<?php if ( $show_wavy_lines ) : ?>
			<?php $render_wavy_lines(); ?>
		<?php endif; ?>
	</header>
<?php else : ?>
	<header class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>">
		<?php if ( ! empty( $image['id'] ) ) : ?>
			<?php
			echo wp_get_attachment_image(
				(int) $image['id'],
				'2048x2048',
				false,
				array(
					'class'         => 'apache-hero__image',
					'alt'           => isset( $image['alt'] ) ? $image['alt'] : '',
					'fetchpriority' => 'high',
					'loading'       => 'eager',
					'decoding'      => 'async',
				)
			);
			?>
		<?php elseif ( ! empty( $image['url'] ) ) : ?>
			<img
				class="apache-hero__image"
				src="<?php echo esc_url( $image['url'] ); ?>"
				alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>"
				<?php if ( ! empty( $image['width'] ) && ! empty( $image['height'] ) ) : ?>
					width="<?php echo esc_attr( absint( $image['width'] ) ); ?>"
					height="<?php echo esc_attr( absint( $image['height'] ) ); ?>"
				<?php endif; ?>
				fetchpriority="high"
				loading="eager"
				decoding="async"
			>
		<?php endif; ?>

		<div class="apache-hero__inner">
				<?php if ( ! $has_text_overlay && $has_no_overlay_content ) : ?>
				<?php
				$no_overlay_classes = array(
					'apache-hero__no-overlay-content',
					'apache-flex--apache-section-header',
				);

				if ( '' !== $no_overlay_cta_options ) {
					$no_overlay_classes[] = $no_overlay_cta_options;
				}

				$cta_wrapper_classes = array(
					'apache-flex--apache-section-header__ctas',
				);

				if ( '' !== $no_overlay_cta_options && 'none' !== $no_overlay_cta_options ) {
					$cta_wrapper_classes[] = 'apache-flex--apache-section-header__ctas--' . $no_overlay_cta_options;
				}
				?>
				<div class="<?php echo esc_attr( implode( ' ', array_filter( $no_overlay_classes ) ) ); ?>">
					<?php if ( $no_overlay_eyebrow ) : ?>
						<p class="h5 section-name <?php echo esc_attr( $no_overlay_line_color ); ?>">
							<?php echo esc_html( $no_overlay_eyebrow ); ?>
						</p>
					<?php endif; ?>

						<div class="apache-flex--apache-section-header__wrapper">
							<?php if ( $no_overlay_headline ) : ?>
								<<?php echo esc_attr( $heading_tag ); ?> class="apache-flex--apache-section-header__headline">
									<?php echo wp_kses_post( $no_overlay_headline ); ?>
								</<?php echo esc_attr( $heading_tag ); ?>>
							<?php endif; ?>

						<?php if ( 'link' === $no_overlay_cta_options && ! empty( $no_overlay_link_cta ) ) : ?>
							<div class="<?php echo esc_attr( implode( ' ', array_filter( $cta_wrapper_classes ) ) ); ?>">
								<a
									class="<?php echo esc_attr( $no_overlay_link_cta['class'] ?? 'btn btn-more' ); ?>"
									href="<?php echo esc_url( $no_overlay_link_cta['url'] ); ?>"
									target="<?php echo esc_attr( apache_2026_sanitize_link_target( $no_overlay_link_cta['target'] ?? '_self' ) ); ?>"
									<?php if ( apache_2026_link_rel( $no_overlay_link_cta['target'] ?? '_self' ) ) : ?>
										rel="noopener noreferrer"
									<?php endif; ?>
								>
									<?php echo esc_html( $no_overlay_link_cta['title'] ?? '' ); ?>
								</a>
							</div>
						<?php elseif ( 'none' !== $no_overlay_cta_options && ! empty( $no_overlay_button_ctas ) ) : ?>
							<div class="<?php echo esc_attr( implode( ' ', array_filter( $cta_wrapper_classes ) ) ); ?>">
								<?php foreach ( $no_overlay_button_ctas as $cta ) : ?>
									<a
										class="<?php echo esc_attr( $cta['class'] ?? 'btn btn-primary' ); ?>"
										href="<?php echo esc_url( $cta['url'] ?? '' ); ?>"
										target="<?php echo esc_attr( apache_2026_sanitize_link_target( $cta['target'] ?? '_self' ) ); ?>"
										<?php if ( apache_2026_link_rel( $cta['target'] ?? '_self' ) ) : ?>
											rel="noopener noreferrer"
										<?php endif; ?>
									>
										<?php echo esc_html( $cta['title'] ?? '' ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $no_overlay_content ) : ?>
						<div class="apache-flex--apache-section-header__content">
							<?php echo apache_2026_kses_wysiwyg_content( $no_overlay_content ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<div class="apache-hero__content">
					<?php if ( $eyebrow ) : ?>
						<p class="apache-hero__eyebrow section-name <?php echo esc_attr( ( $has_media && $has_text_overlay ) ? 'light' : 'gold' ); ?>"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>

					<?php if ( $title ) : ?>
						<<?php echo esc_attr( $heading_tag ); ?> class="apache-hero__title"><?php echo esc_html( $title ); ?></<?php echo esc_attr( $heading_tag ); ?>>
					<?php endif; ?>

					<?php if ( $content ) : ?>
						<div class="apache-hero__copy">
							<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $button['label'] ) && ! empty( $button['url'] ) ) : ?>
						<p class="apache-hero__actions">
							<a
								class="<?php echo esc_attr( $button_classes ); ?>"
								href="<?php echo esc_url( $button['url'] ); ?>"
								target="<?php echo esc_attr( apache_2026_sanitize_link_target( $button['target'] ?? '_self' ) ); ?>"
								<?php if ( apache_2026_link_rel( $button['target'] ?? '_self' ) ) : ?>
									rel="noopener noreferrer"
								<?php endif; ?>
							>
								<?php echo esc_html( $button['label'] ); ?>
							</a>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $show_cut_overlay ) : ?>
			<div class="apache-hero__cut-overlay" aria-hidden="true"></div>
		<?php endif; ?>
		<?php if ( $show_wavy_lines ) : ?>
			<?php $render_wavy_lines(); ?>
		<?php endif; ?>
	</header>
<?php endif; ?>
