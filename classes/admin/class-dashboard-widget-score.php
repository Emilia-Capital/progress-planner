<?php
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

use Progress_Planner\Admin\Dashboard_Widget;
use Progress_Planner\Badges;
use Progress_Planner\Admin\Page;

/**
 * Class Dashboard_Widget
 */
class Dashboard_Widget_Score extends Dashboard_Widget {

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
		Page::enqueue_styles();
		foreach ( [ 'badge-streak', 'activity-scores' ] as $handle ) {
			$stylesheet = "/assets/css/page-widgets/{$handle}.css";
			\wp_enqueue_style(
				'prpl-widget-' . $handle,
				PROGRESS_PLANNER_URL . $stylesheet,
				[],
				(string) filemtime( PROGRESS_PLANNER_DIR . $stylesheet )
			);
		}
		\wp_enqueue_style(
			'prpl-dashboard-widget-' . $this->id,
			PROGRESS_PLANNER_URL . "/assets/css/dashboard-widgets/{$this->id}.css",
			[],
			(string) filemtime( PROGRESS_PLANNER_DIR . "/assets/css/dashboard-widgets/{$this->id}.css" )
		);

		include \PROGRESS_PLANNER_DIR . "/views/dashboard-widgets/{$this->id}.php"; // phpcs:ignore PEAR.Files.IncludingFile.UseInclude
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
			'content' => false,
			'streak'  => false,
		];

		if ( $cached[ $category ] ) {
			return $cached[ $category ];
		}

		$result = [];
		$badges = [
			'content' => [ 'wonderful-writer', 'bold-blogger', 'awesome-author' ],
			'streak'  => [ 'progress-padawan', 'maintenance-maniac', 'super-site-specialist' ],
		];

		// Get the badge to display.
		foreach ( $badges[ $category ] as $badge ) {
			$progress = Badges::get_badge_progress( $badge );
			if ( 100 > $progress['progress'] ) {
				break;
			}
		}
		$result['progress'] = $progress;
		$result['badge']    = Badges::get_badge( $badge );

		$result['color'] = 'var(--prpl-color-accent-red)';
		if ( $result['progress']['progress'] > 50 ) {
			$result['color'] = 'var(--prpl-color-accent-orange)';
		}
		if ( $result['progress']['progress'] > 75 ) {
			$result['color'] = 'var(--prpl-color-accent-green)';
		}

		$cached[ $category ] = $result;

		return $result;
	}
}
