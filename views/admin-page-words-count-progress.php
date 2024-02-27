<?php
/**
 * Print the graph for words count progress.
 *
 * @package ProgressPlanner
 */

echo '<div style="max-width:1000px;">';
echo '<h2>';
esc_html_e( 'Words count progress', 'progress-planner' );
echo '</h2>';

( new \ProgressPlanner\Charts\Posts() )->render(
	'post',
	'words',
	\ProgressPlanner\Admin\Page::get_params()['filter_interval'],
	\ProgressPlanner\Admin\Page::get_params()['filter_number'],
	0
);
echo '</div>';
