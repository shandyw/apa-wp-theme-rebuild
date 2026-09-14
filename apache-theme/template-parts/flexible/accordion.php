<?php
/**
 * Flexible layout: Accordion.
 *
 * ACF layout key: accordion
 *
 * @package Apache_2026
 */

if ( ! function_exists( 'have_rows' ) || ! have_rows( 'accordion_items' ) ) {
	return;
}

$accordion_name = get_sub_field( 'accordion_name' );
$accordion_id   = get_sub_field( 'accordion_id' );

$normalize_accordion_style = static function ( $value ): string {
	if ( is_array( $value ) ) {
		$value = reset( $value );
	}

	if ( ! is_string( $value ) ) {
		return 'default';
	}

	$value = strtolower( trim( $value ) );
	$value = str_replace( array( '_', ' ' ), '-', $value );

	return ( false !== strpos( $value, 'gray' ) && false !== strpos( $value, 'bars' ) )
		? 'gray-bars'
		: 'default';
};

$accordion_style_candidates = array();

$collect_accordion_style = static function ( &$candidates, $value ): void {
	if ( is_array( $value ) ) {
		foreach ( $value as $item ) {
			if ( is_scalar( $item ) ) {
				$candidates[] = (string) $item;
			}
		}

		return;
	}

	if ( is_scalar( $value ) ) {
		$candidates[] = (string) $value;
	}
};

$is_gray_bars_style = static function ( ...$values ) use ( $normalize_accordion_style ): bool {
	foreach ( $values as $value ) {
		if ( 'gray-bars' === $normalize_accordion_style( $value ) ) {
			return true;
		}
	}

	return false;
};


$accordion_style = get_sub_field( 'accordion_style' );
$collect_accordion_style( $accordion_style_candidates, $accordion_style );
$accordion_id_sanitized = is_string( $accordion_id ) ? sanitize_title( $accordion_id ) : '';

if ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) {
	$accordion_style = get_sub_field( 'field_accordion_style_2026' );
	$collect_accordion_style( $accordion_style_candidates, $accordion_style );
}
$items          = array();

if ( ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) && function_exists( 'get_row' ) ) {
	$row_data = get_row( false );

	if ( is_array( $row_data ) ) {
		if ( array_key_exists( 'accordion_style', $row_data ) ) {
			$accordion_style = $row_data['accordion_style'];
			$collect_accordion_style( $accordion_style_candidates, $accordion_style );
		} elseif ( array_key_exists( 'field_accordion_style_2026', $row_data ) ) {
			$accordion_style = $row_data['field_accordion_style_2026'];
			$collect_accordion_style( $accordion_style_candidates, $accordion_style );
		}
	}
}

if ( ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) && function_exists( 'get_sub_field_object' ) ) {
	$accordion_style_field = get_sub_field_object( 'accordion_style', false, false );

	if ( is_array( $accordion_style_field ) && array_key_exists( 'value', $accordion_style_field ) ) {
		$accordion_style = $accordion_style_field['value'];
		$collect_accordion_style( $accordion_style_candidates, $accordion_style );
	}
}

if ( ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) && function_exists( 'get_sub_field_object' ) ) {
	$accordion_style_field = get_sub_field_object( 'field_accordion_style_2026', false, false );

	if ( is_array( $accordion_style_field ) && array_key_exists( 'value', $accordion_style_field ) ) {
		$accordion_style = $accordion_style_field['value'];
		$collect_accordion_style( $accordion_style_candidates, $accordion_style );
	}
}

if ( ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) && function_exists( 'get_row_index' ) ) {
	$current_row_index = get_row_index();
	$current_post_id   = get_the_ID();

	if ( $current_row_index && $current_post_id ) {
		$meta_row_index = max( 0, (int) $current_row_index - 1 );
		$meta_value     = get_post_meta( $current_post_id, 'blocks_' . $meta_row_index . '_accordion_style', true );

		if ( is_string( $meta_value ) && '' !== trim( $meta_value ) ) {
			$accordion_style = $meta_value;
			$collect_accordion_style( $accordion_style_candidates, $accordion_style );
		}
	}
}

if ( ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) && '' !== $accordion_id_sanitized ) {
	$current_post_id = get_the_ID();
	$meta_value      = $current_post_id ? get_post_meta( $current_post_id, 'apache_2026_accordion_style_' . $accordion_id_sanitized, true ) : '';

	if ( is_string( $meta_value ) && '' !== trim( $meta_value ) ) {
		$accordion_style = $meta_value;
		$collect_accordion_style( $accordion_style_candidates, $accordion_style );
	}
}

if ( ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) && '' !== $accordion_id_sanitized ) {
	$current_post_id = get_the_ID();

	if ( $current_post_id ) {
		$all_meta = get_post_meta( $current_post_id );

		if ( is_array( $all_meta ) ) {
			foreach ( $all_meta as $meta_key => $meta_values ) {
				if ( ! is_string( $meta_key ) || ! preg_match( '/^blocks_(\d+)_accordion_id$/', $meta_key, $matches ) ) {
					continue;
				}

				$saved_accordion_id = '';

				if ( is_array( $meta_values ) && isset( $meta_values[0] ) && is_string( $meta_values[0] ) ) {
					$saved_accordion_id = sanitize_title( $meta_values[0] );
				}

				if ( $saved_accordion_id !== $accordion_id_sanitized ) {
					continue;
				}

				$matched_row_index = (int) $matches[1];
				$meta_value        = get_post_meta( $current_post_id, 'blocks_' . $matched_row_index . '_accordion_style', true );

				if ( is_string( $meta_value ) && '' !== trim( $meta_value ) ) {
					$accordion_style = $meta_value;
					$collect_accordion_style( $accordion_style_candidates, $accordion_style );
					break;
				}
			}
		}
	}
}

if ( ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) && function_exists( 'get_field' ) ) {
	$current_post_id = get_the_ID();
	$blocks_rows     = $current_post_id ? get_field( 'blocks', $current_post_id, false ) : null;
	$accordion_block_rows = array();

	if ( is_array( $blocks_rows ) ) {
		foreach ( $blocks_rows as $block_row ) {
			if ( ! is_array( $block_row ) ) {
				continue;
			}

			$layout_name = $block_row['acf_fc_layout'] ?? '';

			if ( 'accordion' !== $layout_name ) {
				continue;
			}

			$row_accordion_id = '';

			if ( isset( $block_row['accordion_id'] ) && is_string( $block_row['accordion_id'] ) ) {
				$row_accordion_id = sanitize_title( $block_row['accordion_id'] );
			} elseif ( isset( $block_row['field_691f8dbd0b7e2'] ) && is_string( $block_row['field_691f8dbd0b7e2'] ) ) {
				$row_accordion_id = sanitize_title( $block_row['field_691f8dbd0b7e2'] );
			}

			$row_style = $block_row['accordion_style'] ?? ( $block_row['field_accordion_style_2026'] ?? null );

			if ( is_array( $row_style ) ) {
				$row_style = reset( $row_style );
			}

			$accordion_block_rows[] = array(
				'id'    => $row_accordion_id,
				'style' => is_string( $row_style ) ? trim( $row_style ) : '',
				'row'   => $block_row,
			);

			if ( '' !== $accordion_id_sanitized && $row_accordion_id === $accordion_id_sanitized && is_string( $row_style ) && '' !== trim( $row_style ) ) {
				$accordion_style = $row_style;
				$collect_accordion_style( $accordion_style_candidates, $accordion_style );
				break;
			}
		}

		if ( ( null === $accordion_style || false === $accordion_style || '' === $accordion_style ) && 1 === count( $accordion_block_rows ) ) {
			$single_row_style = $accordion_block_rows[0]['style'] ?? '';

			if ( is_string( $single_row_style ) && '' !== trim( $single_row_style ) ) {
				$accordion_style = $single_row_style;
				$collect_accordion_style( $accordion_style_candidates, $accordion_style );
			}
		}
	}
}

$accordion_name = is_string( $accordion_name ) ? trim( $accordion_name ) : '';
$accordion_id   = $accordion_id_sanitized;
$accordion_style_raw = $accordion_style;
$accordion_style = $normalize_accordion_style( $accordion_style );
$accordion_style_current = get_sub_field( 'accordion_style' );
$accordion_style_legacy  = get_sub_field( 'field_accordion_style_2026' );
$accordion_name_current  = get_sub_field( 'accordion_name' );
$parent_row_data = function_exists( 'get_row' ) ? get_row( false ) : null;

if ( '' === $accordion_name && is_string( $accordion_name_current ) && '' !== trim( $accordion_name_current ) ) {
	$accordion_name = trim( $accordion_name_current );
}

if (
	'' === $accordion_name
	&& is_array( $parent_row_data )
	&& array_key_exists( 'field_691f939ebcdc2', $parent_row_data )
	&& is_string( $parent_row_data['field_691f939ebcdc2'] )
) {
	$accordion_name = trim( $parent_row_data['field_691f939ebcdc2'] );
}

if (
	'default' === $accordion_style
	&& is_array( $parent_row_data )
) {
	if ( is_array( $parent_row_data ) && array_key_exists( 'field_accordion_style_2026', $parent_row_data ) ) {
		$accordion_style_raw = $parent_row_data['field_accordion_style_2026'];
		$collect_accordion_style( $accordion_style_candidates, $accordion_style_raw );
		$accordion_style = $normalize_accordion_style( $accordion_style_raw );
	}
}

if ( '' === $accordion_id ) {
	$accordion_id = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'accordion-' ) : 'accordion-' . uniqid();
}

if ( ! preg_match( '/^[A-Za-z]/', $accordion_id ) ) {
	$accordion_id = 'accordion-' . $accordion_id;
}

while ( have_rows( 'accordion_items' ) ) {
	the_row();

	$title   = get_sub_field( 'accordion_title' );
	$content = get_sub_field( 'accordion_content' );

	$title   = is_string( $title ) ? trim( $title ) : '';
	$content = is_string( $content ) ? trim( $content ) : '';

	if ( '' === $title && '' === $content ) {
		continue;
	}

	$items[] = array(
		'title'   => $title,
		'content' => $content,
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

$classes = array(
	'apache-flex',
	'apache-flex--accordion',
	'accordion-block',
);
$accordion_row_for_classes = function_exists( 'get_row' ) ? get_row( false ) : null;
$accordion_row_style_for_classes = is_array( $accordion_row_for_classes ) && array_key_exists( 'field_accordion_style_2026', $accordion_row_for_classes )
	? $accordion_row_for_classes['field_accordion_style_2026']
	: null;

if ( 'gray-bars' === $accordion_style || 'gray-bars' === $normalize_accordion_style( $accordion_style_raw ) ) {
	$classes[] = 'gray-bars';
}

if (
	! in_array( 'gray-bars', $classes, true )
	&& $is_gray_bars_style(
		$accordion_style,
		$accordion_style_raw,
		$accordion_style_current,
		$accordion_style_legacy,
		$accordion_style_candidates,
		$accordion_row_style_for_classes
	)
) {
	$classes[] = 'gray-bars';
}


$styles = array();
$accordion_row_debug = $accordion_row_for_classes;
$accordion_name_render = $accordion_name;

if ( '' === $accordion_name_render ) {
	$accordion_name_fallback = get_sub_field( 'accordion_name' );

	if ( is_string( $accordion_name_fallback ) && '' !== trim( $accordion_name_fallback ) ) {
		$accordion_name_render = trim( $accordion_name_fallback );
	} elseif (
		is_array( $accordion_row_debug )
		&& array_key_exists( 'field_691f939ebcdc2', $accordion_row_debug )
		&& is_string( $accordion_row_debug['field_691f939ebcdc2'] )
		&& '' !== trim( $accordion_row_debug['field_691f939ebcdc2'] )
	) {
		$accordion_name_render = trim( $accordion_row_debug['field_691f939ebcdc2'] );
	}
}

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
	<div class="apache-flex__inner apache-flex--accordion__inner">
		<?php if ( $accordion_name_render ) : ?>
			<header class="apache-flex--accordion__header">
				<h3 class="apache-flex--accordion__title mb-0">
					<?php echo esc_html( $accordion_name_render ); ?>
				</h3>
			</header>
		<?php endif; ?>

		<div class="accordion-wrapper apache-flex--accordion__wrapper" id="<?php echo esc_attr( $accordion_id ); ?>">
			
				<div class="accordion-toggle-all">
					<?php if ( count( $items ) > 1 ) : ?>
					<a
						href="#"
						class="accordion-toggle-link"
						data-accordion-toggle="#<?php echo esc_attr( $accordion_id ); ?>"
						data-state="closed"
					>
						<?php esc_html_e( 'Show all', 'apache-2026' ); ?>
					</a>
					<?php endif; ?>
				</div>
			

			<div class="accordion">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$item_id   = $accordion_id . '-item-' . (string) $index;
					$header_id = $item_id . '-header';
					$panel_id  = $item_id . '-panel';
					?>

					<div class="accordion-item">
						<h3 class="accordion-header" id="<?php echo esc_attr( $header_id ); ?>">
							<button
								class="accordion-trigger"
								type="button"
								aria-expanded="false"
								aria-controls="<?php echo esc_attr( $panel_id ); ?>"
								data-accordion-target="#<?php echo esc_attr( $panel_id ); ?>"
							>
								<?php echo esc_html( $item['title'] ); ?>
							</button>
						</h3>

						<div
							id="<?php echo esc_attr( $panel_id ); ?>"
							class="accordion-panel"
							role="region"
							aria-labelledby="<?php echo esc_attr( $header_id ); ?>"
							hidden
						>
							<div class="accordion-content apache-wysiwyg">
								<?php echo apache_2026_kses_wysiwyg_content( $item['content'] ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
