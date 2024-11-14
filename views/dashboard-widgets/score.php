<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

$prpl_show_badges = (
	\progress_planner()->get_admin__dashboard_widget_score()->get_badge_details( 'content' )['progress']['progress'] ||
	\progress_planner()->get_admin__dashboard_widget_score()->get_badge_details( 'maintenance' )['progress']['progress']
);

?>
<div class="prpl-dashboard-widget<?php echo ( $prpl_show_badges ) ? ' show-badges' : ''; ?>">
	<div class="prpl-score-gauge" style="--background: #fff">
		<?php \progress_planner()->get_widgets__activity_scores()->print_score_gauge(); ?>
	</div>

	<prpl-gauge
		value="<?php echo (float) \progress_planner()->get_admin__page()->get_widget( 'suggested-tasks' )->get_score() / \Progress_Planner\Badges\Monthly::TARGET_POINTS; ?>"
		max="<?php echo (int) \Progress_Planner\Badges\Monthly::TARGET_POINTS; ?>"
		background="var(--prpl-background-orange)"
		color="var(--prpl-color-accent-orange)"
		contentFontSize="var(--prpl-font-size-4xl)"
	>
		<?php \progress_planner()->get_badges()->get_badge( 'monthly-' . gmdate( 'Y' ) . '-m' . (int) gmdate( 'm' ) )->the_icon( true ); ?>
	</prpl-gauge>
</div>

<div class="prpl-dashboard-widget-latest-activities">
	<h3><?php \esc_html_e( 'Latest activity', 'progress-planner' ); ?></h3>

	<?php
	$prpl_latest_activities = \progress_planner()->get_query()->get_latest_activities( 2 );
	$prpl_activity_type_map = [
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
		<?php foreach ( $prpl_latest_activities as $prpl_activity ) : ?>
			<li>
				<span class="activity-type">
					<?php
					if ( isset( $prpl_activity_type_map[ $prpl_activity->category . '-' . $prpl_activity->type ] ) ) {
						echo \esc_html( $prpl_activity_type_map[ $prpl_activity->category . '-' . $prpl_activity->type ] );
					} elseif ( 'content' === $prpl_activity->category ) {
						\esc_html_e( 'Updated content', 'progress-planner' );
					} elseif ( 'maintenance' === $prpl_activity->category ) {
						\esc_html_e( 'Site maintenance', 'progress-planner' );
					} elseif ( 'todo' === $prpl_activity->category ) {
						\esc_html_e( 'Updated To-do list', 'progress-planner' );
					}
					?>
				</span>
				<span class="activity-date">
					<?php echo \esc_html( \date_i18n( \get_option( 'date_format' ), strtotime( $prpl_activity->date->format( 'Y-m-d' ) ) ) ); ?>
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
