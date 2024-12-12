<?php
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

/**
 * Class Dashboard_Widget_Score
 */
class Dashboard_Widget_Score extends \Progress_Planner\Admin\Dashboard_Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'score';

	/**
	 * Get the title of the widget.
	 *
	 * @return string
	 */
	protected function get_title() {
		return \esc_html__( 'Progress Planner', 'progress-planner' );
	}

	/**
	 * Render the dashboard widget.
	 *
	 * @return void
	 */
	public function render_widget() {
		// Enqueue stylesheets.
		\progress_planner()->get_admin__scripts()->register_scripts();
		\progress_planner()->get_admin__page()->enqueue_styles();
		\wp_enqueue_script( 'progress-planner-web-components-prpl-gauge' );

		$suggested_tasks_widget = \progress_planner()->get_admin__page()->get_widget( 'suggested-tasks' );
		if ( $suggested_tasks_widget ) {
			$suggested_tasks_widget->enqueue_styles();
			$suggested_tasks_widget->enqueue_scripts();
		}

		\wp_enqueue_style(
			'prpl-dashboard-widget-' . $this->id,
			PROGRESS_PLANNER_URL . "/assets/css/dashboard-widgets/{$this->id}.css",
			[],
			\progress_planner()->get_file_version( PROGRESS_PLANNER_DIR . "/assets/css/dashboard-widgets/{$this->id}.css" )
		);

		\progress_planner()->the_view( "dashboard-widgets/{$this->id}.php" );
	}

	/**
	 * Get the badge.
	 *
	 * @param string $category The category of the badge.
	 *
	 * @return array
	 */
	public function get_badge_details( $category = 'content' ) {
		static $cached = [
			'content'     => false,
			'maintenance' => false,
		];

		if ( $cached[ $category ] ) {
			return $cached[ $category ];
		}

		$result = [];

		// Get the badge to display.
		foreach ( \progress_planner()->get_badges()->get_badges( $category ) as $badge ) {
			$progress = $badge->get_progress();
			if ( 100 > $progress['progress'] ) {
				break;
			}
		}

		if ( ! isset( $badge ) || ! isset( $progress ) ) {
			return $result;
		}

		$result['progress'] = $progress;
		$result['badge']    = $badge;

		$result['color'] = 'var(--prpl-color-accent-red)';
		if ( $result['progress']['progress'] > 50 ) {
			$result['color'] = 'var(--prpl-color-accent-orange)';
		}
		if ( $result['progress']['progress'] > 75 ) {
			$result['color'] = 'var(--prpl-color-accent-green)';
		}
		$result['background'] = $badge->get_background();

		$cached[ $category ] = $result;

		return $result;
	}
}
