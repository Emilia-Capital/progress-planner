<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget                 = \progress_planner()->get_admin__page()->get_widget( 'badge-streak' );
$prpl_widget_context_details = [];
if ( $prpl_widget->get_details( 'content' ) ) {
	$prpl_widget_context_details['content'] = [
		'text' => sprintf(
			\esc_html(
				/* translators: %s: The remaining number of posts or pages to write. */
				\_n(
					'Write %s new post or page and earn your next badge!',
					'Write %s new posts or pages and earn your next badge!',
					(int) $prpl_widget->get_details( 'content' )->get_progress()['remaining'],
					'progress-planner'
				)
			),
			\esc_html( \number_format_i18n( $prpl_widget->get_details( 'content' )->get_progress()['remaining'] ) )
		),
	];
}
if ( $prpl_widget->get_details( 'maintenance' ) ) {
	$prpl_widget_context_details['maintenance'] = [
		'text' => sprintf(
			\esc_html(
				/* translators: %s: The remaining number of weeks. */
				\_n(
					'%s week to go to complete this streak!',
					'%s weeks to go to complete this streak!',
					(int) $prpl_widget->get_details( 'maintenance' )->get_progress()['remaining'],
					'progress-planner'
				)
			),
			\esc_html( \number_format_i18n( $prpl_widget->get_details( 'maintenance' )->get_progress()['remaining'] ) )
		),
	];
}

?>

<h2 class="prpl-widget-title">
	<?php
	\esc_html_e( 'Your streak badges', 'progress-planner' );
	\progress_planner()->get_popover()->the_popover( 'badge-streak' )->render_button(
		'info-outline',
		'<span class="screen-reader-text">' . \esc_html__( 'More info', 'progress-planner' ) . '</span>'
	);
	\progress_planner()->get_popover()->the_popover( 'badge-streak' )->render();
	?>
</h2>

<div class="prpl-latest-badges-wrapper">
	<?php
	$prpl_current_context = 0;
	$prpl_contexts_count  = count( array_keys( $prpl_widget_context_details ) );
	?>
	<?php foreach ( $prpl_widget_context_details as $prpl_context => $prpl_details ) : ?>
		<?php ++$prpl_current_context; ?>
		<prpl-gauge background="<?php echo \esc_attr( $prpl_widget->get_details( $prpl_context )->get_background() ); ?>" color="var(--prpl-color-accent-orange)">
			<progress max="100" value="<?php echo (float) $prpl_widget->get_details( $prpl_context )->get_progress()['progress']; ?>">
				<?php $prpl_widget->get_details( $prpl_context )->the_icon( true ); ?>
			</progress>
		</prpl-gauge>
		<div class="prpl-badge-content-wrapper">
			<h3><?php echo \esc_html( $prpl_widget->get_details( $prpl_context )->get_name() ); ?></h3>
			<p><?php echo \esc_html( $prpl_details['text'] ); ?></p>
		</div>
		<?php if ( $prpl_current_context < $prpl_contexts_count ) : ?>
			<hr>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
