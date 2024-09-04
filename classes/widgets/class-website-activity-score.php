<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Widgets\Widget;

/**
 * Website activity score widget.
 */
final class Website_Activity_Score extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'website-activity-score';

	/**
	 * Render the widget content.
	 */
	public function the_content() {
		?>
		<h2 class="prpl-widget-title">
			<?php \esc_html_e( 'Your website activity score', 'progress-planner' ); ?>
		</h2>
		<div class="two-col">
			<?php self::print_score_gauge(); ?>
			<div>
				<?php \esc_html_e( 'Your activity this week:', 'progress-planner' ); ?>
				<?php $this->print_weekly_activities_checklist(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the score gauge.
	 *
	 * @param string $background_color The background color.
	 * @param string $description      The description.
	 *
	 * @return void
	 */
	public static function print_score_gauge( $background_color = 'var(--prpl-background-orange)', $description = '' ) {
		$score = self::get_score();
		?>
		<div class="prpl-activities-gauge-container-container">
			<div class="prpl-activities-gauge-container">
				<div
					class="prpl-activities-gauge"
					style="
						--value:<?php echo (float) ( $score / 100 ); ?>;
						--background: <?php echo \esc_attr( $background_color ); ?>;
						--max: 180deg;
						--start: 270deg;
						--color:<?php echo \esc_attr( self::get_gauge_color( $score ) ); ?>"
				>
					<span class="prpl-gauge-0">
						0
					</span>
					<span class="prpl-gauge-number">
						<?php echo (int) $score; ?>
					</span>
					<span class="prpl-gauge-100">
						100
					</span>
				</div>
			</div>
			<?php
			if ( empty( $description ) ) {
				\esc_html_e( 'Your Website Activity Score is based on the amount of website maintenance work you\'ve done over the past 30 days.', 'progress-planner' );
			} else {
				echo \wp_kses_post( $description );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Print the list of weekly activities checklist items.
	 *
	 * @return void
	 */
	public static function print_weekly_activities_checklist() {
		?>
		<ul>
			<?php foreach ( self::get_checklist_results() as $label => $value ) : ?>
				<li class="prpl-checklist-item">
					<?php
					echo $value
						? '<span class="prpl-icon prpl-green"><svg role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path fill="#16a34a" d="M32 63.9C14.41 63.9.1 49.59.1 32S14.41.1 32 .1 63.9 14.41 63.9 32 49.59 63.9 32 63.9Zm0-57.82C17.71 6.08 6.08 17.71 6.08 32S17.71 57.92 32 57.92 57.92 46.29 57.92 32 46.29 6.08 32 6.08Zm-2.41 38.55c-.79 0-1.55-.31-2.11-.88l-7.23-7.23c-1.17-1.17-1.17-3.06 0-4.23 1.17-1.17 3.06-1.17 4.23 0l4.73 4.73 9.99-13.99c.96-1.34 2.83-1.66 4.17-.7 1.34.96 1.66 2.83.7 4.17L32.02 43.36c-.51.72-1.31 1.17-2.19 1.24-.08 0-.17.01-.25.01Z"/></svg></span>'
						: '<span class="prpl-icon prpl-red"><svg role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path fill="#e73136" d="M32 63.9C14.41 63.9.1 49.59.1 32S14.41.1 32 .1 63.9 14.41 63.9 32 49.59 63.9 32 63.9Zm0-57.82C17.71 6.08 6.08 17.71 6.08 32S17.71 57.92 32 57.92 57.92 46.29 57.92 32 46.29 6.08 32 6.08Zm14.45 28.91H32c-1.65 0-2.99-1.34-2.99-2.99V12.73c0-1.65 1.34-2.99 2.99-2.99s2.99 1.34 2.99 2.99v16.28h11.46c1.65 0 2.99 1.34 2.99 2.99s-1.34 2.99-2.99 2.99Z"/></svg></span>';
					?>
					<?php echo wp_kses_post( $label ); ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}

	/**
	 * Get the score.
	 *
	 * @return int The score.
	 */
	public static function get_score() {
		$activities = \progress_planner()->get_query()->query_activities(
			[
				// Use 31 days to take into account
				// the activities score decay from previous activities.
				'start_date' => new \DateTime( '-31 days' ),
			]
		);

		$score        = 0;
		$current_date = new \DateTime();
		foreach ( $activities as $activity ) {
			$score += $activity->get_points( $current_date );
		}
		$score = min( 100, max( 0, $score ) );

		// Get the number of pending updates.
		$pending_updates = \wp_get_update_data()['counts']['total'];

		// Reduce points for pending updates.
		$score -= min( min( $score / 2, 25 ), $pending_updates * 5 );
		return (int) floor( $score );
	}

	/**
	 * Get the checklist results.
	 *
	 * @return array<string, bool> The checklist results.
	 */
	public static function get_checklist_results() {
		$items   = self::get_checklist();
		$results = [];
		foreach ( $items as $item ) {
			$results[ $item['label'] ] = $item['callback']();
		}
		return $results;
	}

	/**
	 * Get the checklist items.
	 *
	 * @return array The checklist items.
	 */
	public static function get_checklist() {
		return [
			[
				'label'    => \esc_html__( 'published content', 'progress-planner' ),
				'callback' => function () {
					$events = \progress_planner()->get_query()->query_activities(
						[
							'start_date' => new \DateTime( '-7 days' ),
							'category'   => 'content',
							'type'       => 'publish',
						]
					);
					return count( $events ) > 0;
				},
			],
			[
				'label'    => \esc_html__( 'updated content', 'progress-planner' ),
				'callback' => function () {
					$events = \progress_planner()->get_query()->query_activities(
						[
							'start_date' => new \DateTime( '-7 days' ),
							'category'   => 'content',
							'type'       => 'update',
						]
					);
					return count( $events ) > 0;
				},
			],
			[
				'label'    => 0 === \wp_get_update_data()['counts']['total']
					? \esc_html__( 'performed all updates', 'progress-planner' )
					: '<a href="' . \esc_url( \admin_url( 'update-core.php' ) ) . '">' . \esc_html__( 'Perform all updates', 'progress-planner' ) . '</a>',
				'callback' => function () {
					return ! \wp_get_update_data()['counts']['total'];
				},
			],
		];
	}

	/**
	 * Get the gauge color.
	 *
	 * @param int $score The score.
	 *
	 * @return string The color.
	 */
	protected static function get_gauge_color( $score ) {
		if ( $score >= 75 ) {
			return 'var(--prpl-color-accent-green)';
		}
		if ( $score >= 50 ) {
			return 'var(--prpl-color-accent-orange)';
		}
		return 'var(--prpl-color-accent-red)';
	}
}
