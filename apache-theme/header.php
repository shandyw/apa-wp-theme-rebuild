<?php
/**
 * The header for the theme.
 *
 * @package Apache_2026
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta property="og:image" content="https://www.apachecorp.com/wp-content/uploads/2020/08/Apache_Homepage_Suriname_Total_.jpg" />
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main">
	<?php esc_html_e( 'Skip to content', 'apache-2026' ); ?>
</a>

<?php get_template_part( 'template-parts/header/site-header' ); ?>

<main id="main" class="site-main">
