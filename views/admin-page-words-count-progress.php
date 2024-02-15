<?php
/**
 * Print the graph for words count progress.
 *
 * @package ProgressPlanner
 */

echo '<h2>';
esc_html_e( 'Words count progress', 'progress-planner' );
echo '</h2>';

( new \ProgressPlanner\Charts\Posts() )->render(
	\ProgressPlanner\Admin\Page::get_params()['stats']->get_post_types_names(),
	'words',
	\ProgressPlanner\Admin\Page::get_params()['filter_interval'],
	\ProgressPlanner\Admin\Page::get_params()['filter_number'],
	0
);
