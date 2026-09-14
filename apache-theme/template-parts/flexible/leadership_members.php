<?php
/**
 * Flexible layout: Leadership Members.
 *
 * ACF layout key: leadership_members
 *
 * Backward-compatible with the legacy full_width_biography layout key.
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'have_rows' ) || ! function_exists( 'get_sub_field' ) ) {
	return;
}
?>
<section class="apache-flex leadership-members">
	<div class="leadership-members__inner">
			<div class="leadership-members__grid">
				<?php if ( have_rows( 'leaderships' ) ) : ?>
					<?php
					while ( have_rows( 'leaderships' ) ) :
						the_row();

					$members      = get_sub_field( 'members' );
					$member_posts = array();
					$term_id      = 0;

					if ( $members instanceof WP_Term ) {
						$members = (int) $members->term_id;
					}

						if ( is_numeric( $members ) ) {
							$term_id = absint( $members );

							if ( $term_id > 0 ) {
								$display_member_ids = function_exists( 'apache_2026_get_leadership_category_display_member_ids' )
									? apache_2026_get_leadership_category_display_member_ids( $term_id )
									: array();

								if ( ! empty( $display_member_ids ) ) {
									foreach ( $display_member_ids as $display_member_id ) {
										$display_member_post = get_post( $display_member_id );

										if ( $display_member_post instanceof WP_Post ) {
											$member_posts[] = $display_member_post;
										}
									}
								}
							}
						} elseif ( $members instanceof WP_Post ) {
						$member_posts = array( $members );
					} elseif ( is_array( $members ) ) {
						foreach ( $members as $member ) {
							if ( $member instanceof WP_Post ) {
								$member_posts[] = $member;
							} elseif ( is_numeric( $member ) ) {
								$member_post = get_post( absint( $member ) );

								if ( $member_post instanceof WP_Post ) {
									$member_posts[] = $member_post;
								}
							}
						}
					}

					if ( empty( $member_posts ) ) {
						continue;
					}

						foreach ( $member_posts as $member_post ) {
							if ( ! $member_post instanceof WP_Post ) {
								continue;
							}

							$member_id      = (int) $member_post->ID;
							$member_slug    = is_string( $member_post->post_name ) ? $member_post->post_name : '';
							$member_name    = get_the_title( $member_id );
							$member_link    = get_permalink( $member_id );
							$member_term    = $term_id > 0 ? get_term( $term_id, 'leadership_category' ) : null;

							if ( $member_term instanceof WP_Term && is_string( $member_link ) ) {
								$member_link = add_query_arg( 'leadership_group', $member_term->slug, $member_link );
							}
						$profile_image  = function_exists( 'get_field' ) ? get_field( 'profile_image', $member_id ) : null;
						$image_id       = 0;
						$image_url      = '';
						$image_alt      = '';
						$job_title      = function_exists( 'get_field' ) ? get_field( 'job_title', $member_id ) : '';
						$title_division = function_exists( 'get_field' ) ? get_field( 'title_division', $member_id ) : '';

						if ( is_array( $profile_image ) ) {
							$image_id  = isset( $profile_image['ID'] ) ? absint( $profile_image['ID'] ) : 0;
							$image_url = isset( $profile_image['url'] ) && is_string( $profile_image['url'] ) ? trim( $profile_image['url'] ) : '';
							$image_alt = isset( $profile_image['alt'] ) && is_string( $profile_image['alt'] ) ? trim( $profile_image['alt'] ) : '';
						} elseif ( is_numeric( $profile_image ) ) {
							$image_id  = absint( $profile_image );
							$image_url = wp_get_attachment_image_url( $image_id, 'large' );
						} elseif ( is_string( $profile_image ) ) {
							$image_url = trim( $profile_image );
						}

						if ( $image_id && '' === $image_alt ) {
							$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
						}

						if ( '' === $image_alt ) {
							$image_alt = $member_name;
						}

						$job_title      = is_string( $job_title ) ? trim( $job_title ) : '';
						$title_division = is_string( $title_division ) ? trim( $title_division ) : '';
						$member_name    = is_string( $member_name ) ? trim( $member_name ) : '';
						$member_link    = is_string( $member_link ) ? $member_link : '';
						$image_url      = is_string( $image_url ) ? $image_url : '';
						$image_alt      = is_string( $image_alt ) ? $image_alt : '';
						?>

						<div
							<?php if ( '' !== $member_slug ) : ?>
								id="<?php echo esc_attr( $member_slug ); ?>"
							<?php endif; ?>
							class="card leadership-members__item"
						>
							<div class="leadership-members__image">
								<?php if ( '' !== $member_link ) : ?>
									<a href="<?php echo esc_url( $member_link ); ?>">
								<?php endif; ?>

								<?php if ( '' !== $image_url ) : ?>
									<?php if ( $image_id ) : ?>
										<?php
										echo wp_get_attachment_image(
											$image_id,
											'large',
											false,
											array(
												'alt'      => $image_alt,
												'loading'  => 'lazy',
												'decoding' => 'async',
											)
										);
										?>
									<?php else : ?>
										<img
											src="<?php echo esc_url( $image_url ); ?>"
											alt="<?php echo esc_attr( $image_alt ); ?>"
											loading="lazy"
											decoding="async"
										>
									<?php endif; ?>
								<?php endif; ?>

								<?php if ( '' !== $member_link ) : ?>
									</a>
								<?php endif; ?>
							</div>

							<div class="leadership-members__content">
								<h2 class="h4 leadership-members__name">
									<?php if ( '' !== $member_link ) : ?>
										<a href="<?php echo esc_url( $member_link ); ?>">
									<?php endif; ?>

									<?php echo esc_html( $member_name ); ?>

									<?php if ( '' !== $member_link ) : ?>
										</a>
									<?php endif; ?>
								</h2>

								<?php if ( '' !== $job_title ) : ?>
									<p class="leadership-members__meta"><?php echo esc_html( $job_title ); ?></p>
								<?php endif; ?>

								<?php if ( '' !== $title_division ) : ?>
									<p class="leadership-members__meta"><?php echo esc_html( $title_division ); ?></p>
								<?php endif; ?>
							</div>
						</div>
						<?php
					}
				endwhile;
				?>
			<?php else : ?>
				<div class="leadership-members__empty"></div>
			<?php endif; ?>
		</div>
	</div>
</section>
