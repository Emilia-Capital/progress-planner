<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_badge           = \progress_planner()->get_badges()->get_badge( 'monthly-2024-m12' );
$prpl_badge_progress  = $prpl_badge->get_progress();
$prpl_badge_completed = 100 === (int) $prpl_badge_progress['progress'];

$prpl_location  = '';
$prpl_css_class = '';

if ( isset( $args['css_class'] ) ) {
	$prpl_css_class = \esc_attr( $args['css_class'] );
}

$prpl_location = false !== strpos( $prpl_css_class, 'in-popover' ) ? 'popover' : 'suggested-tasks';

if ( ! $prpl_badge_completed && 'popover' !== $prpl_location ) {
	return;
}

$prpl_title_tag = isset( $args['title_tag'] ) ? \esc_attr( $args['title_tag'] ) : 'h3';
?>

<div class="<?php echo \esc_attr( $prpl_css_class ); ?>">
	<<?php echo \esc_html( $prpl_title_tag ); ?> class="prpl-widget-title">
		<?php \esc_html_e( 'Your monthly badge 2024', 'progress-planner' ); ?>
	</<?php echo \esc_html( $prpl_title_tag ); ?>>
	<div class="prpl-ravi-reward-container">
		<img
			src="<?php echo \esc_url( \progress_planner()->get_admin__page()->get_widget( 'latest-badge' )->endpoint . $prpl_badge->get_id() ); ?>"
			alt="<?php echo \esc_attr( $prpl_badge->get_name() ); ?>"
			class="<?php echo $prpl_badge_completed ? 'complete' : 'incomplete'; ?>"
		/>
	</div>
</div>
