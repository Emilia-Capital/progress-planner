<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Badges;
use Progress_Planner\Badges\Badge\Monthly;

/**
 * Badge content widget.
 */
final class Badge_Monthly extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'badge-monthly';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		$monthly = Monthly::get_instances();
		?>
		<div class="prpl-badges-columns-wrapper">
			<table>
				<?php foreach ( $monthly as $month => $badge ) : ?>
					<tr class="prpl-badges-column">
						<th><?php echo esc_html( $badge->get_name() ); ?></th>
						<td>
							<progress
								max="100"
								value="<?php echo (int) $badge->progress_callback()['progress']; ?>"
							></progress>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<?php
	}
}
