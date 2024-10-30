<?php
/**
 * Interactive Globes
 *
 * @wordpress-plugin
 * Plugin Name:       Interactive Globes
 * Plugin URI:        https://wpinteractiveglobes.com/
 * Description:       Create interactive geographic globes. Color full regions or create markers on specific locations that will have information on hover and can also have actions on click. This plugin uses the online globe.gl library to generate the maps.
 * Version:           1.3.1
 * Requires PHP:      7.0
 * Author:            Interactive Globes
 * Author URI:        https://wpinteractiveglobes.com/
 * Text Domain:       interactive-globes
 * Domain Path:       /languages
 */

namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes;

use Saltus\WP\Framework\Core as FrameworkCore;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Plugin\Freemius;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Prepare;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Project;
use Saltus\WP\Framework\Infrastructure\Container\ServiceContainer;

// If this file is called directly, quit.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only run plugin code if PHP version bigger than 7.0 for now
if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
	return;
}
/**
 * Configuration values
 */
if ( ! defined( 'WP_ENV' ) ) {
	define( 'WP_ENV', 'production' ); // possible: production, development
}
if ( ! defined( 'SALTUS_PLAN' ) ) {
	define( 'SALTUS_PLAN', 'free' ); // possible: pro, free, other?
}


if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( ! class_exists( FrameworkCore::class ) ||
	! class_exists( Project::class ) ||
	! class_exists( ServiceContainer::class ) ) {
		return;
}

global $ig_fs;

// Freemius logic
if ( isset( $ig_fs ) && $ig_fs ) {
	$ig_fs()->set_basename( true, __FILE__ );
	//return;
}

// before any model
$is_pro = false;

if ( ( defined( 'SALTUS_PLAN' ) && SALTUS_PLAN === 'pro' ) ) {
	$fs = new Freemius( __DIR__ );
	$fs->load();
	$fs->init();
	$is_pro = $fs->is_pro();
}

if ( $is_pro ) {
	$prepare = new Prepare();
	$prepare->register();
}

// loads the modals
$framework = new FrameworkCore( __DIR__ );
$framework->register();

$feature_container  = new ServiceContainer();
$services_container = new ServiceContainer();

$project = new Project( 'interactive-globes', '1.3.1', __FILE__ );

/**
 * Initialize plugin
 */
add_action(
	'plugins_loaded',
	function () use ( $project, $framework, $feature_container, $services_container, $is_pro ) {

		if ( $is_pro ) {
			$plugin = new CorePro( $project, $framework, $feature_container, $services_container );
		} else {
			$plugin = new Core( $project, $framework, $feature_container, $services_container );
		}

		$plugin->init();
	}
);

// run as early as possible
register_activation_hook( __FILE__, [ __NAMESPACE__ . '\\Plugin\\Lifecycle', 'activation_actions' ] );
