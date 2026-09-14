<?php

/**
 * Flexible layout: Apache Section Header.
 *
 * ACF layout key: apache_eyebrow
 *
 * @package Apache_2026
 */

if (! function_exists('get_sub_field')) {
	return;
}

$eyebrow     = get_sub_field('eyebrow');
$headline    = get_sub_field('headline');
$cta_options = get_sub_field('cta_options');
$link        = get_sub_field('link');
$content        = get_sub_field('content');

$eyebrow_title = '';
$line_color    = 'gold';
$use_parent_directory = false;

if (is_array($eyebrow)) {
	$eyebrow_title = isset($eyebrow['title']) && is_string($eyebrow['title'])
		? trim($eyebrow['title'])
		: '';

	$use_parent_directory = ! empty($eyebrow['use_parent_directory']);

	$line_color = isset($eyebrow['line_color']) && is_string($eyebrow['line_color']) && '' !== trim($eyebrow['line_color'])
		? trim($eyebrow['line_color'])
		: 'gold';
}

if ($use_parent_directory) {
	$current_post = get_post();

	if ($current_post instanceof WP_Post && ! empty($current_post->post_parent)) {
		$parent_title = get_the_title((int) $current_post->post_parent);

		if (is_string($parent_title) && '' !== trim($parent_title)) {
			$eyebrow_title = trim($parent_title);
		}
	}
}

$headline = is_string($headline) ? trim($headline) : '';
$link_cta = null;
$button_ctas = array();

if (is_array($link)) {
	$link_url    = isset($link['url']) && is_string($link['url']) ? trim($link['url']) : '';
	$link_title  = isset($link['title']) && is_string($link['title']) ? trim($link['title']) : '';
	$link_target = isset($link['target']) && is_string($link['target']) ? trim($link['target']) : '';

	if ('' !== $link_url && '' !== $link_title) {
		$link_cta = array(
			'url'    => $link_url,
			'title'  => $link_title,
			'target' => in_array($link_target, array('_blank', '_self'), true) ? $link_target : '_self',
			'class'  => 'btn btn-more',
		);
	}
}

if (function_exists('have_rows') && have_rows('button_repeater')) {
	while (have_rows('button_repeater')) {
		the_row();

		$button_link = get_sub_field('button');

		if (! is_array($button_link)) {
			continue;
		}

		$button_url    = isset($button_link['url']) && is_string($button_link['url']) ? trim($button_link['url']) : '';
		$button_title  = isset($button_link['title']) && is_string($button_link['title']) ? trim($button_link['title']) : '';
		$button_target = isset($button_link['target']) && is_string($button_link['target']) ? trim($button_link['target']) : '';

		if ('' === $button_url || '' === $button_title) {
			continue;
		}

		$button_ctas[] = array(
			'url'    => $button_url,
			'title'  => $button_title,
			'target' => in_array($button_target, array('_blank', '_self'), true) ? $button_target : '_self',
			'class'  => 'centermulti' === $cta_options ? 'btn btn-minimal' : 'btn btn-primary',
		);
	}
}

if ('' === $eyebrow_title && '' === $headline) {
	return;
}

$section_id = '';

foreach (array('section_id', 'anchor_id', 'anchor_tag_name') as $id_field) {
	$id_value = get_sub_field($id_field);

	if (is_string($id_value) && '' !== trim($id_value)) {
		$section_id = sanitize_title($id_value);
		break;
	}
}


$line_color = sanitize_html_class($line_color);
$cta_options = is_string($cta_options) ? sanitize_html_class($cta_options) : '';

$is_first_module = function_exists('get_row_index') && 1 === (int) get_row_index();
$hero_is_active  = function_exists('get_field') ? (bool) get_field('show_hero') : false;
$headline_tag    = ($is_first_module && ! $hero_is_active) ? 'h1' : 'h2';
$headline_classes = array_filter(
	array(
		'apache-flex--apache-section-header__headline',
	)
);

$classes = array(
	'apache-flex',
	'apache-flex--apache-section-header',
);

if ($is_first_module) {
	$classes[] = 'first-module';
}

if ('' !== $cta_options) {
	$classes[] = $cta_options;
}

$styles = array();
$cta_wrapper_classes = array(
	'apache-flex--apache-section-header__ctas',
);

if ('' !== $cta_options && 'none' !== $cta_options) {
	$cta_wrapper_classes[] = 'apache-flex--apache-section-header__ctas--' . $cta_options;
}


?>

<section
	<?php if ($section_id) : ?>
	id="<?php echo esc_attr($section_id); ?>"
	<?php endif; ?>
	class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
	<?php if ($styles) : ?>
	style="<?php echo esc_attr(implode('; ', $styles)); ?>"
	<?php endif; ?>>
	<div class="apache-flex__inner apache-flex--apache-section-header__inner">
		<?php if ($eyebrow_title) : ?>
			<p class="h5 section-name <?php echo esc_attr($line_color); ?>">
				<?php echo esc_html($eyebrow_title); ?>
			</p>
		<?php endif; ?>
		<div class="apache-flex--apache-section-header__wrapper">
			<?php if ($headline) : ?>
				<<?php echo esc_attr($headline_tag); ?> class="<?php echo esc_attr(implode(' ', $headline_classes)); ?>">
					<?php echo wp_kses_post($headline); ?>
				</<?php echo esc_attr($headline_tag); ?>>
			<?php endif; ?>

			<?php if ('link' === $cta_options && is_array($link_cta)) : ?>
				<div class="<?php echo esc_attr(implode(' ', array_filter($cta_wrapper_classes))); ?>">
					<a
						class="<?php echo esc_attr($link_cta['class']); ?>"
						href="<?php echo esc_url($link_cta['url']); ?>"
						target="<?php echo esc_attr($link_cta['target']); ?>"
						<?php if ('_blank' === $link_cta['target']) : ?>
						rel="noopener noreferrer"
						<?php endif; ?>>
						<?php echo esc_html($link_cta['title']); ?>
					</a>
				</div>
			<?php elseif ('none' !== $cta_options && ! empty($button_ctas)) : ?>
				<div class="<?php echo esc_attr(implode(' ', array_filter($cta_wrapper_classes))); ?>">
					<?php foreach ($button_ctas as $cta) : ?>
						<a
							class="<?php echo esc_attr($cta['class']); ?>"
							href="<?php echo esc_url($cta['url']); ?>"
							target="<?php echo esc_attr($cta['target']); ?>"
							<?php if ('_blank' === $cta['target']) : ?>
							rel="noopener noreferrer"
							<?php endif; ?>>
							<?php echo esc_html($cta['title']); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $content ) : ?>
			<div class="apache-flex--apache-section-header__content">
				<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
