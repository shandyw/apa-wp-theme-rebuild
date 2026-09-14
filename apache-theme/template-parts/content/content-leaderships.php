<?php
/**
 * Leaderships post content.
 *
 * @package Apache_2026
 */

$post_id = get_the_ID();

$profile_image  = function_exists( 'get_field' ) ? get_field( 'profile_image', $post_id ) : null;
$job_title      = function_exists( 'get_field' ) ? get_field( 'job_title', $post_id ) : '';
$title_division = function_exists( 'get_field' ) ? get_field( 'title_division', $post_id ) : '';

$job_title      = is_string( $job_title ) ? trim( $job_title ) : '';
$title_division = is_string( $title_division ) ? trim( $title_division ) : '';

$image_id     = 0;
$image_url    = '';
$image_alt    = '';
$image_width  = '';
$image_height = '';

if ( is_array( $profile_image ) ) {
	$image_id     = isset( $profile_image['ID'] ) ? absint( $profile_image['ID'] ) : 0;
	$image_url    = isset( $profile_image['url'] ) && is_string( $profile_image['url'] ) ? trim( $profile_image['url'] ) : '';
	$image_alt    = isset( $profile_image['alt'] ) && is_string( $profile_image['alt'] ) ? trim( $profile_image['alt'] ) : '';
	$image_width  = isset( $profile_image['width'] ) ? absint( $profile_image['width'] ) : '';
	$image_height = isset( $profile_image['height'] ) ? absint( $profile_image['height'] ) : '';
} elseif ( is_numeric( $profile_image ) ) {
	$image_id  = absint( $profile_image );
	$image_url = wp_get_attachment_image_url( $image_id, 'large' );
} elseif ( is_string( $profile_image ) ) {
	$image_url = trim( $profile_image );
}

if ( $image_id && '' === $image_alt ) {
	$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	$image_alt = is_string( $image_alt ) ? trim( $image_alt ) : '';
}

if ( '' === $image_alt ) {
	$image_alt = get_the_title( $post_id );
}

$member_slug = get_post_field( 'post_name', $post_id );
$member_slug = is_string( $member_slug ) && '' !== $member_slug ? sanitize_title( $member_slug ) : 'leader-' . $post_id;

$navigation_term     = function_exists( 'apache_2026_get_leadership_navigation_term' ) ? apache_2026_get_leadership_navigation_term( $post_id ) : null;
$adjacent_leadership = $navigation_term instanceof WP_Term && function_exists( 'apache_2026_get_leadership_category_adjacent_posts' )
	? apache_2026_get_leadership_category_adjacent_posts( $post_id, (int) $navigation_term->term_id )
	: array();
$previous_leadership = $adjacent_leadership['previous'] ?? null;
$next_leadership     = $adjacent_leadership['next'] ?? null;

$previous_link = $previous_leadership instanceof WP_Post ? get_permalink( $previous_leadership ) : '';
$next_link     = $next_leadership instanceof WP_Post ? get_permalink( $next_leadership ) : '';

if ( $navigation_term instanceof WP_Term ) {
	$previous_link = is_string( $previous_link ) && '' !== $previous_link
		? add_query_arg( 'leadership_group', $navigation_term->slug, $previous_link )
		: '';
	$next_link     = is_string( $next_link ) && '' !== $next_link
		? add_query_arg( 'leadership_group', $navigation_term->slug, $next_link )
		: '';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'apache-flex--leaderships' ); ?>>
	<div id="<?php echo esc_attr( $member_slug ); ?>" class="apache-flex__inner apache-flex--leaderships__inner">
		<?php if ( $image_url ) : ?>
			<div class="col">
				
          
				<a href="<?php echo esc_url( $image_url ); ?>" target="_blank" title="Download <?php the_title(); ?>'s profile image">
					<img
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo esc_attr( $image_alt ); ?>"
						<?php if ( $image_width ) : ?>
							width="<?php echo esc_attr( (string) $image_width ); ?>"
						<?php endif; ?>
						<?php if ( $image_height ) : ?>
							height="<?php echo esc_attr( (string) $image_height ); ?>"
						<?php endif; ?>
						loading="lazy"
						decoding="async"
						class="profile-image cut-corner"
					>
				</a>
				
				<a class="btn btn-primary mt-15" href="<?php echo esc_url( $image_url ); ?>" target="_blank" title="Download <?php the_title(); ?>'s profile image" role="button">Download Profile Image</a>
                  <p class="mt-15">
                    <small><a href="/privacy-policy/">Get permission to use</a> <br>
                            &copy; APA Corporation
                    </small>
                  </p>
			</div>
		<?php endif; ?>

		<div class="entry-content site-container">
			<header>
				<?php the_title( '<h1 class="h2">', '</h1>' ); ?>

				<?php if ( $job_title || $title_division ) : ?>
					<div class="entry-meta">
						<?php if ( $job_title ) : ?>
							<p class="h3 mb-10"><?php echo esc_html( $job_title ); ?></p>
						<?php endif; ?>

						<?php if ( $title_division ) : ?>
							<p><?php echo esc_html( $title_division ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</header>

			
				<?php
				the_content();

				if ( $previous_leadership instanceof WP_Post || $next_leadership instanceof WP_Post ) :
					?>
					<nav class="leadership-navigation" aria-label="<?php esc_attr_e( 'Leadership navigation', 'apache-2026' ); ?>">
						<div class="nav-links">
							<div class="nav-previous">
								<?php if ( $previous_leadership instanceof WP_Post ) : ?>
									<a href="<?php echo esc_url( $previous_link ); ?>">
										<span class="meta-nav" aria-hidden="true">&larr;</span>
										<span class="post-title"><?php echo esc_html( get_the_title( $previous_leadership ) ); ?></span>
									</a>
								<?php endif; ?>
							</div>

							<div class="nav-next">
								<?php if ( $next_leadership instanceof WP_Post ) : ?>
									<a href="<?php echo esc_url( $next_link ); ?>">
										<span class="post-title"><?php echo esc_html( get_the_title( $next_leadership ) ); ?></span>
										<span class="meta-nav" aria-hidden="true">&rarr;</span>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</nav>
					<?php
				endif;
				?>
			
		</div>
	</div>
</article>
