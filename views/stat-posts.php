<?php
/**
 * View for the admin page - single stat.
 *
 * @package ProgressPlanner
 */
?>

<h4>
	<?php
	/* translators: %s: The period. */
	printf( esc_html__( 'Period: %s', 'progress-planner' ), esc_html( $line['period'] ) );
	?>
</h4>
<p>
	<?php
	printf(
		/* translators: 1: The post type label. 2: The number of posts. 3: The number of words. */
		esc_html__( 'Number of %1$s published: %2$s. Words: %3$s', 'progress-planner' ),
		esc_html( $line['post_type_label'] ),
		esc_html(
			\ProgressPlanner\Progress_Planner::get_instance()
				->get_stats()
				->get_stat( 'posts' )
				->set_post_type( $line['post_type'] )
				->get_data( $line['period'] )['count']
		),
		esc_html(
			\ProgressPlanner\Progress_Planner::get_instance()
				->get_stats()
				->get_stat( 'posts' )
				->set_post_type( $line['post_type'] )
				->get_data( $line['period'] )['word_count']
		)
	);
	?>
</p>
