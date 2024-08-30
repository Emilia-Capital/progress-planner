<?php
/**
 * File that creates a better playground environment for the Progress Planner plugin.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

/**
 * Class Playground
 */
class Playground {

	/**
	 * Constructor.
	 */
	public function __construct() {
		\add_action( 'init', [ $this, 'register_hooks' ] );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		if ( ! \get_option( 'progress_planner_license_key', false ) ) {
			$this->generate_data();
			\update_option( 'progress_planner_license_key', str_replace( ' ', '-', $this->create_random_string( 20 ) ) );
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- This is a demo.
			\update_option( 'progress_planner_todo', unserialize( 'a:2:{i:0;a:2:{s:7:"content";s:48:"Update a post to see that the plugin tracks that";s:4:"done";b:0;}i:1;a:2:{s:7:"content";s:24:"Try out Progress Planner";s:4:"done";b:0;}}' ) );
		}
		\add_action( 'admin_notices', [ $this, 'admin_notices' ] );
	}

	/**
	 * Print an admin notice helping people.
	 *
	 * @return void
	 */
	public function admin_notices() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We're not processing any data.
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'progress-planner' ) {
			return;
		}

		echo '<div id="progress-planner-playground-notice" class="notice notice-success" style="margin-bottom:40px">';
		echo '<p style="max-width:600px;"><strong>' . esc_html__( 'Progress Planner demo', 'progress-planner' ) . '</strong><br>';
		\esc_html_e( 'This is a demo of Progress Planner. We\'ve prefilled this site with some content to show you what the reports in Progress Planner look like. We\'ve also added a few to-do\'s for you, you can see these here and on your dashoard.', 'progress-planner' );
		echo '</p>';
		echo '</div>';
	}

	/**
	 * Generate random posts & terms.
	 *
	 * @return void
	 */
	public function generate_data() {
		$post_ids = [];
		for ( $i = 0; $i < 20; $i++ ) {
			$post_ids[] = $this->create_random_post();
		}
	}

	/**
	 * Create a random post.
	 *
	 * @return int Post ID.
	 */
	private function create_random_post() {
		$postarr = [
			'post_title'   => str_replace( '.', '', $this->create_random_string( 5 ) ),
			'post_content' => $this->create_random_string( wp_rand( 200, 500 ) ),
			'post_status'  => 'publish',
			'post_type'    => 'post',
		];

		return \wp_insert_post( $postarr );
	}

	/**
	 * Create a random string of content.
	 *
	 * @param int $length Length of the string to create.
	 *
	 * @return string Random string.
	 */
	private function create_random_string( $length ) {
		$words     = [ 'the', 'and', 'have', 'that', 'for', 'you', 'with', 'say', 'this', 'they', 'but', 'his', 'from', 'not', 'she', 'as', 'what', 'their', 'can', 'who', 'get', 'would', 'her', 'all', 'make', 'about', 'know', 'will', 'one', 'time', 'there', 'year', 'think', 'when', 'which', 'them', 'some', 'people', 'take', 'out', 'into', 'just', 'see', 'him', 'your', 'come', 'could', 'now', 'than', 'like', 'other', 'how', 'then', 'its', 'our', 'two', 'more', 'these', 'want', 'way', 'look', 'first', 'also', 'new', 'because', 'day', 'use', 'man', 'find', 'here', 'thing', 'give', 'many', 'well', 'only', 'those', 'tell', 'very', 'even', 'back', 'any', 'good', 'woman', 'through', 'life', 'child', 'work', 'down', 'may', 'after', 'should', 'call', 'world', 'over', 'school', 'still', 'try', 'last', 'ask', 'need' ];
		$sentences = '';

		// phpcs:ignore Squiz.PHP.DisallowSizeFunctionsInLoops.Found -- This works fine.
		while ( \strlen( $sentences ) < $length ) {
			$sentence  = '';
			$word_keys = \array_rand( $words, \min( $length - \strlen( $sentence ), \count( $words ) ) );
			foreach ( (array) $word_keys as $key ) {
				$sentence .= $words[ $key ] . ' ';
			}
			$sentences .= \ucfirst( \trim( $sentence ) ) . '.';
		}

		return $sentences;
	}
}
