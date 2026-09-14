<?php
/**
 * Contact Information ACF field group.
 *
 * @package Apache_2026
 */

$data             = isset( $args['data'] ) && is_array( $args['data'] ) ? $args['data'] : array();
$contact_blocks   = isset( $data['contact_blocks'] ) && is_array( $data['contact_blocks'] ) ? $data['contact_blocks'] : array();
$department_block = isset( $data['department_block'] ) && is_array( $data['department_block'] ) ? $data['department_block'] : array();
$departments      = isset( $department_block['departments'] ) && is_array( $department_block['departments'] ) ? $department_block['departments'] : array();
$select_id        = wp_unique_id( 'apache-contact-department-' );
?>

<div class="apache-contact-info" data-contact-information>
	<?php foreach ( $contact_blocks as $block ) : ?>
		<?php
		$headline       = isset( $block['headline'] ) && is_string( $block['headline'] ) ? $block['headline'] : '';
		$content        = isset( $block['content'] ) && is_string( $block['content'] ) ? $block['content'] : '';
		$margin_top     = isset( $block['margin_top'] ) && is_int( $block['margin_top'] ) ? $block['margin_top'] : null;
		$bottom_divider = ! empty( $block['bottom_divider'] );
		$style          = null !== $margin_top ? sprintf( '--apache-contact-margin-top:%dpx;', $margin_top ) : '';
		?>
		<section class="apache-contact-info__block"<?php echo $style ? ' style="' . esc_attr( $style ) . '"' : ''; ?>>
			<div class="apache-contact-info__inner">
				<?php if ( '' !== $headline ) : ?>
					<h2 class="apache-contact-info__headline"><?php echo esc_html( $headline ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $content ) : ?>
					<div class="apache-contact-info__content apache-wysiwyg">
						<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $bottom_divider ) : ?>
					<div class="apache-contact-info__divider" aria-hidden="true"></div>
				<?php endif; ?>
			</div>
		</section>
	<?php endforeach; ?>

	<?php if ( ! empty( $department_block['headline'] ) || ! empty( $department_block['sub_headline'] ) || ! empty( $departments ) ) : ?>
		<?php
		$department_margin = isset( $department_block['margin_top'] ) && is_int( $department_block['margin_top'] ) ? $department_block['margin_top'] : null;
		$department_style  = null !== $department_margin ? sprintf( '--apache-contact-margin-top:%dpx;', $department_margin ) : '';
		?>
		<section class="departments-module apache-contact-departments"<?php echo $department_style ? ' style="' . esc_attr( $department_style ) . '"' : ''; ?> data-contact-departments>
			<div class="apache-contact-info__inner apache-contact-departments__inner">
				<?php if ( ! empty( $department_block['headline'] ) ) : ?>
					<h2 class="headline apache-contact-departments__headline"><?php echo esc_html( $department_block['headline'] ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $department_block['sub_headline'] ) ) : ?>
					<p class="apache-contact-departments__subheadline"><?php echo esc_html( $department_block['sub_headline'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $departments ) ) : ?>
					<div class="apache-contact-departments__control">
						<label class="screen-reader-text" for="<?php echo esc_attr( $select_id ); ?>">
							<?php esc_html_e( 'Select Department', 'apache-2026' ); ?>
						</label>
						<select id="<?php echo esc_attr( $select_id ); ?>" class="custom apache-contact-departments__select" name="department" data-contact-department-select>
							<option value=""><?php esc_html_e( 'Select Department', 'apache-2026' ); ?></option>
							<?php foreach ( $departments as $department ) : ?>
								<?php
								$key  = isset( $department['key'] ) && is_string( $department['key'] ) ? $department['key'] : '';
								$name = isset( $department['name'] ) && is_string( $department['name'] ) ? $department['name'] : '';
								?>
								<?php if ( '' !== $key && '' !== $name ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $name ); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="apache-contact-departments__panels" aria-live="polite">
						<?php foreach ( $departments as $department ) : ?>
							<?php
							$key     = isset( $department['key'] ) && is_string( $department['key'] ) ? $department['key'] : '';
							$name    = isset( $department['name'] ) && is_string( $department['name'] ) ? $department['name'] : '';
							$content = isset( $department['content'] ) && is_string( $department['content'] ) ? $department['content'] : '';
							?>
							<?php if ( '' !== $key && '' !== $content ) : ?>
								<div class="apache-contact-departments__panel apache-wysiwyg" id="apache-contact-department-<?php echo esc_attr( $key ); ?>" data-contact-department-panel data-department="<?php echo esc_attr( $key ); ?>" hidden>
									<?php if ( '' !== $name ) : ?>
										<h3 class="apache-contact-departments__panel-title"><?php echo esc_html( $name ); ?></h3>
									<?php endif; ?>

									<?php echo apache_2026_kses_wysiwyg_content( $content ); ?>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<div class="banner-topography apache-contact-info__topography" aria-hidden="true"></div>
</div>
