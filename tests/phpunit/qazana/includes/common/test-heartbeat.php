<?php
namespace Qazana\Testing;

class Qazana_Test_Heartbeat extends Qazana_Test_Base {

	protected $user_own_post;
	protected $user_editor;

	public function setUp() {
		parent::setUp();

		// Create new instance again
		new \Qazana\Heartbeat();
	}

	public function test_postLock() {
		$this->user_own_post = $this->factory()->create_and_get_administrator_user()->ID;
		$this->user_editor = $this->factory()->create_and_get_administrator_user()->ID;

		wp_set_current_user( $this->user_own_post );

		$post = $this->factory()->create_and_get_default_post();

		$data = [
			'qazana_post_lock' => [
				'post_ID' => $post->ID,
			],
		];

		/** This filter is documented in wp-admin/includes/ajax-actions.php */
        $response = apply_filters( 'heartbeat_received', [], $data, '' );

		// Switch to other user
		wp_set_current_user( $this->user_editor );

		$this->assertEquals( $this->user_own_post, wp_check_post_lock( $post->ID ) );

		/** This filter is documented in wp-admin/includes/ajax-actions.php */
		$response = apply_filters( 'heartbeat_received', [], $data, '' );

		$this->assertArrayHasKey( 'locked_user', $response );
	}
}
