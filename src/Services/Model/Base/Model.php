<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

/**
 * Manage Assets like scripts and styles.
 */
class Model {

	public static function get() {
		return [
			'active'       => true,
			'type'         => 'cpt',
			'name'         => 'iglobe',
			'supports'     => [
				'title',
			],
			'features'     => [],
			'labels'       => [],
			'options'      => [],
			'block_editor' => false,
			'meta'         => [],
		];
	}
}
