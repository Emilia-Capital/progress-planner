<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Badges;

/**
 * Badge content widget.
 */
final class Badge_Streak extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'badge-streak';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		$details = self::get_badge_details();
		?>
		<div class="prpl-badges-columns-wrapper">
			<div class="prpl-badge-wrapper">
				<span
					class="prpl-badge"
					data-value="<?php echo \esc_attr( $details['progress']['percent'] ); ?>"
				>
					<div
						class="prpl-badge-gauge"
						style="
							--value:<?php echo \esc_attr( $details['progress']['percent'] / 100 ); ?>;
							--max: 360deg;
							--start: 180deg;
							--color:<?php echo \esc_attr( $details['color'] ); ?>
						">
						<?php require $details['badge']['icons-svg']['complete']['path']; ?>
					</div>
				</span>
				<span class="progress-percent"><?php echo \esc_attr( $details['progress']['percent'] ); ?>%</span>
			</div>
			<div class="prpl-badge-content-wrapper">
				<h2 class="prpl-widget-title">
					<?php echo \esc_html( $details['badge']['name'] ); ?>
				</h2>
				<p>
					<?php
					printf(
						/* translators: %s: The number of weeks. */
						\esc_html__( '%s weeks to go to complete this streak!', 'progress-planner' ),
						\esc_html( $details['progress']['remaining'] )
					);
					?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the badge.
	 *
	 * @return array
	 */
	public static function get_badge_details() {
		$result = [];
		$badges = [
			'progress-professional',
			'maintenance-maniac',
			'super-site-specialist',
		];

		// Get the badge to display.
		foreach ( $badges as $badge ) {
			$progress = Badges::get_badge_progress( $badge );
			if ( 100 > $progress['percent'] ) {
				break;
			}
		}
		$result['progress'] = $progress;
		$result['badge']    = Badges::get_badge( $badge );

		$result['color'] = 'var(--prpl-color-accent-red)';
		if ( $result['percent'] > 50 ) {
			$result['color'] = 'var(--prpl-color-accent-orange)';
		}
		if ( $result['percent'] > 75 ) {
			$result['color'] = 'var(--prpl-color-accent-green)';
		}
		return $result;
	}
}