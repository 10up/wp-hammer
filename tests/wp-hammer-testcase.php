<?php

/**
 * Base unit test class for Co-Authors Plus
 */

class WP_HammerTestCase extends WP_UnitTestCase {

	protected $suppress = false;

	public function setUp() {
		global $wpdb;
		parent::setUp();
		$this->suppress = $wpdb->suppress_errors();

		$_SERVER['REMOTE_ADDR'] = '';

		$this->author1 = $this->factory->user->create( array( 'role' => 'author', 'user_login' => 'author1' ) );
		$this->editor1 = $this->factory->user->create( array( 'role' => 'editor', 'user_login' => 'editor2' ) );

		$post = array(
			'post_author'     => $this->author1,
			'post_status'     => 'publish',
			'post_content'    => rand_str(),
			'post_title'      => rand_str(),
			'post_type'       => 'post',
		);

		$this->author1_post1 = wp_insert_post( $post );

		$post = array(
			'post_author'     => $this->author1,
			'post_status'     => 'publish',
			'post_content'    => rand_str(),
			'post_title'      => rand_str(),
			'post_type'       => 'post',
		);

		$this->author1_post2 = wp_insert_post( $post );

		$page = array(
			'post_author'     => $this->author1,
			'post_status'     => 'publish',
			'post_content'    => rand_str(),
			'post_title'      => rand_str(),
			'post_type'       => 'page',
		);

		$this->author1_page1 = wp_insert_post( $page );

		$page = array(
			'post_author'     => $this->author1,
			'post_status'     => 'publish',
			'post_content'    => rand_str(),
			'post_title'      => rand_str(),
			'post_type'       => 'page',
		);

		$this->author1_page2 = wp_insert_post( $page );

		$comment = array(
			'comment_post_ID'      => $this->author1_post1,
			'comment_author_email' => 'commenter_1@example.org',
		);

		$this->comment_1 = $this->factory()->comment->create( $comment );

		$comment = array(
			'comment_post_ID'      => $this->author1_post2,
			'comment_author_email' => 'commenter_2@example.org',
		);

		$this->comment_2 = $this->factory()->comment->create( $comment );

		$args = array(
				"-l",
				"users=5,posts=100.post_date",
				"-f",
				"posts.post_author=auto,users.user_pass=auto,users.user_email=ivan+__ID__@kruchkoff.com,posts.post_title=ipsum,comments.comment_author_email=ivan+__comment_ID__@kruchkoff.com",
		);
		$assoc_args = array();
		$this->settings = new WP_CLI\Hammer\Settings();
		$this->settings->parse_arguments( $args, $assoc_args );
	}

	public function tearDown() {
		global $wpdb;
		parent::tearDown();
		$wpdb->suppress_errors( $this->suppress );
	}
}
