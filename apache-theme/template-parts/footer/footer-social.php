<?php
/**
 * Footer social links and legal notice.
 *
 * @package Apache_2026
 */

$social_links = function_exists( 'apache_2026_social_links' ) ? apache_2026_social_links() : array();
$has_social_links = array_filter(
	$social_links,
	static function ( array $link ): bool {
		return ! empty( $link['url'] );
	}
);
?>

<div class="footer-social">
	<?php if ( ! empty( $has_social_links ) ) : ?>
		<nav class="footer-social__nav" aria-label="<?php esc_attr_e( 'Social Menu', 'apache-2026' ); ?>">
			<ul class="footer-social__menu">
				<?php foreach ( $social_links as $network => $link ) : ?>
					<?php
					$url   = ! empty( $link['url'] ) ? esc_url( $link['url'] ) : '';
					$icon  = function_exists( 'apache_2026_social_icon' ) ? apache_2026_social_icon( (string) $network ) : '';
					$label = isset( $link['label'] ) ? (string) $link['label'] : (string) $network;

					if ( '' === $url || '' === $icon ) {
						continue;
					}
					?>
					<li class="footer-social__item">
						<a class="footer-social__link" href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer">
							<?php echo $icon; ?>
							<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>

	
</div>
