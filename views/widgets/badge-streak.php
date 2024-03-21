<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

use ProgressPlanner\Badges;

$prpl_badges = [
	'progress-professional',
	'maintenance-maniac',
	'super-site-specialist',
];

// Get the badge to display.
foreach ( $prpl_badges as $prpl_badge ) {
	$prpl_progress = Badges::get_badge_progress( $prpl_badge );
	if ( 100 > $prpl_progress['percent'] ) {
		break;
	}
}
$prpl_badge = Badges::get_badge( $prpl_badge );

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
			data-value="<?php echo \esc_attr( $prpl_progress['percent'] ); ?>"
		>
			<div
				class="prpl-badge-gauge"
				style="
					--value:<?php echo \esc_attr( $prpl_progress['percent'] / 100 ); ?>;
					--max: 360deg;
					--start: 180deg;
					--color:<?php echo \esc_attr( $prpl_get_progress_color( $prpl_progress['percent'] ) ); ?>
				">
				<?php require $prpl_badge['icons-svg']['complete']['path']; ?>
			</div>
		</span>
		<span class="progress-percent"><?php echo \esc_attr( $prpl_progress['percent'] ); ?>%</span>
	</div>
	<div class="prpl-badge-content-wrapper">
		<h2 class="prpl-widget-title">
			<?php echo \esc_html( $prpl_badge['name'] ); ?>
		</h2>
		<p>
			<?php
			printf(
				/* translators: %s: The number of weeks. */
				\esc_html__( '%s weeks to go to complete this streak!', 'progress-planner' ),
				\esc_html( $prpl_progress['remaining'] )
			);
			?>
		</p>
	</div>
</div>
