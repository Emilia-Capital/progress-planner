<?php
/**
 * Generate charts for the admin page.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Admin;

/**
 * Render a chart.
 */
class Chart {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_hooks();
	}

	/**
	 * Register the hooks.
	 */
	private function register_hooks() {
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueue the scripts and styles.
	 */
	public function enqueue_scripts() {
		\wp_enqueue_script( 'chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], '4.4.1', true );
	}

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
	public function render_chart( $id, $type, $data, $options ) {
		$id = 'progress-planner-chart-' . $id;
		?>
		<canvas id="<?php echo sanitize_key( $id ); ?>" width="400" height="400"></canvas>
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
