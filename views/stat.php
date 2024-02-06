<?php
/**
 * View for the admin page - single stat.
 *
 * @package ProgressPlanner
 */

printf(
	'<p>%s: %s</p>',
	esc_html( $line['label'] ),
	esc_html(
		\ProgressPlanner\Progress_Planner::get_instance()
			->get_stats()
			->get_stat( 'posts' )
			->set_post_type( $line['post_type'] )
			->get_data( $line['period'] )['count']
	)
);
