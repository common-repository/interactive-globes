<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Assets;

use Saltus\WP\Framework\Infrastructure\Service\{
	Service
};

/**
 * Manage Assets like scripts and styles.
 */
class AssetsService implements Service {

	private static $instance;

	/**
	 * Instantiate this Service object.
	 *
	 */
	public function __construct( ...$dependencies ) {
		self::$instance = new AssetsCore( ...$dependencies );
	}

	/**
	 * Create a new instance of the service provider
	 *
	 * @return object The new instance
	 */
	public static function make( $name, $project, $args ) {
		return self::$instance;
	}

	public static function create() {
		return self::$instance;
	}
}
