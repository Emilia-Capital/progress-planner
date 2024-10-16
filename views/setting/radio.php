<?php
/**
 * Template for a `select` setting.
 *
 * @package Progress_Planner
 */

?>
<fieldset id="prpl-setting-fieldset-<?php echo esc_attr( $prpl_setting['id'] ); ?>">
	<legend><?php echo wp_kses_post( $prpl_setting['label'] ); ?></legend>
	<div class="radios">
		<?php foreach ( $prpl_setting['options'] as $prpl_pro_option_value => $prpl_pro_option_label ) : ?>
			<label>
				<input
					type="radio"
					id="prpl-setting-<?php echo esc_attr( $prpl_setting['id'] ); ?>"
					name="<?php echo esc_attr( $prpl_setting['id'] ); ?>"
					value="<?php echo esc_attr( $prpl_pro_option_value ); ?>"
					<?php if ( isset( $prpl_setting['page'] ) && $prpl_setting['page'] ) : ?>
						data-page="<?php echo esc_attr( $prpl_setting['page'] ); ?>"
					<?php endif; ?>
					<?php echo ( (string) $prpl_pro_option_value === (string) $prpl_setting['value'] ) ? ' checked' : ''; ?>
				>
				<?php echo wp_kses_post( $prpl_pro_option_label ); ?>
			</label>
		<?php endforeach; ?>
	</div>
</fieldset>
