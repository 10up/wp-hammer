<?php

namespace WP_CLI\Hammer\Formatters\Comments;
use WP_CLI\Iterators\Query;

/**
 * Action to be run for manipulating comments table, all formatters are passed as a parameter.
 * @param array $formatters All formatters for all tables
 */
function comments( $formatters ) {
	global $wpdb;
	$comments_query = "SELECT * FROM $wpdb->comments";
	$comments = new Query( $comments_query );

	while ( $comments->valid() ) {
		$original_comment = (array) $comments->current();
		$modified_comment = (array) $comments->current();

		foreach ( $formatters['comments'] as $column => $formatter ) {
			$modified_comment = apply_filters( 'wp_hammer_run_formatter_filter_comments_' . $column, $modified_comment, $formatter );
		}

		$modified = array_diff( $modified_comment, $original_comment );

		if ( count( $modified ) ) {
			\WP_CLI::line( "Making change to comment {$original_comment[ 'comment_ID' ]} to contain " . wp_json_encode( $modified ) );
			$wpdb->update(
				"$wpdb->comments",
				$modified,
				array( 'comment_ID' => $original_comment['comment_ID'] ),
				'%s',
				'%d'
			);
		}
		$comments->next();
	}
}

/**
 * @param array  $comment   Represents a row in wp_comments table
 * @param string $formatter String for email, with other columns substituted with __COLUMN_NAME__
 *
 * @return mixed
 */
function comment_author_email( $comment, $formatter ) {
	preg_match_all( '/__([a-zA-Z0-9-_]*)__/', $formatter, $matches );
	if ( is_array( $matches ) && 2 === count( $matches ) ) {
		foreach ( $matches[1] as $match ) {
			if ( isset( $comment[ $match ] ) ) {
				$formatter = str_replace( "__{$match}__", $comment[ $match ], $formatter );
			}
		}
		$comment['comment_author_email'] = $formatter;
	}
	return $comment;
}

add_filter( 'wp_hammer_run_formatter_filter_comments_comment_author_email', __NAMESPACE__ . '\comment_author_email', null, 2 );
add_action( 'wp_hammer_run_formatter_comments', __NAMESPACE__ . '\comments' );
