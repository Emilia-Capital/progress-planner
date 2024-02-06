<?php
/**
 * Handle the settings.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

/**
 * Settings class.
 */
class Settings {

	/**
	 * The option name.
	 *
	 * @var string
	 */
	private $option_name = 'progress_planner';

	/**
	 * Get the option value.
	 *
	 * @return array
	 */
	public function get_value() {
		// Get the saved value.
		$saved_value = \get_option( $this->option_name, [] );

		// Get the value for current week & month.
		$current_value = $this->get_current_value();

		// Merge the saved value with the default value.
		return \array_replace_recursive( $current_value, $saved_value );
	}

	/**
	 * Get the value for the current week & month.
	 *
	 * @return array
	 */
	private function get_current_value() {
		// Get the values for current week and month.
		$curr_y     = \gmdate( 'Y' );
		$curr_m     = \gmdate( 'n' );
		$curr_w     = \gmdate( 'W' );
		$curr_value = [
			'stats' => [
				$curr_y => [
					'weeks'  => [
						$curr_w => [
							'posts' => [],
							'words' => [],
						],
					],
					'months' => [
						$curr_m => [
							'posts' => [],
							'words' => [],
						],
					],
				],
			],
		];
		foreach ( \array_keys( \get_post_types( [ 'public' => true ] ) ) as $post_type ) {
			$week_stats = Progress_Planner::get_instance()
				->get_stats()
				->get_stat( 'posts' )
				->set_post_type( $post_type )
				->get_data( 'this week' );

			$month_stats = Progress_Planner::get_instance()
				->get_stats()
				->get_stat( 'posts' )
				->set_post_type( $post_type )
				->get_data( gmdate( 'F Y' ) );

			$curr_value['stats'][ $curr_y ]['weeks'][ $curr_w ]['posts'][ $post_type ]  = $week_stats['count'];
			$curr_value['stats'][ $curr_y ]['weeks'][ $curr_w ]['words'][ $post_type ]  = $week_stats['word_count'];
			$curr_value['stats'][ $curr_y ]['months'][ $curr_m ]['posts'][ $post_type ] = $month_stats['count'];
			$curr_value['stats'][ $curr_y ]['months'][ $curr_m ]['words'][ $post_type ] = $month_stats['word_count'];
		}

		return $curr_value;
	}

	/**
	 * Update value for previous week.
	 */
}
