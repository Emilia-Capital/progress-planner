<?php
/**
 * Class Page_Transition_Test
 *
 * @package Progress_Planner\Tests
 */

/**
 * Page types test case.
 */
class Page_Transition_Test extends \WP_UnitTestCase {

	/**
	 * Page types object.
	 *
	 * @var Page_Types
	 */
	protected $page_types;

	/**
	 * Setup the test case.
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();

		$this->page_types = \progress_planner()->get_page_types();
	}

	/**
	 * Test transition post status updates options.
	 *
	 * @return void
	 */
	public function test_transition_post_status_updates_options() {
		// Create a draft page.
		$post_id = $this->factory->post->create(
			[
				'post_type'   => 'page',
				'post_status' => 'draft',
			]
		);

		// Check if the term already exists.
		$term = term_exists( 'homepage', $this->page_types::TAXONOMY_NAME );
		if ( ! $term ) {
			// Create a term if it doesn't exist.
			$term = \wp_insert_term( 'Homepage', $this->page_types::TAXONOMY_NAME, [ 'slug' => 'homepage' ] );
		}

		// Assign the term to the post.
		\wp_set_object_terms( $post_id, $term['term_id'], $this->page_types::TAXONOMY_NAME );

		// Update the post status to publish.
		wp_update_post(
			[
				'ID'          => $post_id,
				'post_status' => 'publish',
			]
		);

		// Check if the options are updated.
		$this->assertEquals( $post_id, get_option( 'page_on_front' ) );
		$this->assertEquals( 'page', get_option( 'show_on_front' ) );
	}
}
