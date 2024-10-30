<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Meta;

use Saltus\WP\Framework\Infrastructure\Service\{
	Actionable,
	Service,
	Conditional
};

/**
 */
class MetaFeature implements Service, Conditional, Actionable {

	private static $instance;

	/**
	 * Instantiate this Service object.
	 *
	 */
	public function __construct( ...$dependencies ) {
		self::$instance = new MetaCore( ...$dependencies );
	}

	/**
	 * Check whether the conditional service is currently needed.
	 *
	 * @return bool Whether the conditional service is needed.
	 */
	public static function is_needed(): bool {
		return true; // is needed in REST too
	}
	public function add_action() {
		self::$instance->register();
	}
	public function priority() {
		return 100;
	}
}
