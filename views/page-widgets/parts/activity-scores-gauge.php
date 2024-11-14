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

<prpl-gauge background="var(--prpl-background-orange)" color="<?php echo esc_attr( $prpl_widget->get_gauge_color( $prpl_widget->get_score() ) ); ?>" contentFontSize="var(--prpl-font-size-6xl)">
	<progress max="100" value="<?php echo (float) $prpl_widget->get_score(); ?>">
		<?php echo \esc_html( $prpl_widget->get_score() ); ?>
	</progress>
</prpl-gauge>
