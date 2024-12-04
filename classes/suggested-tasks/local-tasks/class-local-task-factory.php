<?php
/**
 * Local task factory.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Suggested_Tasks\Local_Tasks;

/**
 * Local task factory.
 */
class Local_Task_Factory {
	/**
	 * Create a task.
	 *
	 * @param string $task_id The task ID.
	 *
	 * @return Task_Local
	 */
	public static function create( string $task_id ): Task_Local {
		if ( str_contains( $task_id, '|' ) ) {
			// Parse detailed format.
			$parts = \explode( '|', $task_id );
			$data  = [];
			foreach ( $parts as $part ) {
				$part = \explode( '/', $part );
				if ( 2 !== \count( $part ) ) {
					continue;
				}
				$data[ $part[0] ] = ( \is_numeric( $part[1] ) )
					? (int) $part[1]
					: $part[1];
			}
			\ksort( $data );

			// Convert (int) 1 and (int) 0 to (bool) true and (bool) false.
			if ( isset( $data['long'] ) ) {
				$data['long'] = (bool) $data['long'];
			}

			$data['task_id'] = $task_id;
		} else {
			$data = [];

			// Parse simple format, e.g. 'update-core-202449'.
			$last_pos = strrpos( $task_id, '-' );
			if ( false !== $last_pos ) {
				$data['type']      = substr( $task_id, 0, $last_pos );
				$data['year_week'] = substr( $task_id, $last_pos + 1 );
			}

			$data['task_id'] = $task_id;
		}

		return new Task_Local( $data );
	}
}
