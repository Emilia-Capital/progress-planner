<?php
/**
 * Uninstall the plugin.
 *
 * Deletes the custom database tables, and the plugin options.
 *
 * @package ProgressPlanner
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once __DIR__ . '/includes/class-settings.php';
require_once __DIR__ . '/includes/class-query.php';

// Delete the plugin options.
delete_option( \ProgressPlanner\Settings::OPTION_NAME );

// Delete the custom database tables.
global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
	$wpdb->prepare(
		// phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnsupportedIdentifierPlaceholder, WordPress.DB.DirectDatabaseQuery.SchemaChange
		'DROP TABLE IF EXISTS %i',
		$wpdb->prefix . \ProgressPlanner\Query::TABLE_NAME
	)
);
