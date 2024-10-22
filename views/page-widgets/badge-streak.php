<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Badges;

$streak_badge_details  = $this->get_streak_badge_details();
$content_badge_details = $this->get_content_badge_details();

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $progress_planner;

?>

<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your streak badges', 'progress-planner' ); ?>
	<?php $progress_planner->get_popovers()->badges->render(); ?>
</h2>

<div class="prpl-latest-badges-wrapper">
	<div class="prpl-badges-columns-wrapper">
		<div class="prpl-badge-wrapper" style="--background: var(--prpl-background-blue);">
			<span
				class="prpl-badge"
				data-value="<?php echo \esc_attr( $content_badge_details['progress']['progress'] ); ?>"
			>
				<div
					class="prpl-badge-gauge"
					style="
						--value:<?php echo (float) ( $content_badge_details['progress']['progress'] / 100 ); ?>;
						--max: 360deg;
						--start: 180deg;
					">
					<?php require PROGRESS_PLANNER_DIR . '/assets/images/badges/' . $content_badge_details['badge']['id'] . '.svg'; ?>
				</div>
			</span>
			<span class="progress-percent"><?php echo \esc_attr( $content_badge_details['progress']['progress'] ); ?>%</span>
		</div>
		<div class="prpl-badge-content-wrapper">
			<h3><?php echo \esc_html( $content_badge_details['badge']['name'] ); ?></h3>
			<p>
				<?php
				printf(
					\esc_html(
						/* translators: %s: The remaining number of posts or pages to write. */
						\_n(
							'Write %s new post or page and earn your next badge!',
							'Write %s new posts or pages and earn your next badge!',
							(int) $content_badge_details['progress']['remaining'],
							'progress-planner'
						)
					),
					\esc_html( \number_format_i18n( $content_badge_details['progress']['remaining'] ) )
				);
				?>
			</p>
		</div>
	</div>

	<hr>

	<div class="prpl-badges-columns-wrapper">
		<div class="prpl-badge-wrapper" style="--background: var(--prpl-background-red);">
			<span
				class="prpl-badge"
				data-value="<?php echo \esc_attr( $streak_badge_details['progress']['progress'] ); ?>"
			>
				<div
					class="prpl-badge-gauge"
					style="
						--value:<?php echo (float) ( $streak_badge_details['progress']['progress'] / 100 ); ?>;
						--max: 360deg;
						--start: 180deg;
					">
					<?php require PROGRESS_PLANNER_DIR . '/assets/images/badges/' . $streak_badge_details['badge']['id'] . '.svg'; ?>
				</div>
			</span>
			<span class="progress-percent"><?php echo \esc_attr( $streak_badge_details['progress']['progress'] ); ?>%</span>
		</div>
		<div class="prpl-badge-content-wrapper">
			<h3><?php echo \esc_html( $streak_badge_details['badge']['name'] ); ?></h3>
			<p>
				<?php
				printf(
					\esc_html(
						/* translators: %s: The remaining number of weeks. */
						\_n(
							'%s week to go to complete this streak!',
							'%s weeks to go to complete this streak!',
							(int) $streak_badge_details['progress']['remaining'],
							'progress-planner'
						)
					),
					\esc_html( \number_format_i18n( $streak_badge_details['progress']['remaining'] ) )
				);
				?>
			</p>
		</div>
	</div>
</div>

<hr>

<h3><?php esc_html_e( 'Your achievements', 'progress-planner' ); ?></h3>
<div class="prpl-badges-container-achievements">
	<?php
	foreach ( [
		'content'     => [ 'wonderful-writer', 'bold-blogger', 'awesome-author' ],
		'maintenance' => [ 'progress-padawan', 'maintenance-maniac', 'super-site-specialist' ],
	] as $badge_group => $group_badges ) :
		?>
		<div class="progress-wrapper badge-group-<?php echo \esc_attr( $badge_group ); ?>">
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
					include $badge_completed // phpcs:ignore PEAR.Files.IncludingFile.UseRequire
						? PROGRESS_PLANNER_DIR . '/assets/images/badges/' . $badge . '.svg'
						: PROGRESS_PLANNER_DIR . '/assets/images/badges/' . $badge . '-bw.svg';
					?>
					<p><?php echo \esc_html( Badges::get_badge( $badge )['name'] ); ?></p>
				</span>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
</div>
