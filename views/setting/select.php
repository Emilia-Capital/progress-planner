<?php
/**
 * Template for a `select` setting.
 *
 * @package Progress_Planner
 */

?>
<label>
	<p><?php echo wp_kses_post( $prpl_setting['label'] ); ?></p>
	<select
		id="prpl-setting-<?php echo esc_attr( $prpl_setting['id'] ); ?>"
		name="<?php echo esc_attr( $prpl_setting['id'] ); ?>"
	>
		<?php foreach ( $prpl_setting['options'] as $prpl_pro_option_value => $prpl_pro_option_label ) : ?>
			<option
				value="<?php echo esc_attr( $prpl_pro_option_value ); ?>"
				<?php echo ( $prpl_pro_option_value === $prpl_setting['value'] ) ? ' selected' : ''; ?>
			>
				<?php echo esc_html( $prpl_pro_option_label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
</label>
