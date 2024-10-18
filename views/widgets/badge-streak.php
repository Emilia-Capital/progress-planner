<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Badges;

$streak_badge_details  = $this->get_streak_badge_details();
$content_badge_details = $this->get_content_badge_details();


$badges = [
	'content'     => [ 'wonderful-writer', 'bold-blogger', 'awesome-author' ],
	'maintenance' => [ 'progress-padawan', 'maintenance-maniac', 'super-site-specialist' ],
];

?>
<div class="prpl-badges-columns-wrapper">
	<div class="prpl-badge-wrapper">
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
				<?php require $streak_badge_details['badge']['icons-svg']['complete']['path']; ?>
			</div>
		</span>
		<span class="progress-percent"><?php echo \esc_attr( $streak_badge_details['progress']['progress'] ); ?>%</span>
	</div>
	<div class="prpl-badge-content-wrapper">
		<h2 class="prpl-widget-title">
			<?php echo \esc_html( $streak_badge_details['badge']['name'] ); ?>
		</h2>
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


<div class="prpl-badges-columns-wrapper">
	<div class="prpl-badge-wrapper">
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
				<?php require $content_badge_details['badge']['icons-svg']['complete']['path']; ?>
			</div>
		</span>
		<span class="progress-percent"><?php echo \esc_attr( $content_badge_details['progress']['progress'] ); ?>%</span>
	</div>
	<div class="prpl-badge-content-wrapper">
		<h2 class="prpl-widget-title">
			<?php echo \esc_html( $content_badge_details['badge']['name'] ); ?>
		</h2>
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

<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Your Achievements', 'progress-planner' ); ?>
	<?php new \Progress_Planner\Popups\Badges(); ?>
</h2>
<div class="prpl-widget-content">
	<?php \esc_html_e( 'Check out your progress! Which badge will you unlock next?', 'progress-planner' ); ?>
</div>
<?php foreach ( $badges as $badge_group => $group_badges ) : ?>
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
					? $badge_args['icons-svg']['complete']['path']
					: $badge_args['icons-svg']['pending']['path'];
				?>
				<p><?php echo \esc_html( Badges::get_badge( $badge )['name'] ); ?></p>
			</span>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
