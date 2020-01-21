<?php

class SettingsTest extends WP_HammerTestCase {
    protected $settings;

    /**
     * Check Dry Run
     */
    public function testDryRun() {
        $this->settings->parse_arguments( array(), array( 'dry-run' => true ) );
        $this->assertTrue( $this->settings->dry_run );
    }

    /**
     * Check Formatters
     */
    public function testFormats() {
        $this->assertEquals( 5, count( $this->settings->formats ), 'Valid Format Count' );
        $this->assertEquals( 'posts.post_author=auto', $this->settings->formats[0], 'Valid table.column=type parse');
        $this->assertEquals( 'users.user_email=ivan+__ID__@kruchkoff.com', $this->settings->formats[2], 'Valid users.user_email=email@format parse' );
        $this->assertEquals( 'comments.comment_author_email=ivan+__comment_ID__@kruchkoff.com', $this->settings->formats[4], 'Valid comments.comment_author_email=email@format parse' );
    }

    /**
     * Check Limits
     */
    public function testLimits() {
        $this->assertEquals( 2, count( $this->settings->limits ), 'Valid Limit Count' );
        $this->assertEquals( 'users=5', $this->settings->limits[0], 'Valid table=limit parse');
        $this->assertEquals( 'posts=100.post_date', $this->settings->limits[1], 'Valid table=limit.column parse');
    }
}
