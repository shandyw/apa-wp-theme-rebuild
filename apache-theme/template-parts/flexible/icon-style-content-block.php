<?php
/**
 * Flexible layout: Icon Style Content Block.
 *
 * ACF layout key: icon_style_content_block
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'have_rows' ) || ! have_rows( 'icon_info_block' ) ) {
	return;
}

$items = array();

while ( have_rows( 'icon_info_block' ) ) {
	the_row();

	$icon    = get_sub_field( 'icon_image' );
	$title   = get_sub_field( 'title' );
	$content = get_sub_field( 'content' );
	$button  = get_sub_field( 'button' );

	$icon_url    = '';
	$icon_alt    = '';
	$icon_width  = '';
	$icon_height = '';

	if ( is_array( $icon ) ) {
		$icon_url    = isset( $icon['url'] ) && is_string( $icon['url'] ) ? trim( $icon['url'] ) : '';
		$icon_alt    = isset( $icon['alt'] ) && is_string( $icon['alt'] ) ? trim( $icon['alt'] ) : '';
		$icon_width  = isset( $icon['width'] ) ? absint( $icon['width'] ) : '';
		$icon_height = isset( $icon['height'] ) ? absint( $icon['height'] ) : '';
	} elseif ( is_string( $icon ) ) {
		$icon_url = trim( $icon );
	}

	$title   = is_string( $title ) ? trim( $title ) : '';
	$content = is_string( $content ) ? trim( $content ) : '';

	$button_label  = '';
	$button_url    = '';
	$button_target = '_self';

	if ( is_array( $button ) ) {
		$button_label = isset( $button['button_text'] ) && is_string( $button['button_text'] ) ? trim( $button['button_text'] ) : '';

		if ( ! empty( $button['link_to_page'] ) && isset( $button['page'] ) && is_string( $button['page'] ) ) {
			$button_url = trim( $button['page'] );
		} elseif ( ! empty( $button['link_to_url'] ) && isset( $button['link'] ) ) {
			$link = $button['link'];

			if ( is_array( $link ) ) {
				$button_url    = isset( $link['url'] ) && is_string( $link['url'] ) ? trim( $link['url'] ) : '';
				$button_target = isset( $link['target'] ) && is_string( $link['target'] ) ? trim( $link['target'] ) : '_self';
			}
		}
	}

	$button_target = in_array( $button_target, array( '_blank', '_self' ), true ) ? $button_target : '_self';

	if ( '' === $icon_url && '' === $title && '' === $content && ( '' === $button_label || '' === $button_url ) ) {
		continue;
	}

	$items[] = array(
		'icon_url'      => $icon_url,
		'icon_alt'      => $icon_alt,
		'icon_width'    => $icon_width,
		'icon_height'   => $icon_height,
		'title'         => $title,
		'content'       => $content,
		'button_label'  => $button_label,
		'button_url'    => $button_url,
		'button_target' => $button_target,
	);
}

if ( empty( $items ) ) {
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
?>

<section
	<?php if ( $section_id ) : ?>
		id="<?php echo esc_attr( $section_id ); ?>"
	<?php endif; ?>
	class="apache-flex apache-flex--icon-style-content-block investors"
>
	<div class="apache-flex__inner apache-flex--icon-style-content-block__inner">
		<div class="icon-info-blocks apache-flex--icon-style-content-block__grid">
			<?php foreach ( $items as $item ) : ?>
				<article class="icon-info-container apache-flex--icon-style-content-block__card">
					<?php if ( $item['icon_url'] ) : ?>
						<figure class="apache-flex--icon-style-content-block__icon-wrap">
							<img
								class="large-icons apache-flex--icon-style-content-block__icon"
								src="<?php echo esc_url( $item['icon_url'] ); ?>"
								alt="<?php echo esc_attr( $item['icon_alt'] ); ?>"
								<?php if ( $item['icon_width'] ) : ?>
									width="<?php echo esc_attr( (string) $item['icon_width'] ); ?>"
								<?php endif; ?>
								<?php if ( $item['icon_height'] ) : ?>
									height="<?php echo esc_attr( (string) $item['icon_height'] ); ?>"
								<?php endif; ?>
								loading="lazy"
								decoding="async"
							>
						</figure>
					<?php endif; ?>

					<div class="content apache-flex--icon-style-content-block__body">
						<?php if ( $item['title'] ) : ?>
							<div class="title-container apache-flex--icon-style-content-block__title-wrap">
								<h3 class="title apache-flex--icon-style-content-block__title">
									<span><?php echo esc_html( $item['title'] ); ?></span>
								</h3>
							</div>
						<?php endif; ?>

						<?php if ( $item['content'] ) : ?>
							<div class="apache-wysiwyg apache-flex--icon-style-content-block__content">
								<?php echo apache_2026_kses_wysiwyg_content( $item['content'] ); ?>
							</div>
						<?php endif; ?>

						<?php if ( $item['button_label'] && $item['button_url'] ) : ?>
							<p class="btn-container apache-flex--icon-style-content-block__cta">
								<a
									class="btn btn-primary"
									href="<?php echo esc_url( $item['button_url'] ); ?>"
									target="<?php echo esc_attr( $item['button_target'] ); ?>"
									<?php if ( '_blank' === $item['button_target'] ) : ?>
										rel="noopener noreferrer"
									<?php endif; ?>
								>
									<?php echo esc_html( $item['button_label'] ); ?>
								</a>
							</p>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
