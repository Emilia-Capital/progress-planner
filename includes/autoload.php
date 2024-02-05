<?php
/**
 * Autoload PHP classes for the plugin.
 *
 * @package ProgressPlanner
 */

spl_autoload_register(
	function ( $class_name ) {
		$prefix = 'ProgressPlanner\\';

		if ( 0 !== \strpos( $class_name, $prefix ) ) {
			return;
		}

		$class_name = \str_replace( $prefix, '', $class_name );

		$parts = \explode( '\\', $class_name );
		$file  = PROGRESS_PLANNER_DIR . '/includes/';
		$last  = \array_pop( $parts );

		foreach ( $parts as $part ) {
			$file .= strtolower( $part ) . '/';
		}
		$file .= 'class-' . \str_replace( '_', '-', \strtolower( $last ) ) . '.php';

		if ( \file_exists( $file ) ) {
			require_once $file;
		}
	}
);
