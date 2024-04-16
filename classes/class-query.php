<?php
/**
 * Query class.
 *
 * This class is responsible for storing and retrieving data from the database.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder

/**
 * Query class.
 */
class Query {

	/**
	 * The name of the activities table.
	 *
	 * @var string
	 */
	const TABLE_NAME = 'progress_planner_activities';

	/**
	 * An instance of this class.
	 *
	 * @var \ProgressPlanner\Query
	 */
	private static $instance;

	/**
	 * Get the single instance of this class.
	 *
	 * @return \ProgressPlanner\Query
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
		$this->create_tables();
	}

	/**
	 * Create database tables.
	 *
	 * @return void
	 */
	public function create_tables() {
		$this->create_activities_table();
	}

	/**
	 * Create the activities table.
	 *
	 * @return void
	 */
	private function create_activities_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . static::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		/**
		 * Create a table for activities.
		 *
		 * Columns:
		 * - date: The date of the activity.
		 * - category: The category of the activity.
		 * - type: The type of the activity.
		 * - data_id: The ID of the data of the activity.
		 */
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $table_name (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				date DATE NOT NULL,
				category VARCHAR(255) NOT NULL,
				type VARCHAR(255) NOT NULL,
				data_id BIGINT(20) UNSIGNED NOT NULL,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;"
		);
	}

	/**
	 * Query the database for activities.
	 *
	 * @param array  $args        The arguments for the query.
	 * @param string $return_type The type of the return value. Can be "RAW" or "ACTIVITIES".
	 *
	 * @return array The activities.
	 */
	public function query_activities( $args, $return_type = 'ACTIVITIES' ) {
		global $wpdb;

		$defaults = [
			'id'         => null,
			'start_date' => null,
			'end_date'   => null,
			'category'   => null,
			'type'       => null,
			'data_id'    => null,
			'user_id'    => null,
		];

		$args = \wp_parse_args( $args, $defaults );

		$cache_key = 'progress-planner-activities-' . md5( \wp_json_encode( $args ) );
		$results   = \wp_cache_get( $cache_key );

		if ( false === $results ) {
			$where_args   = [];
			$prepare_args = [];
			if ( $args['id'] !== null ) {
				$where_args[]   = 'id = %d';
				$prepare_args[] = $args['id'];
			}
			if ( $args['start_date'] !== null ) {
				$where_args[]   = 'date >= %s';
				$prepare_args[] = ( $args['start_date'] instanceof \Datetime )
					? $args['start_date']->format( 'Y-m-d' )
					: $args['start_date'];
			}
			if ( $args['end_date'] !== null ) {
				$where_args[]   = 'date <= %s';
				$prepare_args[] = ( $args['end_date'] instanceof \Datetime )
					? $args['end_date']->format( 'Y-m-d' )
					: $args['end_date'];
			}
			if ( $args['category'] !== null ) {
				$where_args[]   = 'category = %s';
				$prepare_args[] = $args['category'];
			}
			if ( $args['type'] !== null ) {
				$where_args[]   = 'type = %s';
				$prepare_args[] = $args['type'];
			}
			if ( $args['data_id'] !== null ) {
				$where_args[]   = 'data_id = %s';
				$prepare_args[] = $args['data_id'];
			}

			if ( $args['user_id'] !== null ) {
				$where_args[]   = 'user_id = %s';
				$prepare_args[] = $args['user_id'];
			}

			$results = ( empty( $where_args ) )
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				? $wpdb->get_results(
					$wpdb->prepare( 'SELECT * FROM %i ORDER BY date', $wpdb->prefix . static::TABLE_NAME )
				)
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				: $wpdb->get_results(
					// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- This is a false positive.
					$wpdb->prepare(
						sprintf(
							'SELECT * FROM %%i WHERE %s',
							\implode( ' AND ', $where_args )
						),
						array_merge(
							[ $wpdb->prefix . static::TABLE_NAME ],
							$prepare_args
						)
					)
				);

			\wp_cache_set( $cache_key, $results );
		}

		if ( ! $results ) {
			return [];
		}

		// Remove duplicates. This could be removed in a future release.
		$results_unique = [];
		foreach ( $results as $key => $result ) {
			$result_key = $result->category . $result->type . $result->data_id . $result->date;
			// Cleanup any duplicates that may exist.
			if ( isset( $results_unique[ $result_key ] ) ) {
				$this->delete_activity_by_id( $result->id );
				continue;
			}
			$results_unique[ $result->category . $result->type . $result->data_id . $result->date ] = $result;
		}
		$results = array_values( $results_unique );

		return 'RAW' === $return_type
			? $results
			: $this->get_activities_from_results( $results );
	}

	/**
	 * Insert multiple activities into the database.
	 *
	 * @param \ProgressPlanner\Activity[] $activities The activities to insert.
	 *
	 * @return int[]|false The IDs of the inserted activities, or false on failure.
	 */
	public function insert_activities( $activities ) {
		$ids = [];
		foreach ( $activities as $activity ) {
			$id = $this->insert_activity( $activity );
			if ( false === $id ) {
				continue;
			}
			$ids[] = $id;
		}
		if ( empty( $ids ) ) {
			return false;
		}
		return $ids;
	}


	/**
	 * Insert an activity into the database.
	 *
	 * @param \ProgressPlanner\Activity $activity The activity to insert.
	 *
	 * @return int|false The ID of the inserted activity, or false on failure.
	 */
	public function insert_activity( $activity ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$wpdb->prefix . static::TABLE_NAME,
			[
				'date'     => $activity->date->format( 'Y-m-d H:i:s' ),
				'category' => $activity->category,
				'type'     => $activity->type,
				'data_id'  => $activity->data_id,
				'user_id'  => $activity->user_id,
			],
			[
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
			]
		);

		if ( false === $result ) {
			return false;
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Get activities objects from results.
	 *
	 * @param array $results The results.
	 *
	 * @return \ProgressPlanner\Activity[] The activities.
	 */
	private function get_activities_from_results( $results ) {
		$activities = [];
		foreach ( $results as $result ) {
			$class_name         = $this->get_activity_class_name( $result->category );
			$activity           = new $class_name();
			$activity->date     = new \DateTime( $result->date );
			$activity->category = $result->category;
			$activity->type     = $result->type;
			$activity->data_id  = (int) $result->data_id;
			$activity->id       = (int) $result->id;
			$activity->user_id  = (int) $result->user_id;
			$activities[]       = $activity;
		}

		return $activities;
	}

	/**
	 * Update an activity in the database.
	 *
	 * @param int                       $id       The ID of the activity to update.
	 * @param \ProgressPlanner\Activity $activity The activity to update.
	 *
	 * @return void
	 */
	public function update_activity( $id, $activity ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->update(
			$wpdb->prefix . static::TABLE_NAME,
			[
				'date'     => $activity->date->format( 'Y-m-d H:i:s' ),
				'category' => $activity->category,
				'type'     => $activity->type,
				'data_id'  => $activity->data_id,
				'user_id'  => $activity->user_id,
			],
			[ 'id' => $id ],
			[
				'%s',
				'%s',
				'%s',
				'%d',
				'%d',
			],
			[ '%d' ]
		);
	}

	/**
	 * Delete activities from the database.
	 *
	 * @param \ProgressPlanner\Activity[] $activities The activity to delete.
	 *
	 * @return void
	 */
	public function delete_activities( $activities ) {
		foreach ( $activities as $activity ) {
			$this->delete_activity( $activity );
		}
	}

	/**
	 * Delete an activity from the database.
	 *
	 * @param \ProgressPlanner\Activity $activity The activity to delete.
	 *
	 * @return void
	 */
	public function delete_activity( $activity ) {
		$this->delete_activity_by_id( $activity->id );
	}

	/**
	 * Delete activitiy by ID.
	 *
	 * @param int $id The ID of the activity to delete.
	 *
	 * @return void
	 */
	public function delete_activity_by_id( $id ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->delete(
			$wpdb->prefix . static::TABLE_NAME,
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

	/**
	 * Detele all activities in a category.
	 *
	 * @param string $category The category of the activities to delete.
	 *
	 * @return void
	 */
	public function delete_category_activities( $category ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->delete(
			$wpdb->prefix . static::TABLE_NAME,
			[ 'category' => $category ],
			[ '%s' ]
		);
	}

	/**
	 * Get oldest activity.
	 *
	 * @return \ProgressPlanner\Activity|null Returns null if there are no activities.
	 */
	public function get_oldest_activity() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$result = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM %i ORDER BY date ASC LIMIT 1',
				$wpdb->prefix . static::TABLE_NAME
			)
		);

		if ( ! $result ) {
			return null;
		}

		$class_name         = $this->get_activity_class_name( $result->category );
		$activity           = new $class_name();
		$activity->date     = new \DateTime( $result->date );
		$activity->category = $result->category;
		$activity->type     = $result->type;
		$activity->data_id  = (int) $result->data_id;
		$activity->id       = (int) $result->id;
		$activity->user_id  = (int) $result->user_id;

		return $activity;
	}

	/**
	 * Get activity class name from its category.
	 *
	 * @param string $category The category of the activity.
	 *
	 * @return string The class name of the Activity.
	 */
	protected function get_activity_class_name( $category ) {
		if ( class_exists( '\ProgressPlanner\Activities\\' . ucfirst( $category ) ) ) {
			return '\ProgressPlanner\Activities\\' . ucfirst( $category );
		}
		return '\ProgressPlanner\Activity';
	}
}
// phpcs:enable
