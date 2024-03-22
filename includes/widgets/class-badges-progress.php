<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Badges;

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
			'content'     => [ 'wonderful-writer', 'awesome-author', 'notorious-novelist' ],
			'maintenance' => [ 'progress-professional', 'maintenance-maniac', 'super-site-specialist' ],
		];

		?>
		<h2 class="prpl-widget-title">
			<?php esc_html_e( 'Badges progress', 'progress-planner' ); ?>
		</h2>

		<?php foreach ( $badges as $category_badges ) : ?>
			<div class="progress-wrapper">
				<?php foreach ( $category_badges as $category_badge ) : ?>
					<?php
					$badge_progress  = Badges::get_badge_progress( $category_badge );
					$badge_completed = 100 === (int) $badge_progress['percent'];
					$badge_args      = Badges::get_badge( $category_badge );
					?>
					<span
						class="prpl-badge"
						data-value="<?php echo \esc_attr( $badge_progress['percent'] ); ?>"
					>
						<?php
						include $badge_completed
							? $badge_args['icons-svg']['complete']['path']
							: $badge_args['icons-svg']['pending']['path'];
						?>
						<p><?php echo \esc_html( Badges::get_badge( $category_badge )['name'] ); ?></p>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
		<?php
	}
}
