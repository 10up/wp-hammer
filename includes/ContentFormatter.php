<?php
namespace WP_CLI\Hammer;

use WP_CLI;

class ContentFormatter {

	protected $dry_run;
	protected $formatters;

	function __construct( $formatters, $dry_run ) {
		$this->dry_run = $dry_run;
		$this->parse_formatters( $formatters );
	}

	/**
	 * @param $formatters Formatters as supplied on command line, parsed and processed via add_formatter
	 */
	function parse_formatters( $formatters ) {
		$this->formatters = array();
		foreach( $formatters as $formatter ) {
			$options = explode( '.', $formatter, 2 );
			// Check we have table.column=type set
			if ( 2 === count( $options ) ) {
				$table = $options[0];
				$generator_options = explode( '=', $options[1] );
				$column = $generator_options[0];
				$generator = isset( $generator_options[1] ) ? $generator_options[1] : null;
				$this->add_formatter( $table, $column, $generator );
			}
		}
	}

	/**
	 * @param $table e.g. users (for wp_users, prefix automatically substituted)
	 * @param $column e.g. user_email (for users.user_email)
	 * @param $generator which method of content generation is supported for this field
	 */
	function add_formatter( $table, $column, $generator ) {
		$formatters = is_array( $this->formatters ) ? $this->formatters : array();
		$formatters[ $table ] = isset( $formatters[ $table ] ) && is_array( $formatters[ $table ] ) ? $formatters[ $table ] : array();
		$formatters[ $table ][ $column ] = $generator;
		$this->formatters = $formatters;
	}

	/**
	 * Process all generator actions.
	 */
	function run() {
		/**
		 * Any setup we want to do before running the formatters.
		 */
		do_action( 'wp_hammer_before_run_formatter' );
		WP_CLI::line( "Running content formatters" );
		foreach ( $this->formatters as $table => $formatters )     {
			if ( $this->dry_run ) {
				WP_CLI::line( "Dry run formatter $table" );
			} else {
				WP_CLI::line( "Running formatters for table: $table" );
				/**
				 * Any formatter actions to run for the table type, use this to specify the functionality for a custom table.
				 */
				do_action( 'wp_hammer_run_formatter_' . $table, $this->formatters );
				$columns = array_keys( $formatters );
				foreach ( $columns as $column ) {
					/**
					 *
					 * Any formatter action just for a specific column, won't necessary override default functionality
					 * since that's specified via the table action.
					 *
					 */
					do_action( 'wp_hammer_run_formatter_' . $table . '_' . $column , $formatters[ $column ] );
				}
			}

		}
	}
}



