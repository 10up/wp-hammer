<?php

namespace WP_CLI\Hammer\Pruners\Posts;
use WP_CLI\Iterators\Query;

/**
 * @param            $prune               How many posts to keep
 * @param bool|false $sort_type           How to determine which posts to keep
 * @param            $post_type_to_prune  Post type to prune
 */
function pruner( $limit, $sort_type = false, $post_type_to_prune = null ) {
	global $wpdb;
	$post_ids_by_type = array();
	$post_ids = array();
	\WP_CLI::line( "Fetching all posts, we will only keep $limit of them. This could take a while." );
	$sort_column = is_null( $sort_type ) || false === $sort_type ? 'post_modified' : preg_replace( '/[^A-Za-z0-9_]/', '', $sort_type ); // Alpha-numeric sort type to prevent injections.
	$post_type_query = is_null( $post_type_to_prune ) ? "" : " WHERE post_type = '" . sanitize_key( $post_type_to_prune ) . "' ";
	$posts_query = apply_filters( 'wp_hammer_prune_posts_query', "SELECT ID,post_type,post_parent FROM {$wpdb->prefix}posts" . $post_type_query . " order by $sort_column DESC" );
	$post_parent_relationships = array();
	$posts = new Query( $posts_query );
	$total_posts = 0;
	while ( $posts->valid() ) {
		$total_posts++;
		$post = $posts->current();
		$post_type = $post->post_type;
		$post_id = $post->ID;
		if ( ! in_array( $post_type, apply_filters( 'wp_hammer_prune_post_types_to_completely_remove', array( 'revision' ) ) ) ) {
			$post_ids_by_type[ $post_type ] = isset( $post_ids_by_type[ $post_type ] ) ? $post_ids_by_type[ $post_type ] : array();
			$post_ids_by_type[ $post_type ][] = $post_id;
		}
		// Store the post parent in a separate array so we can keep the post parent and not worry about re-assigning any parent IDs
		if ( $post->post_parent ) {
			$post_parent_relationships[ $post_id ] = $post->post_parent;
		}
		$post_ids[] = $post_id;
		$posts->next();
	}
	\WP_CLI::line( "Total number of posts is $total_posts posts" );

	// Post IDs to keep
	$keep_ids = array();
	$remaining = (int) min( $limit, $total_posts );
	while ( $remaining ) {
		// Iterate post_types, keeping one more of each until we've hit our limits
		foreach ( $post_ids_by_type as $type => &$ids ) {
			$keep_id = array_shift( $ids );
			if ( ! is_null( $keep_id ) && ! in_array( $keep_id, $keep_ids ) ) {
				$parent_id = isset( $post_parent_relationships[ $keep_id ] ) ? $post_parent_relationships[ $keep_id ] : false;
				// Keep the post parent if it exists, and then if we have space, keep the post itself.
				if ( $parent_id && ! in_array( $parent_id, $keep_ids ) ) {
					$keep_ids[] = $parent_id;
					$remaining--;
				}
				if ( 0 === $remaining ) {
					break;
				}

				if ( ! in_array( $keep_id, $keep_ids ) ) {
					$keep_ids[] = $keep_id;
					$remaining--;

					if ( 0 === $remaining ) {
						break;
					}
				}
			}
		}
	}

	\WP_CLI::line( "Deleting " . ( max( $total_posts - $limit, 0 ) ) . " posts" );
	$post_ids_to_delete = array_diff( $post_ids, $keep_ids );
	if ( 'query' === apply_filters( 'wp_hammer_prune_post_types_delete_method ', 'query' ) ) {
		$deleted_posts_count = delete_posts_sql( $post_ids_to_delete, $keep_ids, $post_type_to_prune );
	} else {
		$deleted_posts_count = delete_posts_wp( $post_ids_to_delete );
	}
	\WP_CLI::line( "Deleted $deleted_posts_count posts." );
}

/**
 * Delete posts using wp_delete_post, allows all of the WP hooks to run, but significantly slower than the SQL method.
 * @param $post_ids array of post_ids to delete
 *
 * @return int number of deleted posts
 */
function delete_posts_wp( $post_ids ) {
	$deleted_posts_count = 0;
	foreach( $post_ids as $post_id ) {
		$deleted_post = wp_delete_post( $post_id, true );
		if ( false !== $deleted_post ) {
			\WP_CLI::line( "Deleted post with ID $post_id, type {$deleted_post->post_type}, title {$deleted_post->post_title}" );
			$deleted_posts_count++;
		}
	}
	return $deleted_posts_count;
}

/**
 * Delete posts using raw SQL queries, doesn't run any WP hooks or handle any custom tables, but is significantly faster than the wp_delete_post method.
 * @param $post_ids array of post_ids to delete
 *
 * @return int number of deleted posts
 */
function delete_posts_sql( $post_ids_to_delete, $keep_ids, $post_type_to_prune ) {
	global $wpdb;
	$keep_ids_as_string = implode( ",", $keep_ids );
	$query = "delete {$wpdb->prefix}posts, {$wpdb->prefix}term_relationships, {$wpdb->prefix}postmeta from {$wpdb->prefix}posts left join {$wpdb->prefix}term_relationships ON ( {$wpdb->prefix}posts.ID = {$wpdb->prefix}term_relationships.object_id ) left join {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id ) where ( {$wpdb->prefix}posts.ID not in ( $keep_ids_as_string) )";
	if ( $post_type_to_prune ) {
		$query .= " AND {$wpdb->prefix}posts.post_type = '" . sanitize_key( $post_type_to_prune) . "'";
	}
	if ( $wpdb->query( $query) > 0 ) {
		\WP_CLI::line( "Posts deleted, progress = 100%");
	}
	return count( $post_ids_to_delete );
}

add_action( 'wp_hammer_run_prune_posts', __NAMESPACE__ . '\pruner', null, 3 );
