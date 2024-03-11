<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

// Get an array of badges.
$prpl_badges = \progress_planner()->get_badges()->get_badge_progress( 'streak_any_task' );

// Get the badge to display.
foreach ( $prpl_badges as $prpl_badge_step ) {
	$prpl_badge = $prpl_badge_step;
	if ( 100 > $prpl_badge_step['progress'] ) {
		$prpl_badge = $prpl_badge_step;
		break;
	}
}

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
<div class="prpl-badges-columns-wrapper">
	<div class="prpl-badge-wrapper">
		<span
			class="prpl-badge"
			data-value="<?php echo esc_attr( $prpl_badge['progress'] ); ?>"
		>
			<div
				class="prpl-badge-gauge"
				style="
					--value:<?php echo esc_attr( $prpl_badge['progress'] / 100 ); ?>;
					--color:<?php echo esc_attr( $prpl_get_progress_color( $prpl_badge['progress'] ) ); ?>
				">
				<?php include $prpl_badge['icons'][1]; ?>
			</div>
		</span>
	</div>
	<div class="prpl-badge-content-wrapper">
		<h2 class="prpl-widget-title">
			<?php echo esc_html( $prpl_badge['name'] ); ?>
		</h2>
	</div>
</div>
