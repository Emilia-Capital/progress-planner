<?php
/**
 * View for the streak.
 *
 * @package ProgressPlanner
 */

// TODO: DISABLE THIS FOR NOW, IT'S NOT WORKING.
return;

$prpl_streaks = [
	'weekly_post'  => 10, // Number of posts per week, targetting for 10 weeks.
	'weekly_words' => 10, // Number of words per week, targetting for 10 weeks.
];
?>

<div style="display:flex;">
	<?php foreach ( $prpl_streaks as $prpl_streak_id => $prpl_streak_goal ) : ?>
		<div style="text-align:center;border-right:1px dashed;padding:0 1em;max-width: 20em;">
			<?php $prpl_streak = \ProgressPlanner\Streaks::get_instance()->get_streak( $prpl_streak_id, $prpl_streak_goal ); ?>
			<h2><?php echo esc_html( $prpl_streak['title'] ); ?></h2>
			<p><?php echo esc_html( $prpl_streak['description'] ); ?></p>
			<p class="prpl-streak" style="font-size:6em;color:<?php echo esc_attr( $prpl_streak['color'] ); ?>;">
				<?php echo esc_html( $prpl_streak['number'] ); ?>
			</p>
		</div>
	<?php endforeach; ?>
</div>
