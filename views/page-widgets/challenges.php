<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prpl_challenge = \progress_planner()->get_widgets__challenges()->get_current_challenge();
?>
<h2 class="prpl-widget-title">
	<?php echo \esc_html( $prpl_challenge['name'] ); ?>
</h2>


<div class="prpl-challenge-content">
	<?php echo \wp_kses_post( $prpl_challenge['content'] ); ?>
</div>
