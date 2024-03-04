<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_badges = \progress_planner()->get_badges()->get_badges();
?>
<h2 class="prpl-widget-title">
	<?php esc_html_e( 'Badges progress', 'progress-planner' ); ?>
</h2>

<?php foreach ( $prpl_badges as $prpl_badge ) : ?>
	<?php
	$prpl_badge_progress       = \progress_planner()->get_badges()->get_badge_progress( $prpl_badge['id'] );
	$prpl_badge_progress_color = 'var(--prpl-color-accent-red)';
	if ( $prpl_badge_progress > 50 ) {
		$prpl_badge_progress_color = 'var(--prpl-color-accent-orange)';
	}
	if ( $prpl_badge_progress > 75 ) {
		$prpl_badge_progress_color = 'var(--prpl-color-accent-green)';
	}
	?>
	<div class="progress-wrapper">
		<p>
			<?php echo esc_html( $prpl_badge['name'] ); ?>
		</p>
		<div class="progress-bar">
			<span style="width: <?php echo esc_attr( $prpl_badge_progress ); ?>%; --color: <?php echo esc_attr( $prpl_badge_progress_color ); ?>;"></span>
		</div>
		<div class="progress-label">
			<?php echo esc_attr( $prpl_badge_progress ); ?>%
			<?php if ( $prpl_badge_progress === 100 ) : ?>
				🏆
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>
