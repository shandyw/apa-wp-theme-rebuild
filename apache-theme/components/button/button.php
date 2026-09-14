<?php
/**
 * Button component.
 *
 * Expected args:
 * - url     string
 * - label   string
 * - target  string
 * - variant string primary|secondary|tertiary
 * - class   string optional
 */

$url     = $url ?? '';
$label   = $label ?? '';
$target  = $target ?? '';
$variant = $variant ?? 'primary';
$class   = $class ?? '';

if (empty($url) || empty($label)) {
    return;
}

$allowed_variants = [
    'primary',
    'secondary',
    'tertiary',
];

if (! in_array($variant, $allowed_variants, true)) {
    $variant = 'primary';
}

$classes = trim(sprintf(
    'apache-button apache-button--%s %s',
    esc_attr($variant),
    esc_attr($class)
));

$target     = apache_2026_sanitize_link_target($target);
$target_attr = sprintf(' target="%s"', esc_attr($target));
$rel_attr    = apache_2026_link_rel($target) ? sprintf(' rel="%s"', esc_attr(apache_2026_link_rel($target))) : '';
?>

<a
    class="<?php echo esc_attr($classes); ?>"
    href="<?php echo esc_url($url); ?>"
    <?php echo $target_attr; ?>
    <?php echo $rel_attr; ?>
>
    <?php echo esc_html($label); ?>
</a>



<!-- BASIC USAGE -->

<!-- apache_2026_component('button', [
    'url'     => '/contact-us/',
    'label'   => 'Contact Us',
    'variant' => 'primary',
]); -->



<!-- ACF LINK FIELD USAGE -->

<!-- $cta = get_field('cta');

if ($cta) {
    apache_2026_component('button', [
        'url'     => $cta['url'] ?? '',
        'label'   => $cta['title'] ?? '',
        'target'  => $cta['target'] ?? '',
        'variant' => 'primary',
    ]);
} -->
    

<!-- EXTRA CLASSES USAGE

apache_2026_component('button', [
    'url'     => '/careers/',
    'label'   => 'View Careers',
    'target'  => '',
    'variant' => 'secondary',
    'class'   => 'hero__button',
]); -->
