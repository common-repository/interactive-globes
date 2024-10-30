<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Updater;

use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Project;
use Saltus\WP\Framework\Infrastructure\Container\Invalid;
use Saltus\WP\Framework\Infrastructure\Plugin\Registerable;

/**
 * The Meta class
 */
class UpdateCore implements Registerable {

	private $project;

	public function __construct( ...$dependencies ) {
		if ( empty( $dependencies[0] ) ) {
			throw Invalid::from( 'Project' );
		}
		if ( ! $dependencies[0] instanceof Project ) {
			throw Invalid::from( $dependencies[0] );
		}

		$this->project = $dependencies[0];
	}

	/**
	 * Register Shortcode
	 */
	public function register() {
		// add version flag to control migration/update
		// 'init' hook would not work, we hooked later as an alternative
		add_action( 'wp', [ $this, 'check_version_flag' ] );
	}

	public function check_version_flag() {

		$db_version      = get_option( 'ittglobe_version' );
		$current_version = $this->project->version; // grab from plugin directly

		if ( ! $db_version ) {
			// we need to migrate old data to new cpt
			$this->migrate_data_to_cpts();
			// set version
			update_option( 'ittglobe_version', $current_version );
			\flush_rewrite_rules();
			return;
		}

		// the version is on the database, meaning,
		// update cpts already ran, just update version
		if ( version_compare( $current_version, $db_version, '>' ) ) {
			update_option( 'ittglobe_version', $current_version );
		}
	}

	// loops through globes to get points and dot labels and adds them as cpt entries with the meta data
	private function migrate_data_to_cpts() {
		// Define the arguments for the query
		$args = array(
			'post_type'   => 'iglobe',  // Custom post type
			'post_status' => 'publish', // Only get published posts
			'numberposts' => -1,         // Retrieve all posts
		);

		// Get the posts
		$iglobe_posts = get_posts( $args );

		// Check if posts were found
		if ( empty( $iglobe_posts ) ) {
			return;
		}
		// Loop through each post
		foreach ( $iglobe_posts as $post ) {
			// Get the globe_info meta field
			$globe_info = get_post_meta( $post->ID, 'globe_info', true );

			// Check if globe_info exists
			if ( ! is_array( $globe_info ) ) {
				continue;
			}
			// Process points array
			if ( isset( $globe_info['points'] ) && is_array( $globe_info['points'] ) ) {
				foreach ( $globe_info['points'] as $point ) {
					if ( ! isset( $point['id'] ) ) {
						continue;
					}
					// Create a new post for the point
					$new_post_id = wp_insert_post(
						array(
							'post_title'  => sanitize_text_field( $point['id'] ),
							'post_type'   => 'itt_globe_point',
							'post_status' => 'publish',
						)
					);

					// Add each key in the points array as a meta field
					if ( $new_post_id ) {
						update_post_meta( $new_post_id, 'points_info', $point );
						// Store the parent globe ID
						update_post_meta( $new_post_id, 'globe_id', $post->ID );
					}
				}
			}

			// Process labels array
			if ( isset( $globe_info['labels'] ) && is_array( $globe_info['labels'] ) ) {
				foreach ( $globe_info['labels'] as $label ) {
					if ( ! isset( $label['id'] ) ) {
						continue;
					}
					// Create a new post for the label
					$new_post_id = wp_insert_post(
						array(
							'post_title'  => sanitize_text_field( $label['id'] ),
							'post_type'   => 'itt_globe_dotlabel',
							'post_status' => 'publish',
						)
					);

					// Add each key in the labels array as a meta field
					if ( $new_post_id ) {
						update_post_meta( $new_post_id, 'dotLabels_info', $label );
						// Store the parent globe ID
						update_post_meta( $new_post_id, 'globe_id', $post->ID );
					}
				}
			}
		}
	}
}
