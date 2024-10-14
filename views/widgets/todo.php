<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

?>
<p><?php \esc_html_e( 'Write down all your website maintenance tasks you want to get done!', 'progress-planner' ); ?></p>
<?php self::the_todo_list(); ?>
