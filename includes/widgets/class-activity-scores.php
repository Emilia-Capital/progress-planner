<?php
/**
 * Activity Scores Widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Widgets\Widget;
use ProgressPlanner\Chart;

/**
 * Activity Scores Widget.
 */
final class Activity_Scores extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'activity-scores';

	/**
	 * Render the widget content.
	 */
	public function the_content() {
		?>
		<h2 class="prpl-widget-title">
			<?php \esc_html_e( 'Activity scores', 'progress-planner' ); ?>
		</h2>
		<div class="prpl-graph-wrapper">
			<?php ( new Chart() )->the_chart( $this->get_chart_args() ); ?>
		</div>
		<?php
	}

	/**
	 * The color callback.
	 *
	 * @param int $number The number to calculate the color for.
	 *
	 * @return string The color.
	 */
	public function get_color( $number ) {
		if ( $number > 90 ) {
			return '#14b8a6';
		}
		if ( $number > 30 ) {
			return '#faa310';
		}
		return '#f43f5e';
	}

	/**
	 * Get the chart args.
	 *
	 * @return array The chart args.
	 */
	public function get_chart_args() {
		$this_object = $this;
		return [
			'query_params'   => [],
			'dates_params'   => [
				'start'     => \DateTime::createFromFormat( 'Y-m-d', \gmdate( 'Y-m-01' ) )->modify( $this->get_range() ),
				'end'       => new \DateTime(),
				'frequency' => $this->get_frequency(),
				'format'    => 'M',
			],
			'chart_params'   => [
				'type'    => 'bar',
				'options' => [
					'responsive'          => true,
					'maintainAspectRatio' => false,
					'pointStyle'          => false,
					'plugins'             => [
						'legend' => [
							'display' => false,
						],
					],
					'scales'              => [
						'yAxis' => [
							'min' => 0,
							'max' => 100,
						],
					],
				],
			],
			'count_callback' => function ( $activities, $date ) use ( $this_object ) {
				$score = 0;
				foreach ( $activities as $activity ) {
					$score += $activity->get_points( $date );
				}
				$target = \progress_planner()->get_dev_config( 'activity-score-target' );
				return $score * 100 / $target;
			},
			'compound'       => false,
			'normalized'     => true,
			'colors'         => [
				'background' => [ $this_object, 'get_color' ],
				'border'     => [ $this_object, 'get_color' ],
			],
			'max'            => 100,
		];
	}
}
