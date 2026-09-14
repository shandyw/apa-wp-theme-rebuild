<?php
/**
 * Desktop primary navigation.
 *
 * @package Apache_2026
 */

if ( ! has_nav_menu( 'primary' ) ) {
	return;
}

$locations = get_nav_menu_locations();
$menu_id   = isset( $locations['primary'] ) ? absint( $locations['primary'] ) : 0;
$items     = $menu_id ? wp_get_nav_menu_items( $menu_id ) : array();

if ( empty( $items ) || is_wp_error( $items ) ) {
	return;
}

$items_by_parent = array();

foreach ( $items as $item ) {
	$parent_id = absint( $item->menu_item_parent );

	if ( ! isset( $items_by_parent[ $parent_id ] ) ) {
		$items_by_parent[ $parent_id ] = array();
	}

	$items_by_parent[ $parent_id ][] = $item;
}

$build_link_attributes = static function ( WP_Post $item, string $class = '' ): string {
	$atts = array(
		'href'   => ! empty( $item->url ) ? $item->url : '',
		'target' => ! empty( $item->target ) ? $item->target : '',
		'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
		'class'  => $class,
	);

	if ( '_blank' === $atts['target'] && '' === $atts['rel'] ) {
		$atts['rel'] = 'noopener';
	}

	$attributes = '';

	foreach ( $atts as $attr => $value ) {
		if ( '' === $value ) {
			continue;
		}

		$escaped_value = 'href' === $attr ? esc_url( $value ) : esc_attr( $value );
		$attributes   .= sprintf( ' %s="%s"', esc_attr( $attr ), $escaped_value );
	}

	return $attributes;
};

$item_title = static function ( WP_Post $item ): string {
	return (string) apply_filters( 'the_title', $item->title, $item->ID );
};
?>

<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'apache-2026' ); ?>" data-primary-nav>
	<ul class="primary-nav__menu">
		<?php foreach ( $items_by_parent[0] ?? array() as $top_item ) : ?>
			<?php
			$top_item_id  = absint( $top_item->ID );
			$child_items  = $items_by_parent[ $top_item_id ] ?? array();
			$has_children = ! empty( $child_items );
			$item_classes = array( 'primary-nav__item' );

			if ( $has_children ) {
				$item_classes[] = 'primary-nav__item--has-children';
			}

			foreach ( (array) $top_item->classes as $class ) {
				if ( '' !== $class ) {
					$item_classes[] = sanitize_html_class( $class );
				}
			}

			$panel_id = 'primary-nav-panel-' . $top_item_id;
			$title    = $item_title( $top_item );
			?>
			<li class="<?php echo esc_attr( implode( ' ', array_filter( $item_classes ) ) ); ?>">
				<?php if ( $has_children ) : ?>
					<button class="primary-nav__trigger" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>">
						<?php echo esc_html( $title ); ?>
					</button>

					<div id="<?php echo esc_attr( $panel_id ); ?>" class="primary-nav__mega" aria-label="<?php echo esc_attr( $title ); ?>">
						<div class="primary-nav__mega-inner">
							<div class="primary-nav__mega-column primary-nav__mega-column--parent">
								<a<?php echo $build_link_attributes( $top_item, 'primary-nav__mega-parent-link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php echo esc_html( $title ); ?>
								</a>
							</div>

							<?php foreach ( $child_items as $child_item ) : ?>
								<?php
								$child_item_id = absint( $child_item->ID );
								$grandchildren = $items_by_parent[ $child_item_id ] ?? array();
								?>
								<div class="primary-nav__mega-column">
									<?php if ( ! empty( $grandchildren ) ) : ?>
										<a<?php echo $build_link_attributes( $child_item, 'primary-nav__mega-heading' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
											<?php echo esc_html( $item_title( $child_item ) ); ?>
										</a>

										<ul class="primary-nav__mega-list">
											<?php foreach ( $grandchildren as $grandchild ) : ?>
												<li class="primary-nav__mega-list-item">
													<a<?php echo $build_link_attributes( $grandchild, 'primary-nav__mega-link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
														<?php echo esc_html( $item_title( $grandchild ) ); ?>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									<?php else : ?>
										<a<?php echo $build_link_attributes( $child_item, 'primary-nav__mega-heading primary-nav__mega-heading--link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
											<?php echo esc_html( $item_title( $child_item ) ); ?>
										</a>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php else : ?>
					<a<?php echo $build_link_attributes( $top_item, 'primary-nav__link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $title ); ?>
					</a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
