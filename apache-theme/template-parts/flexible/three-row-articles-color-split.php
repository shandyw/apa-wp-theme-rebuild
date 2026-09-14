<?php
/**
 * Flexible layout: Three Row Articles Color Split.
 *
 * ACF layout key: three_row_articles_color_split
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'get_sub_field' ) ) {
	return;
}

$section_header = get_sub_field( 'section_header' );
$headline       = get_sub_field( 'headline' );
$block_color    = get_sub_field( 'block_color' );
$text_top       = (bool) get_sub_field( 'text_top' );


$section_title = '';
$line_color    = 'gold';

if ( is_array( $section_header ) ) {
	$section_title = isset( $section_header['title'] ) && is_string( $section_header['title'] )
		? trim( $section_header['title'] )
		: '';

	$line_color_value = $section_header['line_color'] ?? 'gold';

	if ( is_array( $line_color_value ) ) {
		$line_color_value = reset( $line_color_value );
	}

	$line_color = is_string( $line_color_value ) && '' !== trim( $line_color_value )
		? trim( $line_color_value )
		: 'gold';
}

$headline    = is_string( $headline ) ? trim( $headline ) : '';
$block_color = is_string( $block_color ) ? sanitize_key( $block_color ) : 'gold';
$block_color = in_array( $block_color, array( 'gold', 'green', 'yellow', 'gray' ), true ) ? $block_color : 'gold';
$articles    = array();

if ( function_exists( 'have_rows' ) && have_rows( 'articles' ) ) {
	while ( have_rows( 'articles' ) ) {
		the_row();

		$title   = get_sub_field( 'title' );
		$content = get_sub_field( 'content' );

		$title   = is_string( $title ) ? trim( $title ) : '';
		$content = is_string( $content ) ? trim( $content ) : '';

		if ( '' === $title && '' === $content ) {
			continue;
		}

		$articles[] = array(
			'title'   => $title,
			'content' => $content,
		);
	}
}

if ( '' === $section_title && '' === $headline && empty( $articles ) ) {
	return;
}

$section_id = '';

foreach ( array( 'section_id', 'anchor_id', 'anchor_tag_name' ) as $id_field ) {
	$id_value = get_sub_field( $id_field );

	if ( is_string( $id_value ) && '' !== trim( $id_value ) ) {
		$section_id = sanitize_title( $id_value );
		break;
	}
}


$line_color = sanitize_html_class( $line_color );

$classes = array(
	'apache-flex',
	'apache-flex--three-row-articles-color-split',
	'section-full-split',
	'apache-flex--color-' . $block_color,
);

if ( $text_top ) {
	$classes[] = 'apache-flex--text-top';
}

if ( count( $articles ) === 4 ) {
	$classes[] = 'apache-flex--has-four-articles';
}



$styles = array();


?>

<section
	<?php if ( $section_id ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
	<?php if ( $styles ) : ?>
		style="<?php echo esc_attr( implode( '; ', $styles ) ); ?>"
	<?php endif; ?>
	
>

	<div class="apache-flex--three-row-articles-color-split__header">
		<div class="apache-flex__inner apache-flex--three-row-articles-color-split__header-inner">
			<?php if ( $section_title ) : ?>
				<p class="h5 section-name <?php echo esc_attr( $line_color ); ?>">
					<?php echo esc_html( $section_title ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $headline ) : ?>
				<h2 class="apache-flex--three-row-articles-color-split__headline">
					<?php echo esc_html( $headline ); ?>
				</h2>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $articles ) : ?>
		<div class="apache-flex--three-row-articles-color-split__articles">
			<div class="apache-flex__inner apache-flex--three-row-articles-color-split__articles-inner">
				<div class="apache-flex--three-row-articles-color-split__square" aria-hidden="true"></div>

				<div class="apache-flex--three-row-articles-color-split__grid">
					<?php foreach ( $articles as $article ) : ?>
						<article class="apache-flex--three-row-articles-color-split__article">
							<?php if ( $article['title'] ) : ?>
								<h3 class="apache-flex--three-row-articles-color-split__article-title">
									<span><?php echo esc_html( $article['title'] ); ?></span>
								</h3>
							<?php endif; ?>

							<?php if ( $article['content'] ) : ?>
								<div class="apache-flex--three-row-articles-color-split__article-content">
									<?php echo apache_2026_kses_wysiwyg_content( $article['content'] ); ?>
								</div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
