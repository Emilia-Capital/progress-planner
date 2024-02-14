<?php
/**
 * Show the filters form.
 *
 * This modifies the stats graphs.
 *
 * @package ProgressPlanner
 */

// Values for the graph filters.
$prpl_filters_intervals = [
	'days'   => __( 'Days', 'progress-planner' ),
	'weeks'  => __( 'Weeks', 'progress-planner' ),
	'months' => __( 'Months', 'progress-planner' ),
];

?>
<div class="progress-planner-form-filters">
	<p><?php esc_html_e( 'Filter results', 'progress-planner' ); ?></p>
	<form method="POST" style="display:flex;">
		<select name="interval">
			<?php foreach ( $prpl_filters_intervals as $prpl_interval_name => $prpl_interval_label ) : ?>
				<option
					name="<?php echo esc_attr( $prpl_interval_name ); ?>"
					<?php echo $prpl_interval_name === $prpl_filters_interval ? ' selected' : ''; ?>
				><?php echo esc_html( $prpl_interval_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<input name="number" type="number" value="<?php echo esc_attr( $prpl_filters_number ); ?>">
		<input type="submit" class="button button-secondary" value="<?php esc_attr_e( 'Update', 'progress-planner' ); ?>">
	</form>
</div>
