<?php
/**
 * Add a widget to the WordPress dashboard.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Admin;

use Progress_Planner\Admin\Dashboard_Widget;
use Progress_Planner\Admin\Page;
use Progress_Planner\Badges;

/**
 * Class Dashboard_Widget
 */
class Dashboard_Widget_Score extends Dashboard_Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'progress_planner_dashboard_widget_score';

	/**
	 * Get the title of the widget.
	 *
	 * @return string
	 */
	protected function get_title() {
		return \esc_html__( 'Progress Planner site score', 'progress-planner' );
	}

	/**
	 * Render the dashboard widget.
	 *
	 * @return void
	 */
	public function render_widget() {
		Page::enqueue_styles();
		?>
		<div class="prpl-dashboard-widget">
			<div class="prpl-score-gauge">
				<?php \Progress_Planner\Widgets\Website_Activity_Score::print_score_gauge(); ?>
			</div>
			<div class="grid-separator"></div>
			<div class="prpl-badges">
				<h3><?php \esc_html_e( 'Next badges', 'progress-planner' ); ?></h3>
				<?php $this->the_badge( 'content' ); ?>
				<?php $this->the_badge( 'streak' ); ?>
			</div>
		</div>

		<div class="prpl-dashboard-widget-footer">
			<img src="<?php echo esc_attr( PROGRESS_PLANNER_URL . '/assets/images/icon_progress_planner.svg' ); ?>" style="width:1.5em;" alt="" />
			<a href="<?php echo \esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) ); ?>">
				<?php \esc_html_e( 'See more details', 'progress-planner' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render a badge.
	 *
	 * @param string $category The category of badges. Can be `content` or `streak`.
	 *
	 * @return void
	 */
	protected function the_badge( $category = 'content' ) {
		$details = $this->get_badge_details( $category );
		?>
		<div class="prpl-badges-columns-wrapper">
			<div class="prpl-badge-wrapper">
				<span
					class="prpl-badge"
					data-value="<?php echo \esc_attr( $details['progress']['progress'] ); ?>"
				>
					<div
						class="prpl-badge-gauge"
						style="
							--value:<?php echo (float) ( $details['progress']['progress'] / 100 ); ?>;
							--max: 360deg;
							--start: 180deg;
						">
						<?php require $details['badge']['icons-svg']['complete']['path']; ?>
					</div>
				</span>
				<span class="progress-percent"><?php echo \esc_attr( $details['progress']['progress'] ); ?>%</span>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the badge.
	 *
	 * @param string $category The category of the badge.
	 *
	 * @return array
	 */
	public function get_badge_details( $category = 'content' ) {
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
		return $result;
	}
}
