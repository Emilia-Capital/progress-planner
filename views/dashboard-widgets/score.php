<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Query;

$show_badges = (
	$this->get_badge_details( 'content' )['progress']['progress'] ||
	$this->get_badge_details( 'streak' )['progress']['progress']
);

?>
<div class="prpl-dashboard-widget<?php echo ( $show_badges ) ? ' show-badges' : ''; ?>">
	<div class="prpl-score-gauge" style="--background: #fff">
		<?php ( new \Progress_Planner\Widgets\Activity_Scores() )->print_score_gauge(); ?>
	</div>
	<?php if ( $show_badges ) : ?>
		<div class="grid-separator"></div>
		<div class="prpl-badges">
			<h3><?php \esc_html_e( 'Next badges', 'progress-planner' ); ?></h3>
			<?php foreach ( [ 'content', 'streak' ] as $category ) : ?>
				<?php
				$details = $this->get_badge_details( $category );
				if ( 100 <= (int) $details['progress']['progress'] ) {
					return;
				}
				?>
				<div class="prpl-badges-columns-wrapper">
					<div class="prpl-badge-wrapper">
						<span
							class="prpl-badge"
							data-value="<?php echo \esc_attr( $details['progress']['progress'] ); ?>"
						>
							<div
								class="prpl-badge-gauge"
								style="
									--value:<?php echo (float) ( $details['progress']['progress'] / 100 ); ?>;
									--max: 360deg;
									--start: 180deg;
								">
								<?php require PROGRESS_PLANNER_DIR . '/assets/images/badges/' . $details['badge']['id'] . '.svg'; ?>
							</div>
						</span>
						<span class="progress-percent"><?php echo \esc_attr( $details['progress']['progress'] ); ?>%</span>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<div class="prpl-dashboard-widget-latest-activities">
	<h3><?php \esc_html_e( 'Latest activity', 'progress-planner' ); ?></h3>

	<?php
	$latest_activities = Query::get_instance()->get_latest_activities( 2 );
	$activity_type_map = [
		'content-publish'               => __( 'Published content', 'progress-planner' ),
		'content-update'                => __( 'Updated content', 'progress-planner' ),
		'content-delete'                => __( 'Deleted content', 'progress-planner' ),
		'maintenance-activate_plugin'   => __( 'Activated a plugin', 'progress-planner' ),
		'maintenance-deactivate_plugin' => __( 'Deactivated a plugin', 'progress-planner' ),
		'maintenance-install_plugin'    => __( 'Installed a plugin', 'progress-planner' ),
		'maintenance-uninstall_plugin'  => __( 'Uninstalled a plugin', 'progress-planner' ),
		'maintenance-update_plugin'     => __( 'Updated a plugin', 'progress-planner' ),
		'maintenance-activate_theme'    => __( 'Activated a theme', 'progress-planner' ),
		'maintenance-deactivate_theme'  => __( 'Deactivated a theme', 'progress-planner' ),
		'maintenance-install_theme'     => __( 'Installed a theme', 'progress-planner' ),
		'maintenance-uninstall_theme'   => __( 'Uninstalled a theme', 'progress-planner' ),
		'maintenance-update_theme'      => __( 'Updated a theme', 'progress-planner' ),
		'maintenance-update_core'       => __( 'Updated WordPress Core', 'progress-planner' ),
		'todo-add'                      => __( 'Added a task to the to-do list', 'progress-planner' ),
		'todo-update'                   => __( 'Updated a task in the to-do list', 'progress-planner' ),
		'todo-delete'                   => __( 'Deleted a task from the to-do list', 'progress-planner' ),
	];
	?>
	<ul>
		<?php foreach ( $latest_activities as $activity ) : ?>
			<li>
				<span class="activity-type">
					<?php
					if ( isset( $activity_type_map[ $activity->category . '-' . $activity->type ] ) ) {
						echo \esc_html( $activity_type_map[ $activity->category . '-' . $activity->type ] );
					} elseif ( 'content' === $activity->category ) {
						\esc_html_e( 'Updated content', 'progress-planner' );
					} elseif ( 'maintenance' === $activity->category ) {
						\esc_html_e( 'Site maintenance', 'progress-planner' );
					} elseif ( 'todo' === $activity->category ) {
						\esc_html_e( 'Updated To-do list', 'progress-planner' );
					}
					?>
				</span>
				<span class="activity-date">
					<?php echo \esc_html( \date_i18n( \get_option( 'date_format' ), strtotime( $activity->date->format( 'Y-m-d' ) ) ) ); ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<div class="prpl-dashboard-widget-footer">
	<img src="<?php echo \esc_attr( PROGRESS_PLANNER_URL . '/assets/images/icon_progress_planner.svg' ); ?>" style="width:1.5em;" alt="" />
	<a href="<?php echo \esc_url( \get_admin_url( null, 'admin.php?page=progress-planner' ) ); ?>">
		<?php \esc_html_e( 'Check out all your stats and badges', 'progress-planner' ); ?>
	</a>
</div>
