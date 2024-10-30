<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Tooltip;

use Saltus\WP\Framework\Infrastructure\Service\{
	Actionable,
	Service,
	Conditional
};

/**
 */
class TooltipFeature implements Service, Conditional, Actionable {

	private static $instance;

	/**
	 * Instantiate this Service object.
	 *
	 */
	public function __construct( ...$dependencies ) {
		self::$instance = new TooltipCore( ...$dependencies );
	}

	/**
	 * Check whether the conditional service is currently needed.
	 *
	 * @return bool Whether the conditional service is needed.
	 */
	public static function is_needed(): bool {

		/*
		 * This service loads in most screens:
		 * - admin: in the edit screen
		 * - ajax:  while updating menu order
		 * - front: during pre_get_posts, etc
		 */
		return true;
	}

	public function add_action() {
		self::$instance->register();
	}

	public function priority() {
		return 50;
	}
	public function filter() {
		return 'plugins_loaded';
	}
}
