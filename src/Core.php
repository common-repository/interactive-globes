<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes;

use Saltus\WP\Framework\Infrastructure\Container\ServiceContainer;

use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Shortcode\ShortcodeFeature;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Shortcode\ShortcodeCore;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Meta\MetaFeature;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\CptManager\CptManagerFeature;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Sales\UpsellPageFeature;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Updater\UpdateFeature;
// services
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Assets\AssetsService;

/**
 * The core class, where logic is defined.
 */
class Core {

	public $project;
	public $framework;

	public $features_container;
	public $services_container;

	/**
	 * Setup the class variables
	 *
	 * @param $project Plugin file path
	 * @param Object $saltus    Saltus Framework
	 * @param ServiceContainer $container Container
	 */
	public function __construct( $project, $framework, ServiceContainer $f_container, ServiceContainer $s_container ) {

		$this->project            = $project;
		$this->framework          = $framework;
		$this->features_container = $f_container;
		$this->services_container = $s_container;
	}

	/**
	 * Start the logic for this plugins.
	 *
	 * Runs on 'plugins_loaded' which is pre- 'init' filter
	 */
	public function init() {

		$this->do_lifecylce();

		$this->set_locale();

		// main scripts and styles
		$this->set_assets();

		// load services
		$service_list = $this->get_services_classes();
		$this->register_services( $service_list );

		// 1- Loads features
		$feature_list = $this->get_features_classes();
		$this->register_features( $feature_list );

		// temp fix for admin
		if ( is_admin() ) {
			$this->register_shortcode();
		}
	}

	/**
	 * Register Shortcode
	 */
	public function register_shortcode() {
		$shortcode = new ShortcodeCore( $this->project, $this->services_container );
		$shortcode->register();
	}

	/**
	 * Register the individual features of this plugin.
	 *
	 * @throws Invalid If a service is not valid.
	 *
	 * @return void
	 */
	public function register_features( $features ) {

		// Bail early so we don't instantiate features twice.
		if ( count( $this->features_container ) > 0 ) {
			return;
		}
		// same dependency for all features for now
		//TODO by pcarvalho: allow each service their own dependencies
		$dependencies = [ $this->project, $this->services_container ];

		foreach ( $features as $id => $class ) {
			$this->features_container->register( $id, $class, $dependencies );
		}
	}
	/**
	 * Register the individual services of this plugin.
	 *
	 * @throws Invalid If a service is not valid.
	 *
	 * @return void
	 */
	public function register_services( $services ) {

		if ( count( $this->features_container ) > 0 ) {
			return;
		}

		$dependencies = [ $this->project ];

		foreach ( $services as $id => $class ) {
			$this->services_container->register( $id, $class, $dependencies );
		}
	}

	/**
	 * Get the list of features to register.
	 *
	 * @return array<string> Associative array of identifiers mapped to fully
	 *                       qualified class names.
	 */
	private function get_features_classes(): array {
		return [
			'CPTManager' => CptManagerFeature::class,
			'Meta'       => MetaFeature::class,
			'shortcode'  => ShortcodeFeature::class,
			'Updater'    => UpdateFeature::class,
			'Upsell'     => UpsellPageFeature::class,
		];
	}

	private function get_services_classes() {
		return [
			'assets' => AssetsService::class,
		];
	}

	/**
	 * Run plugin lifecycle actions
	 */
	protected function do_lifecylce() {
		$lifecycle = new Plugin\Lifecycle( $this->project );
		$lifecycle->run_lifecycle();
	}
	/**
	 * Load translations
	 */
	protected function set_locale() {
		$i18n = new Plugin\I18n( $this->project->name );
		$i18n->load_plugin_textdomain( dirname( $this->project->file_path ) );
	}

	/**
	 * Load assets
	 */
	protected function set_assets() {
		$assets = new Plugin\Assets( $this->project, $this->services_container );
		$assets->register();
	}
}
