<?php
/**
 * Query class.
 *
 * This class is responsible for storing and retrieving data from the database.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Activities;

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
		 * - data: The data of the activity.
		 */
		// phpcs:disable WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL
		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $table_name (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				date DATE NOT NULL,
				category VARCHAR(255) NOT NULL,
				type VARCHAR(255) NOT NULL,
				data_id BIGINT(20) UNSIGNED NOT NULL,
				data LONGTEXT NOT NULL,
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
	 * @return \ProgressPlanner\Activities\Activity[] The activities.
	 */
	public function query_activities( $args, $return_type = 'ACTIVITIES' ) {
		global $wpdb;

		$defaults = [
			'start_date' => null,
			'end_date'   => null,
			'category'   => null,
			'type'       => null,
			'data_id'    => null,
		];

		$args = \wp_parse_args( $args, $defaults );

		$where_args   = [];
		$prepare_args = [];
		if ( $args['start_date'] !== null ) {
			$where_args[]   = 'date >= %s';
			$prepare_args[] = $args['start_date']->format( 'Y-m-d H:i:s' );
		}
		if ( $args['end_date'] !== null ) {
			$where_args[]   = 'date <= %s';
			$prepare_args[] = $args['end_date']->format( 'Y-m-d H:i:s' );
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

		$results = ( empty( $where_args ) )
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			? $wpdb->get_results(
				$wpdb->prepare( 'SELECT * FROM %i', $wpdb->prefix . static::TABLE_NAME )
			)
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
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

		if ( isset( $args['data'] ) && ! empty( $args['data'] ) ) {
			foreach ( $results as $key => $activity ) {
				$data = \json_decode( $activity->data, true );
				foreach ( $args['data'] as $data_key => $data_value ) {
					if ( ! isset( $data[ $data_key ] ) || $data[ $data_key ] !== $data_value ) {
						unset( $results[ $key ] );
					}
				}
			}
			$results = \array_values( $results );
		}
		return 'RAW' === $return_type
			? $results
			: $this->get_activities_from_results( $results );
	}

	/**
	 * Insert multiple activities into the database.
	 *
	 * @param \ProgressPlanner\Activities\Activity[] $activities The activities to insert.
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
	 * @param \ProgressPlanner\Activities\Activity $activity The activity to insert.
	 *
	 * @return int|false The ID of the inserted activity, or false on failure.
	 */
	public function insert_activity( $activity ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$wpdb->prefix . static::TABLE_NAME,
			[
				'date'     => $activity->get_date()->format( 'Y-m-d H:i:s' ),
				'category' => $activity->get_category(),
				'type'     => $activity->get_type(),
				'data_id'  => $activity->get_data_id(),
				'data'     => \wp_json_encode( $activity->get_data() ),
			],
			[
				'%s',
				'%s',
				'%s',
				'%s',
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
	 * @return \ProgressPlanner\Activities\Activity[] The activities.
	 */
	private function get_activities_from_results( $results ) {
		$activities = [];
		foreach ( $results as $result ) {
			$activity = new Activity();
			$activity->set_date( new \DateTime( $result->date ) );
			$activity->set_category( $result->category );
			$activity->set_type( $result->type );
			$activity->set_data_id( (int) $result->data_id );
			$activity->set_data( \json_decode( $result->data, true ) );
			$activity->set_id( (int) $result->id );
			$activities[] = $activity;
		}

		return $activities;
	}

	/**
	 * Update an activity in the database.
	 *
	 * @param int                                  $id       The ID of the activity to update.
	 * @param \ProgressPlanner\Activities\Activity $activity The activity to update.
	 *
	 * @return void
	 */
	public function update_activity( $id, $activity ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->update(
			$wpdb->prefix . static::TABLE_NAME,
			[
				'date'     => $activity->get_date()->format( 'Y-m-d H:i:s' ),
				'category' => $activity->get_category(),
				'type'     => $activity->get_type(),
				'data_id'  => $activity->get_data_id(),
				'data'     => \wp_json_encode( $activity->get_data() ),
			],
			[ 'id' => $id ],
			[
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			],
			[ '%d' ]
		);
	}

	/**
	 * Delete activities from the database.
	 *
	 * @param \ProgressPlanner\Activities\Activity[] $activities The activity to delete.
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
	 * @param \ProgressPlanner\Activities\Activity $activity The activity to delete.
	 *
	 * @return void
	 */
	public function delete_activity( $activity ) {
		$this->delete_activity_by_id( $activity->get_id() );
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
	 * @return \ProgressPlanner\Activities\Activity
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

		$activity = new Activity();
		$activity->set_date( new \DateTime( $result->date ) );
		$activity->set_category( $result->category );
		$activity->set_type( $result->type );
		$activity->set_data_id( (int) $result->data_id );
		$activity->set_data( \json_decode( $result->data, true ) );
		$activity->set_id( (int) $result->id );

		return $activity;
	}
}
// phpcs:enable
