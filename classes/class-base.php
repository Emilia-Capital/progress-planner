<?php
/**
 * Progress Planner main plugin class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

use Progress_Planner\Query;
use Progress_Planner\Admin\Page as Admin_page;
use Progress_Planner\Admin\Dashboard_Widget as Admin_Dashboard_Widget;
use Progress_Planner\Actions\Content as Actions_Content;
use Progress_Planner\Actions\Content_Scan as Actions_Content_Scan;
use Progress_Planner\Actions\Maintenance as Actions_Maintenance;
use Progress_Planner\Settings;
use Progress_Planner\Badges\Badge\Wonderful_Writer as Badge_Wonderful_Writer;
use Progress_Planner\Badges\Badge\Bold_Blogger as Badge_Bold_Blogger;
use Progress_Planner\Badges\Badge\Awesome_Author as Badge_Awesome_Author;
use Progress_Planner\Badges\Badge\Progress_Padawan as Badge_Progress_Padawan;
use Progress_Planner\Badges\Badge\Maintenance_Maniac as Badge_Maintenance_Maniac;
use Progress_Planner\Badges\Badge\Super_Site_Specialist as Badge_Super_Site_Specialist;
use Progress_Planner\Rest_API;
use Progress_Planner\Todo;

/**
 * Main plugin class.
 */
class Base {

	/**
	 * An instance of this class.
	 *
	 * @var \Progress_Planner\Base
	 */
	private static $instance;

	/**
	 * An array of configuration values for points awarded by action-type.
	 *
	 * @var array
	 */
	public static $points_config = [
		'content'      => [
			'publish'          => 50,
			'update'           => 10,
			'delete'           => 5,
			'word-multipliers' => [
				100  => 1.1,
				350  => 1.25,
				1000 => 0.8,
			],
		],
		'maintenance'  => 10,
		'todo'         => [
			'add'     => 1,
			'delete'  => 1,
			'update'  => 3, // Handles marking as done, and updating the content.
			'default' => 1,
		],
		'score-target' => 200,
	];

	/**
	 * Get the single instance of this class.
	 *
	 * @return \Progress_Planner\Base
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		// Basic classes.
		new Admin_Page();
		new Admin_Dashboard_Widget();
		new Actions_Content();
		new Actions_Maintenance();
		new Actions_Content_Scan();

		// Content badges.
		new Badge_Wonderful_Writer();
		new Badge_Bold_Blogger();
		new Badge_Awesome_Author();

		// Maintenance badges.
		new Badge_Progress_Padawan();
		new Badge_Maintenance_Maniac();
		new Badge_Super_Site_Specialist();

		// REST API.
		new Rest_API();

		// Onboarding.
		new Onboard();

		// To-do.
		new Todo();
	}

	/**
	 * Get the query object.
	 *
	 * @return \Progress_Planner\Query
	 */
	public function get_query() {
		return Query::get_instance();
	}

	/**
	 * Get the activation date.
	 *
	 * @return \DateTime
	 */
	public static function get_activation_date() {
		$activation_date = Settings::get( 'activation_date' );
		if ( ! $activation_date ) {
			$activation_date = new \DateTime();
			Settings::set( 'activation_date', $activation_date->format( 'Y-m-d' ) );
			return $activation_date;
		}
		return \DateTime::createFromFormat( 'Y-m-d', $activation_date );
	}

	/**
	 * Get the number of weeks.
	 *
	 * @param int    $number_weeks           Number of weeks.
	 * @param string $formatted_number_weeks Formatted number of weeks.
	 *
	 * @return string  String with the number of weeks + "week" or "weeks".
	 */
	public static function weeks( $number_weeks, $formatted_number_weeks = null ) {
		if ( is_null( $formatted_number_weeks ) ) {
			$formatted_number_weeks = \number_format_i18n( $number_weeks );
		}
		return \esc_html(
			sprintf(
				/* translators: %d: Number of weeks. */
				\_n( '%d week', '%d weeks', $number_weeks, 'progress-planner' ),
				$formatted_number_weeks
			)
		);
	}
}
