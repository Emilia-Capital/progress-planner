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
	 * @param string[] ...$args Get the value for a specific key in the array.
	 *                          This will go over the array recursively, returning the value for the last key.
	 *                          Example: If the value is ['a' => ['b' => 'c']], get_value('a', 'b') will return 'c'.
	 *                          If the key does not exist, it will return null.
	 *                          If no keys are provided, it will return the entire array.
	 *
	 * @return array
	 */
	public function get_value( ...$args ) {
		// Get the saved value.
		$saved_value = \get_option( $this->option_name, [] );

		// Get the value for current week & month.
		$current_value = $this->get_current_value();

		// Merge the saved value with the default value.
		$value = \array_replace_recursive( $current_value, $saved_value );

		return empty( $args )
			? $value
			: \_wp_array_get( $value, $args );
	}

	/**
	 * Update the option value.
	 *
	 * @param string[] $args  The keys to update.
	 *                        This will go over the array recursively, updating the value for the last key.
	 *                        See get_value for more info.
	 * @param mixed    $value The new value.
	 *
	 * @return bool Returns the result of the update_option function.
	 */
	public function set_value( $args, $value ) {
		// Get the saved value.
		$saved_value = \get_option( $this->option_name, [] );

		// Update item in the array.
		\_wp_array_set( $saved_value, $args, $value );

		// Update the option value.
		return \update_option( $this->option_name, $saved_value );
	}

	/**
	 * Get the value for the current week & month.
	 *
	 * @return array
	 */
	private function get_current_value() {
		// Get the values for current week and month.
		$curr_y     = (int) \gmdate( 'Y' );
		$curr_m     = (int) \gmdate( 'n' );
		$curr_w     = (int) \gmdate( 'W' );
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

		$stats = Progress_Planner::get_instance()->get_stats()->get_stat( 'posts' );
		foreach ( \array_keys( \get_post_types( [ 'public' => true ] ) ) as $post_type ) {
			// Set the post-type.
			$stats->set_post_type( $post_type );

			// Get weekly stats.
			$week_stats = $stats->set_date_query(
				[
					[
						'after'     => '-1 week',
						'inclusive' => true,
					],
				]
			)->get_data();

			// Get monthly stats.
			$month_stats = $stats->set_date_query(
				[
					[
						'after'     => gmdate( 'F Y' ),
						'inclusive' => true,
					],
				]
			)->get_data();

			$curr_value['stats'][ $curr_y ]['weeks'][ $curr_w ]['posts'][ $post_type ]  = $week_stats['count'];
			$curr_value['stats'][ $curr_y ]['weeks'][ $curr_w ]['words'][ $post_type ]  = $week_stats['word_count'];
			$curr_value['stats'][ $curr_y ]['months'][ $curr_m ]['posts'][ $post_type ] = $month_stats['count'];
			$curr_value['stats'][ $curr_y ]['months'][ $curr_m ]['words'][ $post_type ] = $month_stats['word_count'];
		}

		return $curr_value;
	}

	/**
	 * Update value for a previous, unsaved week.
	 *
	 * @param string $interval_type  The interval type. Can be "week" or "month".
	 * @param int    $interval_value The number of weeks or months back to update the value for.
	 *
	 * @return void
	 */
	public function update_value_previous_unsaved_interval( $interval_type = 'weeks', $interval_value = 0 ) {

		// Get the year & week numbers for the defined week/month.
		$year             = (int) \gmdate( 'Y', strtotime( "-$interval_value $interval_type" ) );
		$interval_type_nr = (int) \gmdate(
			'weeks' === $interval_type ? 'W' : 'n',
			strtotime( "-$interval_value $interval_type" )
		);

		$stats = Progress_Planner::get_instance()->get_stats()->get_stat( 'posts' );
		foreach ( \array_keys( \get_post_types( [ 'public' => true ] ) ) as $post_type ) {
			$interval_stats = $stats->set_post_type( $post_type )->set_date_query(
				[
					[
						'after'     => '-' . ( $interval_value + 1 ) . ' ' . $interval_type,
						'inclusive' => true,
					],
					[
						'before'    => '-' . $interval_value . ' ' . $interval_type,
						'inclusive' => false,
					],
				]
			)->get_data();

			$this->set_value(
				[ 'stats', $year, $interval_type, $interval_type_nr, 'posts', $post_type ],
				$interval_stats['count']
			);
			$this->set_value(
				[ 'stats', $year, $interval_type, $interval_type_nr, 'words', $post_type ],
				$interval_stats['word_count']
			);
		}
	}
}
