<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'My to-do list', 'progress-planner' ); ?>
</h2>

<p><?php \esc_html_e( 'Write down all your website maintenance tasks you want to get done!', 'progress-planner' ); ?></p>
<?php $this->the_todo_list(); ?>
