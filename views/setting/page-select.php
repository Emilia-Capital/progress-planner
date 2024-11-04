<?php
/**
 * Template for a `page-select` setting.
 *
 * @package Progress_Planner
 */

$prpl_radios = [
	'yes'            => esc_html__( 'Yes', 'progress-planner' ),
	'no'             => esc_html__( 'No', 'progress-planner' ),
	'not-applicable' => esc_html__( 'I don\'t need this page', 'progress-planner' ),
];

$prpl_setting_value = isset( $prpl_setting['value'] ) ? $prpl_setting['value'] : '';

$prpl_select_value = 0;
$prpl_radio_value  = 'no';
if ( '_no_page_needed' === $prpl_setting_value ) {
	$prpl_radio_value = 'not-applicable';
} elseif ( is_numeric( $prpl_setting_value ) && 0 < $prpl_setting_value ) {
	$prpl_radio_value  = 'yes';
	$prpl_select_value = (int) $prpl_setting_value;
}
?>
<prpl-page-select>
	<div class="item-description">
		<h3><?php echo esc_html( $prpl_setting['title'] ); ?></h3>
		<p><?php echo esc_html( $prpl_setting['description'] ); ?></p>
	</div>
	<div>
		<fieldset id="prpl-setting-fieldset-<?php echo esc_attr( $prpl_setting['id'] ); ?>">
			<div class="radios">
				<?php foreach ( $prpl_radios as $prpl_r_value => $prpl_r_label ) : ?>
					<label>
						<input type="radio" id="<?php echo esc_attr( 'pages[' . esc_attr( $prpl_setting['id'] ) . '][have_page]' ); ?>" name="<?php echo esc_attr( 'pages[' . esc_attr( $prpl_setting['id'] ) . '][have_page]' ); ?>" value="<?php echo esc_attr( $prpl_r_value ); ?>" data-page="<?php echo esc_attr( $prpl_setting['page'] ); ?>" <?php checked( $prpl_radio_value, $prpl_r_value ); ?>>
						<?php echo esc_html( $prpl_r_label ); ?>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>
	</div>
	<div class="item-actions">
		<div class="prpl-select-page">
			<div data-action="select">
				<?php
				wp_dropdown_pages(
					[
						'name'             => 'pages[' . esc_attr( $prpl_setting['id'] ) . '][id]',
						'show_option_none' => '&mdash; ' . esc_html__( 'Select page', 'progress-planner' ) . ' &mdash;',
						'selected'         => $prpl_select_value, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					]
				);
				?>
			</div>
			<div data-action="edit">
				<a
					target="_blank"
					class="button"
					href=""
					data-page="<?php echo esc_attr( $prpl_setting['page'] ); ?>"
				>
					<?php esc_html_e( 'Edit', 'progress-planner' ); ?>
				</a>
			</div>
		</div>

		<div data-action="create">
			<?php
			/**
			 * TODO: Find a way to assign the post-meta for the new page.
			 */
			?>
			<a
				target="_blank"
				class="button"
				href="<?php echo esc_url( admin_url( 'post-new.php?post_type=page' ) ); ?>"
			>
				<?php esc_html_e( 'Create', 'progress-planner' ); ?>
			</a>
		</div>
		<input type="hidden" name="<?php echo esc_attr( 'pages[' . esc_attr( $prpl_setting['id'] ) . '][value]' ); ?>" value="<?php echo esc_attr( $prpl_setting_value ); ?>">
	</div>
</prpl-page-select>
