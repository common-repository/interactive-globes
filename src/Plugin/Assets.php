<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Plugin;

use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Project;

use Saltus\WP\Framework\Infrastructure\Container\ServiceContainer;

/**
 * Manage Assets like scripts and styles.
 */
class Assets {

	/**
	 * The plugin's instance.
	 *
	 * @var Project
	 */
	public $project;

	private $services;
	/**
	 * Globe meta
	 */
	public $meta;

	/**
	 * Suffix for filename
	 */
	private $suffix;

	/**
	 * Assets Directory
	 */
	private $dir;

	/**
	 * Define Assets
	 *
	 * @param Project $project This plugin's instance.
	 */
	public function __construct( Project $project, ServiceContainer $services ) {
		$this->project  = $project;
		$this->dir      = WP_ENV === 'development' ? '' : 'dist';
		$this->suffix   = WP_ENV === 'development' ? '' : '.min';
		$this->services = $services;
	}
	public function add_type_attribute( $tag, $handle, $src ) {
		// if not your script, do nothing and return original $tag
		if ( $this->project->name . '_globe' !== $handle &&
			$this->project->name . '_globe-pro' !== $handle &&
			$this->project->name . '_admin' !== $handle &&
			$this->project->name . '_admin-pro' !== $handle
			) {
			return $tag;
		}

		// change the script tag by adding type="module" and return it.
		$tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
		return $tag;
	}

	public function register() {

		add_filter( 'script_loader_tag', array( $this, 'add_type_attribute' ), 10, 3 );
		if ( is_admin() ) {
			$this->load_admin_assets();
			return;
		}
		$this->load_public_assets();
	}
	/**
	 * Load admin assets.
	 *
	 */
	public function load_admin_assets() {

		add_action( 'admin_enqueue_scripts', array( $this, 'load_globe_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_globe_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
	}

	/**
	 * Load globe assets
	 *
	 * @param array $meta
	 * @return void
	 */
	public function load_public_assets() {

		add_action( 'wp_enqueue_scripts', array( $this, 'load_globe_assets' ) );
	}

	public function load_globe_assets() {

		$this->load_globe_styles();
		$this->load_globe_scripts();
	}

	/**
	 * Load globe styles
	 *
	 */
	public function load_globe_styles() {

		try {
			$assets = $this->services->get( 'assets' )->create();
		} catch ( \Exception $exception ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions
				error_log( 'Failed to load assets' );
			}
		}
		if ( is_admin() && ! $this->is_globe_admin() ) {
			return;
		}

		$name = $assets->register_style(
			'/assets/css/public/itt-globes-style.css',
			[]
		);
		wp_enqueue_style( $name );

		wp_dequeue_style( 'csf-fa' );
		wp_dequeue_style( 'csf-fa5' );
		wp_dequeue_style( 'csf-fa5-v4-shims' );

		$name = $assets->register_style(
			'/assets/css/vendor/admin/fontawesome.css',
			[]
		);
		wp_enqueue_style( $name );
		$name = $assets->register_style(
			'/assets/css/vendor/admin/solid.css',
			[]
		);
		wp_enqueue_style( $name );
	}

	/**
	 * Load globe scripts
	 *
	 */
	public function load_globe_scripts() {
		if ( is_admin() && ! $this->is_view_editor() ) {
			return;
		}

		try {
			$assets = $this->services->get( 'assets' )->create();
		} catch ( \Exception $exception ) {

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions
				error_log( 'Failed to load styles' );
			}
		}
		$name = $assets->register_script(
			'/assets/js/vendor/public/globe.gl.js',
			[],
			true
		);
		wp_enqueue_script( $name );
	}

	/**
	 * Load admin styles
	 *
	 */
	public function load_admin_styles() {

		if ( ! $this->is_globe_admin() ) {
			return;
		}
		// might need styles across all admin, because of menu styles
		wp_register_style(
			$this->project->name . '_admin',
			plugins_url( "{$this->dir}/assets/css/admin/admin-style{$this->suffix}.css", $this->project->file_path ),
			false,
			$this->project->version
		);

		wp_enqueue_style( $this->project->name . '_admin' );
	}

	/**
	 * Load admin scripts
	 *
	 */
	public function load_admin_scripts() {

		if ( ! $this->is_view_editor() ) {
			return;
		}

		try {
			$assets = $this->services->get( 'assets' )->create();
		} catch ( \Exception $exception ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions
				error_log( 'Failed to load assets' );
			}
		}
		$name = $assets->register_script(
			'/assets/js/admin/admin.js',
			[
				'/assets/js/public/globe.js',
			],
			true
		);
		wp_enqueue_script( $name );

		// labels
		wp_localize_script(
			$name,
			'itt_admin_labels',
			[
				'newCenterSaved'   => __( 'New center saved', 'interactive-globes' ),
				'setInitialCenter' => __( 'Set initial center', 'interactive-globes' ),
			]
		);

		$admin_url = get_admin_url();
		$admin_url = explode( '?', $admin_url );
		$admin_url = $admin_url[0];

		wp_localize_script(
			$name,
			'itt_globe_data',
			[
				'admin_url' => $admin_url,
			]
		);
		wp_localize_script(
			$name,
			'itt_globe_rest',
			[
				'url'       => esc_url_raw( rest_url( 'wp/v2/' ) ),
				'nonce'     => wp_create_nonce( 'wp_rest' ),
				'namespace' => rest_url( '/ittglobes/v1/globe/' ),
				'list_part' => '/list',
			]
		);
		wp_localize_script(
			$name,
			'itt_globe_meta',
			[
				'relationship_key' => 'globe_id',
			]
		);

		$name = $assets->register_script(
			'/assets/js/admin/cpt-list-events.js',
			[],
			true
		);
		wp_enqueue_script( $name );

		// specific to cpt modal
		$name = $assets->register_script(
			'/assets/js/admin/cpt-modal-events.js',
			[],
			true
		);
		wp_enqueue_script( $name );

		// for pro only
		if ( defined( 'SALTUS_PLAN' ) && SALTUS_PLAN === 'pro' ) {

			$adminp = $assets->register_script(
				'/assets/js/admin/admin-pro.js',
				[
					'/assets/js/admin/admin.js',
					'/assets/js/public/globe.js',
				],
				true
			);
			wp_enqueue_script( $adminp );
		}
	}

	private function is_view_editor() {
		global $typenow;
		global $pagenow;
		if ( $typenow !== 'iglobe' || ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' ) ) {
			return false;
		}
		return true;
	}

	private function is_globe_admin() {

		global $typenow;
		if ( $typenow !== 'iglobe' ) {
			return false;
		}
		return true;
	}
}
