<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

/**
 * Manage Assets like scripts and styles.
 */
class Options {

	public static function get( $settings ) {
		$capability = isset( $settings['capability'] ) ? $settings['capability'] : null;

		return [
			'public'             => false,
			'publicly_queryable' => true,
			'show_in_rest'       => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'can_export'         => true,
			'capability_type'    => $capability,
			'menu_icon'          => 'dashicons-admin-site-alt',
			'rewrite'            => [
				'slug'       => 'globe',
				'with_front' => true,
				'feeds'      => true,
				'pages'      => true,
			],
		];
	}
}
