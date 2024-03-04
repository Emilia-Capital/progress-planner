<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_badges = \progress_planner()->get_badges()->get_badges();

$prpl_get_progress_color = function ( $progress ) {
	$color = 'var(--prpl-color-accent-red)';
	if ( $progress > 50 ) {
		$color = 'var(--prpl-color-accent-orange)';
	}
	if ( $progress > 75 ) {
		$color = 'var(--prpl-color-accent-green)';
	}
	return $color;
};

?>
<h2 class="prpl-widget-title">
	<?php esc_html_e( 'Badges progress', 'progress-planner' ); ?>
</h2>

<?php foreach ( $prpl_badges as $prpl_badge ) : ?>
	<div class="progress-wrapper">
		<?php $prpl_badge_progress = \progress_planner()->get_badges()->get_badge_progress( $prpl_badge['id'] ); ?>
		<?php foreach ( $prpl_badge_progress as $prpl_badge_step => $prpl_badge_step_progress ) : ?>
			<?php if ( 100 === (int) $prpl_badge_step_progress['progress'] ) : ?>
				<p>
					<?php echo esc_html( $prpl_badge_step_progress['icon'] ); ?>
					<?php echo esc_html( $prpl_badge_step_progress['name'] ); ?>
				</p>
			<?php else : ?>
				<p><?php echo esc_html( $prpl_badge_step_progress['name'] ); ?></p>
				<div class="progress-bar">
					<span style="width: <?php echo esc_attr( $prpl_badge_step_progress['progress'] ); ?>%; --color: <?php echo esc_attr( $prpl_get_progress_color( $prpl_badge_step_progress['progress'] ) ); ?>;"></span>
				</div>
				<div class="progress-label">
					<?php echo esc_attr( $prpl_badge_step_progress['progress'] ); ?>%
				</div>
				<?php break; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		<hr>
	</div>
<?php endforeach; ?>
