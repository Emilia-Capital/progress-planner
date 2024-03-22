<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Widgets\Widget;

/**
 * Whats new widget.
 */
final class Whats_New extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'whats-new';

	/**
	 * Render the widget content.
	 */
	public function the_content() {
		// Get the blog feed.
		$prpl_blog_feed = \progress_planner()->get_blog_feed();
		?>
		<h2 class="prpl-widget-title">
			<?php \esc_html_e( 'What\'s new on the Progress Planner blog', 'progress-planner' ); ?>
		</h2>

		<ul>
			<?php foreach ( $prpl_blog_feed as $prpl_blog_post ) : ?>
				<li>
					<a href="<?php echo \esc_url( $prpl_blog_post['link'] ); ?>" target="_blank">
						<h3><?php echo \esc_html( $prpl_blog_post['title']['rendered'] ); ?></h3>
					</a>
					<p>
						<?php echo \esc_html( \wp_strip_all_tags( $prpl_blog_post['excerpt']['rendered'] ) ); ?>
					</p>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}

