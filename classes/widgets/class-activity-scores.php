<?php
/**
 * Activity Scores Widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Base;
use Progress_Planner\Widgets\Widget;

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
	 * The color callback.
	 *
	 * @param int       $number The number to calculate the color for.
	 * @param \DateTime $date   The date.
	 *
	 * @return string The color.
	 */
	public function get_color( $number, $date ) {
		// If monthly and the latest month, return gray (in progress).
		if (
			'monthly' === $this->get_frequency() &&
			\gmdate( 'Y-m-01' ) === $date->format( 'Y-m-01' )
		) {
			return '#d1d5db';
		}

		// If weekly and the current week, return gray (in progress).
		if (
			'weekly' === $this->get_frequency() &&
			\gmdate( 'Y-W' ) === $date->format( 'Y-W' )
		) {
			return '#d1d5db';
		}

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
			'count_callback' => function ( $activities, $date ) {
				$score = 0;
				foreach ( $activities as $activity ) {
					$score += $activity->get_points( $date );
				}
				$target = Base::$points_config['score-target'];
				return $score * 100 / $target;
			},
			'compound'       => false,
			'normalized'     => true,
			'colors'         => [
				'background' => [ $this, 'get_color' ],
				'border'     => [ $this, 'get_color' ],
			],
			'max'            => 100,
		];
	}
}
