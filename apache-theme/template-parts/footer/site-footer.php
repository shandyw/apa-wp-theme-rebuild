<?php
/**
 * Site footer.
 *
 * @package Apache_2026
 */

$site_name = get_bloginfo( 'name' );
$hide_footer_logo = (bool) get_theme_mod( 'apache_2026_hide_footer_logo', false );
?>

<footer class="site-footer">
	<span class="site-footer__corner" aria-hidden="true"></span>

	<div class="site-footer__inner">
		<div class="site-footer__brand">
			<?php if ( ! $hide_footer_logo ) : ?>
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a class="site-footer__logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<span class="site-footer__site-name"><?php echo esc_html( $site_name ); ?></span>
					</a>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( is_active_sidebar( 'footer-brand-text' ) ) : ?>
				<div class="site-footer__brand-text">
					<?php dynamic_sidebar( 'footer-brand-text' ); ?>
				</div>
			<?php endif; ?>

			<p class="site-footer__copyright">
				<small><?php
				printf(
					/* translators: %1$s: Current year. %2$s: Site name. */
					esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'apache-2026' ),
					esc_html( date_i18n( 'Y' ) ),
					esc_html( $site_name )
				);
				?></small>
				</p>
				<p class="site-footer__copyright">
				<small>
		<?php esc_html_e( 'This material may not be published, broadcast, rewritten, or redistributed.', 'apache-2026' ); ?></small>

			</p>
		</div>

		<div class="site-footer__navigation">
			<?php get_template_part( 'template-parts/footer/footer-nav' ); ?>
			<?php get_template_part( 'template-parts/footer/footer-social' ); ?>
		</div>
	</div>

</footer>
