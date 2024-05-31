<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Widgets\Widget;

/**
 * Whats new widget.
 */
final class Whats_New extends Widget {

	/**
	 * The remote server ROOT URL.
	 *
	 * @var string
	 */
	const REMOTE_SERVER_ROOT_URL = 'https://progressplanner.com';

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
		$blog_feed = $this->get_blog_feed();
		?>
		<h2 class="prpl-widget-title">
			<?php \esc_html_e( 'What\'s new on the Progress Planner blog', 'progress-planner' ); ?>
		</h2>

		<ul class="two-col">
			<?php foreach ( $blog_feed as $blog_post ) : ?>
				<li>
					<a href="<?php echo \esc_url( $blog_post['link'] ); ?>" target="_blank">
						<h3><?php echo \esc_html( $blog_post['title']['rendered'] ); ?></h3>
					</a>
					<p>
						<?php echo \esc_html( wp_trim_words( \wp_strip_all_tags( $blog_post['content']['rendered'] ), 55 ) ); ?>
					</p>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<a href="<?php echo \esc_url( self::REMOTE_SERVER_ROOT_URL . '/blog' ); ?>" target="_blank">
				<?php \esc_html_e( 'Read all posts', 'progress-planner' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * Get the feed from the blog.
	 *
	 * @return array
	 */
	public function get_blog_feed() {
		$feed = \get_site_transient( 'progress_planner_blog_feed' );
		if ( false === $feed ) {
			// Get the feed using the REST API.
			$response = \wp_remote_get( self::REMOTE_SERVER_ROOT_URL . '/wp-json/wp/v2/posts/?per_page=2' );
			if ( \is_wp_error( $response ) ) {
				return [];
			}
			$feed = json_decode( \wp_remote_retrieve_body( $response ), true );
			\set_site_transient( 'progress_planner_blog_feed', $feed, 1 * DAY_IN_SECONDS );
		}
		return $feed;
	}
}

