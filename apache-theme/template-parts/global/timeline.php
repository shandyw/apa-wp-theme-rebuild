<?php
/**
 * Global timeline.
 *
 * ACF field group: Timeline
 *
 * @package Apache_2026
 */

$data = isset( $args['data'] ) && is_array( $args['data'] ) ? $args['data'] : apache_2026_get_timeline_data();

if ( ! apache_2026_has_timeline_data( $data ) ) {
	return;
}

$section_header = isset( $data['section_header'] ) && is_array( $data['section_header'] ) ? $data['section_header'] : array();
$section_title  = isset( $section_header['title'] ) && is_string( $section_header['title'] ) ? trim( $section_header['title'] ) : '';
$line_color     = isset( $section_header['line_color'] ) && is_string( $section_header['line_color'] ) ? sanitize_html_class( $section_header['line_color'] ) : 'gold';
$intro_header   = isset( $data['intro_header'] ) && is_string( $data['intro_header'] ) ? trim( $data['intro_header'] ) : '';
$intro_copy     = isset( $data['intro_copy'] ) && is_string( $data['intro_copy'] ) ? trim( $data['intro_copy'] ) : '';
$groups         = isset( $data['groups'] ) && is_array( $data['groups'] ) ? $data['groups'] : array();
$label_id       = $intro_header ? 'apache-timeline-title' : ( $section_title ? 'apache-timeline-label' : '' );
?>

<section
	class="apache-timeline"
	<?php if ( $label_id ) : ?>
		aria-labelledby="<?php echo esc_attr( $label_id ); ?>"
	<?php else : ?>
		aria-label="<?php esc_attr_e( 'Timeline', 'apache-2026' ); ?>"
	<?php endif; ?>
>
	<?php if ( $section_title || $intro_header || $intro_copy ) : ?>
		<div class="apache-timeline__intro-wrap">
			<div class="apache-timeline__intro">
				<?php if ( $section_title ) : ?>
					<p id="apache-timeline-label" class="apache-timeline__eyebrow section-name <?php echo esc_attr( $line_color ); ?>">
						<?php echo esc_html( $section_title ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $intro_header ) : ?>
					<h2 id="apache-timeline-title" class="apache-timeline__title">
						<?php echo esc_html( $intro_header ); ?>
					</h2>
				<?php endif; ?>

				<?php if ( $intro_copy ) : ?>
					<div class="apache-timeline__copy apache-wysiwyg">
						<?php echo apache_2026_kses_wysiwyg_content( $intro_copy ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $groups ) ) : ?>
		<div class="apache-timeline__outer">
			<div class="apache-timeline__line" aria-hidden="true"></div>

			<?php foreach ( $groups as $group ) : ?>
				<?php
				$group_year = isset( $group['year'] ) && is_string( $group['year'] ) ? trim( $group['year'] ) : '';
				$entries    = isset( $group['entries'] ) && is_array( $group['entries'] ) ? $group['entries'] : array();
				?>

				<div class="apache-timeline__group">
					<?php if ( $group_year ) : ?>
						<h3 class="apache-timeline__year">
							<span><?php echo esc_html( $group_year ); ?></span>
						</h3>
					<?php endif; ?>

					<?php if ( ! empty( $entries ) ) : ?>
						<ol class="apache-timeline__entries">
							<?php foreach ( $entries as $entry ) : ?>
								<?php
								$event_year    = isset( $entry['year'] ) && is_string( $entry['year'] ) ? trim( $entry['year'] ) : '';
								$event_content = isset( $entry['content'] ) && is_string( $entry['content'] ) ? trim( $entry['content'] ) : '';
								$event_image   = isset( $entry['image'] ) && is_array( $entry['image'] ) ? $entry['image'] : array();
								?>

								<li class="apache-timeline__entry">
									<article class="apache-timeline__card">
										<?php if ( $event_year ) : ?>
											<h4 class="apache-timeline__event-year"><?php echo esc_html( $event_year ); ?></h4>
										<?php endif; ?>

										<?php if ( $event_content ) : ?>
											<div class="apache-timeline__event-content apache-wysiwyg">
												<?php echo apache_2026_kses_wysiwyg_content( $event_content ); ?>
											</div>
										<?php endif; ?>

										<?php if ( ! empty( $event_image['id'] ) ) : ?>
											<figure class="apache-timeline__figure">
												<?php
												echo wp_get_attachment_image(
													(int) $event_image['id'],
													'large',
													false,
													array(
														'class' => 'apache-timeline__image',
														'alt'   => isset( $event_image['alt'] ) ? $event_image['alt'] : '',
														'loading' => 'lazy',
													)
												);
												?>
											</figure>
										<?php elseif ( ! empty( $event_image['url'] ) ) : ?>
											<figure class="apache-timeline__figure">
												<img
													class="apache-timeline__image"
													src="<?php echo esc_url( $event_image['url'] ); ?>"
													alt="<?php echo esc_attr( $event_image['alt'] ?? '' ); ?>"
													<?php if ( ! empty( $event_image['width'] ) && ! empty( $event_image['height'] ) ) : ?>
														width="<?php echo esc_attr( absint( $event_image['width'] ) ); ?>"
														height="<?php echo esc_attr( absint( $event_image['height'] ) ); ?>"
													<?php endif; ?>
													loading="lazy"
													decoding="async"
												>
											</figure>
										<?php endif; ?>
									</article>
								</li>
							<?php endforeach; ?>
						</ol>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
