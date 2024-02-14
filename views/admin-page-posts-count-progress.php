<?php
/**
 * Print the graph for posts count progress.
 *
 * @package ProgressPlanner
 */

echo '<h2>';
esc_html_e( 'Posts count progress', 'progress-planner' );
echo '</h2>';

( new \ProgressPlanner\Charts\Posts() )->render(
	$prpl_stats_posts->get_post_types_names(),
	'count',
	$prpl_filters_interval,
	$prpl_filters_number,
	0
);
