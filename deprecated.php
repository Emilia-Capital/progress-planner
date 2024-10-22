<?php
/**
 * Deprecated functions.
 *
 * @package Progress_Planner
 */

/**
 * Get the progress planner instance.
 *
 * @return \Progress_Planner\Base
 */
function progress_planner() {
	global $progress_planner;
	_deprecated_function( __FUNCTION__, '0.9.6' );
	return $progress_planner;
}
