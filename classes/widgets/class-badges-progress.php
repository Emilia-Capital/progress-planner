<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Badges;

/**
 * Badges progress widget.
 */
final class Badges_Progress extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'badges-progress';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		$badges = [
			'content'     => [ 'wonderful-writer', 'bold-blogger', 'awesome-author' ],
			'maintenance' => [ 'progress-padawan', 'maintenance-maniac', 'super-site-specialist' ],
		];

		?>
		<h2 class="prpl-widget-title">
			<?php esc_html_e( 'Your Achievements', 'progress-planner' ); ?>
			<?php new \Progress_Planner\Popups\Badges(); ?>
		</h2>

		<?php foreach ( $badges as $badge_group => $group_badges ) : ?>
			<div class="progress-wrapper badge-group-<?php echo esc_attr( $badge_group ); ?>">
				<?php foreach ( $group_badges as $badge ) : ?>
					<?php
					$badge_progress  = Badges::get_badge_progress( $badge );
					$badge_completed = 100 === (int) $badge_progress['progress'];
					$badge_args      = Badges::get_badge( $badge );
					?>
					<span
						class="prpl-badge"
						data-value="<?php echo \esc_attr( $badge_progress['progress'] ); ?>"
					>
						<?php
						include $badge_completed
							? $badge_args['icons-svg']['complete']['path']
							: $badge_args['icons-svg']['pending']['path'];
						?>
						<p><?php echo \esc_html( Badges::get_badge( $badge )['name'] ); ?></p>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
		<?php
	}
}
