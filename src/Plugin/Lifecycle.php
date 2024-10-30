<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Plugin;

use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Project;

/**
 * Activation, update and deactivation cycle
 */
class Lifecycle {
	/**
	 * The plugin's instance.
	 *
	 * @var Project
	 */
	public $project;
	/**
	 * Define the domain.
	 *
	 * @param string $domain    Plugin domain.
	 */
	public function __construct( Project $project ) {
		$this->project = $project;
	}

	public function run_lifecycle() {
		$this->activation();
		$this->deactivation();
		$this->update();
	}
	/**
	 * Cycles.
	 *
	 **/
	private function activation() {
		// too late for register_activation_hook
	}
	private function deactivation() {
		register_deactivation_hook( $this->project->file_path, [ $this, 'deactivation_actions' ] );
	}
	private function update() {
		if ( ! method_exists( $this, 'update_actions' ) ) {
			return;
		}
		add_action( 'upgrader_process_complete', [ $this, 'update_actions' ] );
	}

	public static function activation_actions() {
		self::flush_permalink_rules();
	}
	public function deactivation_actions() {
		self::flush_permalink_rules();
	}
	public function update_actions() {
		self::flush_permalink_rules();
	}

	private static function flush_permalink_rules() {
		\flush_rewrite_rules();
	}
}
