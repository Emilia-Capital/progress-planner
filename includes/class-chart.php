<?php
/**
 * Generate charts for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Render a chart.
 */
class Chart {

	/**
	 * Render the chart.
	 *
	 * @param string $id      The ID of the chart.
	 * @param string $type    The type of chart.
	 * @param array  $data    The data for the chart.
	 * @param array  $options The options for the chart.
	 *
	 * @return void
	 */
	public function render_chart( $id, $type, $data, $options = [] ) {
		$id = 'progress-planner-chart-' . $id;

		$options['responsive'] = true;

		// TODO: This should be properly enqueued.
		// phpcs:ignore
		echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
		?>

		<canvas id="<?php echo sanitize_key( $id ); ?>" style="max-height:500px;"></canvas>
		<script>
			var chart = new Chart( document.getElementById( '<?php echo sanitize_key( $id ); ?>' ), {
				type: '<?php echo esc_js( $type ); ?>',
				data: <?php echo wp_json_encode( $data ); ?>,
				options: <?php echo wp_json_encode( $options ); ?>,
			} );
		</script>
		<?php
	}
}
