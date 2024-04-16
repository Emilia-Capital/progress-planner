<?php
/**
 * Activity Scores Widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Popups;

use ProgressPlanner\Badges as Root_Badges;

/**
 * Activity Scores Widget.
 */
final class Badges extends Popup {

	/**
	 * An array of badge IDs.
	 *
	 * @var array<string, string[]>
	 */
	const BADGES = [
		'content'     => [
			'wonderful-writer',
			'bold-blogger',
			'awesome-author',
		],
		'maintenance' => [
			'progress-padawan',
			'maintenance-maniac',
			'super-site-specialist',
		],
	];

	/**
	 * The popup ID.
	 *
	 * @var string
	 */
	protected $id = 'badges-details';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		?>
		<h2><?php \esc_html_e( 'You are on the right track!', 'progress-planner' ); ?></h2>
		<p><?php \esc_html_e( 'Find out what you can do next, to collect all the badges and become a Progress Planner Professional!', 'progress-planner' ); ?></p>

		<div class="prpl-widgets-container">
			<div class="prpl-widget-wrapper">
				<h3><?php \esc_html_e( 'Don’t break your streak by missing a week!', 'progress-planner' ); ?></h3>
				<p><?php \esc_html_e( 'Do at least one activity on your website every week. That could be publishing or adding content and updating a post or updating a plugin. Not able to work on your site? Use your streak freeze.', 'progress-planner' ); ?></p>
				<div id="popup-badges-content">
					<?php $this->print_badges( 'maintenance' ); ?>
				</div>
				<div class="progress-badges">
					<span class="badges-popup-progress-total">
						<span style="width: <?php echo (int) Root_Badges::get_badge_progress( 'super-site-specialist' )['progress']; ?>%"></span>
					</span>
					<div class="indicators-maintenance">
						<?php foreach ( self::BADGES['maintenance'] as $badge ) : ?>
							<div class="indicator">
								<?php $badge_progress = Root_Badges::get_badge_progress( $badge ); ?>
								<span class="indicator-label">
									<?php if ( 0 === (int) $badge_progress['remaining'] ) : ?>
										✔️
									<?php else : ?>
										<?php
										printf(
											/* translators: The number of weeks remaining to complete the badge. */
											\esc_html__( '%s weeks to go', 'progress-planner' ),
											'<span class="number">' . (int) $badge_progress['remaining'] . '</span>'
										);
										?>
									<?php endif; ?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="prpl-widget-wrapper">
				<h3><?php \esc_html_e( 'Keep adding post and pages', 'progress-planner' ); ?></h3>
				<p><?php \esc_html_e( 'The more you write, the closer you come to winning your badges. You can earn level 1 of this badge immediately after installing the plugin if you have written more than 200 posts.', 'progress-planner' ); ?></p>
				<div id="popup-badges-maintenance">
					<?php $this->print_badges( 'content' ); ?>
				</div>
			</div>
		</div>
		<div class="footer">
			<div class="string-freeze-explain">
				<h2><?php \esc_html_e( 'Streak freeze', 'progress-planner' ); ?></h2>
				<p><?php \esc_html_e( 'Going on a holiday? Don\'t have time to work on your site? You can skip your website maintenance for a maximum of one weeks. Your streak will continue afterward.', 'progress-planner' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Print badges by category.
	 *
	 * @param string $category The category of badges.
	 *
	 * @return void
	 */
	public static function print_badges( $category ) {
		?>
		<?php foreach ( self::BADGES[ $category ] as $badge ) : ?>
			<?php
			$badge_progress  = Root_Badges::get_badge_progress( $badge );
			$badge_completed = 100 === (int) $badge_progress['progress'];
			$badge_args      = Root_Badges::get_badge( $badge );
			?>
			<span
				class="prpl-badge"
				data-value="<?php echo \esc_attr( $badge_progress['progress'] ); ?>"
			>
				<div class="inner">
					<?php
					include $badge_completed
						? $badge_args['icons-svg']['complete']['path']
						: $badge_args['icons-svg']['pending']['path'];
					?>
					<?php echo \esc_html( Root_Badges::get_badge( $badge )['name'] ); ?>
				</div>
				<p><?php echo \esc_html( Root_Badges::get_badge( $badge )['description'] ); ?></p>
			</span>
		<?php endforeach; ?>
		<?php
	}
}
