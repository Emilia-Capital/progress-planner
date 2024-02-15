<?php
/**
 * View for the streak.
 *
 * @package ProgressPlanner
 */

$prpl_streak_nr    = ( new \ProgressPlanner\Streaks() )->get_weekly_post_streak();
$prpl_streak_color = 'hsl(' . min( 100, $prpl_streak_nr * 10 ) . ', 100%, 40%)';
?>
<div>
	<h2><?php esc_html_e( 'Streak (weekly post)', 'progress-planner' ); ?></h2>
	<p class="prpl-streak" style="font-size:6em;color:<?php echo esc_attr( $prpl_streak_color ); ?>;">
		<?php echo esc_html( $prpl_streak_nr ); ?>
	</p>
</div>
