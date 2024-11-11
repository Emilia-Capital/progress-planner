<?php
/**
 * Test Content actions.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Tests;

use DateTime;
use Progress_Planner\Actions\Content;
use WP_UnitTestCase;

/**
 * Class Content_Actions_Test
 */
class Content_Actions_Test extends \WP_UnitTestCase {

	/**
	 * The Content instance.
	 *
	 * @var Content
	 */
	private $content;

	/**
	 * Set up.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->content = new Content();
	}

	/**
	 * Test post insertion hook.
	 */
	public function test_wp_insert_post_hook() {
		$post_data = [
			'post_title'   => 'Test Post',
			'post_content' => 'Test content',
			'post_status'  => 'publish',
			'post_type'    => 'post',
		];

		// Insert a post and verify the hook was called.
		$post_id = wp_insert_post( $post_data );

		// Assert that activities were created.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				'type'     => 'publish',
				'data_id'  => $post_id,
			],
			'RAW'
		);

		$this->assertNotEmpty( $activities );
	}

	/**
	 * Test post update hook.
	 */
	public function test_post_updated_hook() {

		$post_date = \gmdate( 'Y-m-d H:i:s', \strtotime( '-2 day' ) );

		// First create a post.
		$post_id = wp_insert_post(
			[
				'post_title'   => 'Original Title',
				'post_content' => 'Original content',
				'post_status'  => 'publish',
				'post_date'    => $post_date,
				'post_type'    => 'post',
			]
		);

		// Update post activity to date of post.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				'type'     => 'publish',
				'data_id'  => $post_id,
			],
			'ACTIVITIES'
		);

		$activities[0]->date = new DateTime( $post_date );
		$activities[0]->save();

		// Update the post.
		wp_update_post(
			[
				'ID'           => $post_id,
				'post_title'   => 'Updated Title',
				'post_content' => 'Updated content',
			]
		);

		// Assert that update activity was created.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				// 'type'     => 'update',
				'data_id'  => $post_id,
			],
			'RAW'
		);

		$found_activities = [];
		foreach ( $activities as $activity ) {
			if ( 'update' === $activity->type ) {
				$found_activities[] = $activity;
			}
		}

		$this->assertNotEmpty( $found_activities );
	}

	/**
	 * Test post status transition hook.
	 */
	public function test_transition_post_status_hook() {
		// Create a draft post.
		$post_id = wp_insert_post(
			[
				'post_title'   => 'Draft Post',
				'post_content' => 'Draft content',
				'post_status'  => 'draft',
			]
		);

		// Publish the post.
		wp_publish_post( $post_id );

		// Assert that publish activity was created.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				'type'     => 'publish',
				'data_id'  => $post_id,
			],
			'RAW'
		);

		$this->assertNotEmpty( $activities );
	}

	/**
	 * Test post trash hook.
	 */
	public function test_trash_post_hook() {
		// Create a post.
		$post_id = wp_insert_post(
			[
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			]
		);

		// Trash the post.
		wp_trash_post( $post_id );

		// Assert that trash activity was created.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				// 'type'     => 'trash', phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
				'data_id'  => $post_id,
			],
			'RAW'
		);

		$found_activities = [];
		foreach ( $activities as $activity ) {
			if ( 'trash' === $activity->type ) {
				$found_activities[] = $activity;
			}
		}

		$this->assertNotEmpty( $found_activities );
	}

	/**
	 * Test post delete hook.
	 */
	public function test_delete_post_hook() {
		// Create a post.
		$post_id = wp_insert_post(
			[
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			]
		);

		// Delete the post.
		wp_delete_post( $post_id, true );

		// Assert that delete activity was created.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				// 'type'     => 'delete', phpcs:ignore Squiz.Commenting.InlineComment.InvalidEndChar
				'data_id'  => $post_id,
			],
			'RAW'
		);

		$found_activities = [];

		foreach ( $activities as $activity ) {
			if ( 'delete' === $activity->type ) {
				$found_activities[] = $activity;
			}
		}

		$this->assertNotEmpty( $found_activities );
	}

	/**
	 * Test multiple status transitions.
	 */
	public function test_multiple_status_transitions() {

		$post_date = \gmdate( 'Y-m-d H:i:s', \strtotime( '-2 day' ) );

		// Create a draft post.
		$post_id = wp_insert_post(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Test content',
				'post_status'  => 'draft',
				'post_date'    => $post_date,

			]
		);

		// Update post activity to date of post.
		$activities = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				'type'     => 'publish',
				'data_id'  => $post_id,
			],
			'ACTIVITIES'
		);

		$activities[0]->date = new DateTime( $post_date );
		$activities[0]->save();

		// Transition to pending.
		wp_update_post(
			[
				'ID'          => $post_id,
				'post_status' => 'pending',
			]
		);

		// Transition to publish.
		wp_update_post(
			[
				'ID'          => $post_id,
				'post_status' => 'publish',
			]
		);

		// Transition back to draft.
		wp_update_post(
			[
				'ID'          => $post_id,
				'post_status' => 'draft',
			]
		);

		// Assert activities were created in correct order.
		$activities2 = \progress_planner()->get_query()->query_activities(
			[
				'category' => 'content',
				'data_id'  => $post_id,
			],
			'RAW'
		);

		$this->assertNotEmpty( $activities2 );

		// Get the types in order.
		$types = array_map(
			function ( $activity ) {
				return $activity->type;
			},
			$activities2
		);

		$this->assertContains( 'publish', $types ); // Should have publish when first published.
		$this->assertNotContains( 'update', $types ); // Update activity is only added when post is updated.
	}

	/**
	 * Clean up after each test.
	 */
	public function tearDown(): void {
		parent::tearDown();

		// Clean up any posts created during the test.
		$posts = get_posts( [ 'numberposts' => -1 ] );
		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
	}
}
