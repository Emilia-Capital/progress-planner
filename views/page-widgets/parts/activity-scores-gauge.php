<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_widget = \progress_planner()->get_admin__page()->get_widget( 'activity-scores' );
?>

<prpl-gauge
	value="<?php echo (float) $prpl_widget->get_score() / 100; ?>"
	max="100"
	start="270deg"
	background="var(--prpl-background-orange)"
	color="<?php echo esc_attr( $prpl_widget->get_gauge_color( $prpl_widget->get_score() ) ); ?>"
	contentFontSize="var(--prpl-font-size-6xl)"
>
	<?php echo \esc_html( $prpl_widget->get_score() ); ?>
</prpl-gauge>
