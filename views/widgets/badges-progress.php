<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

// Get an array of badges.
$prpl_badges = \progress_planner()->get_badges()->get_badges();

/**
 * Callback to get the progress color.
 *
 * @param int $progress The progress.
 *
 * @return string The color.
 */
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
	<?php
	if ( true !== $prpl_badge['public'] ) {
		continue;
	}
	?>
	<div class="progress-wrapper">
		<?php $prpl_badge_progress = \progress_planner()->get_badges()->get_badge_progress( $prpl_badge['id'] ); ?>
		<?php foreach ( $prpl_badge_progress as $prpl_badge_step => $prpl_badge_step_progress ) : ?>
			<?php $prpl_badge_completed = 100 === (int) $prpl_badge_step_progress['progress']; ?>
			<span
				class="prpl-badge"
				data-value="<?php echo esc_attr( $prpl_badge_step_progress['progress'] ); ?>"
			>
				<?php
				include $prpl_badge_completed
					? $prpl_badge_step_progress['icons']['complete']['path']
					: $prpl_badge_step_progress['icons']['pending']['path'];
				?>
				<p><?php echo esc_html( $prpl_badge_step_progress['name'] ); ?></p>
			</span>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
