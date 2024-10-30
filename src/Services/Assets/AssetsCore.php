<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Assets;

use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Project;
use Saltus\WP\Framework\Infrastructure\Container\Invalid;

/**
 * Manage Assets like scripts and styles.
 */
class AssetsCore {

	private $dependencies;
	private $project;

	/**
	 * Suffix for filename
	 */
	public $suffix;

	/**
	 * Assets Directory
	 */
	public $dir;

	/**
	 * Plugin Root Directory
	 */
	public $root_file_path;

	/**
	 * Instantiate this Service object.
	 *
	 */
	public function __construct( ...$dependencies ) {
		$this->dependencies = $dependencies;

		if ( empty( $this->dependencies[0] ) ) {
			throw Invalid::from( 'Project' );
		}
		if ( ! $this->dependencies[0] instanceof Project ) {
			throw Invalid::from( $this->dependencies[0] );

		}
		$this->project = $this->dependencies[0];
		$this->dir     = WP_ENV === 'development' ? '' : 'dist';
		$this->suffix  = WP_ENV === 'development' ? '' : '.min';

		$this->root_file_path = $this->project->file_path;
	}

	/**
	 * Load admin assets.
	 *
	 */
	public function load_admin_styles( $src, $dependencies = [] ) {

		add_action(
			'admin_enqueue_scripts',
			function () use ( $src, $dependencies ) {

				global $typenow;
				global $pagenow;
				// only load on necessary pages
				if ( ( $typenow === 'iglobe' &&
						( $pagenow === 'post.php' || $pagenow === 'post-new.php' ) ) ||
						( $typenow === 'iglobe' && $pagenow === 'edit.php' ) // subpages
						) {
					$src  = $this->prepare_src( $src );
					$name = $this->register_fullpath_style( $src, $dependencies );
					wp_enqueue_style( $name );
				}
			}
		);
	}

	/**
	 * Prepare source URL for enqueuing assets
	 *
	 * @param string $src Path of the script relative to the assets directory.
	 *
	 * @return string
	 */
	private function prepare_src( $src ) {
		$src_path = dirname( $src );
		$src_name = pathinfo( $src, PATHINFO_FILENAME );
		$src_ext  = pathinfo( $src, PATHINFO_EXTENSION );

		$src_rel_path = "{$this->dir}{$src_path}/{$src_name}{$this->suffix}.{$src_ext}";
		return plugins_url( $src_rel_path, $this->project->file_path );
	}

	/**
	 * Prepare name for enqueuing asset
	 * Strips path from $src
	 *
	 * @param string $src Path of the script relative to the assets directory.
	 *
	 * @return string
	 */
	private function prepare_name( $src ) {
		$src_name = pathinfo( $src, PATHINFO_FILENAME );
		if ( WP_ENV !== 'development' ) {
			$src_name = str_replace( $this->suffix, '', $src_name );
		}
		$src_name = $this->project->name . '_' . $src_name;
		return $src_name;
	}

	/**
	 * @param string[] $dependencies
	 * An array of registered script handles this script depends on. Use the filename and it will automatically convert to the registered name. Follows the pattern: <project_name> + underscore + <filename>
	 *
	 * @return string[]
	 */
	private function prepare_dependencies( $dependencies ) {
		foreach ( $dependencies as $index => $dependency_name ) {
			$dep_src = $this->prepare_src( $dependency_name );
			$dependencies[ $index ] = $this->prepare_name( $dep_src );
		}
		return $dependencies;
	}

	/**
	 * Wrapper for any local style, skips name, version and media parameter
	 *
	 * @param string $src
	 * Path of the script relative to the assets directory.
	 *
	 * @param string[] $dependencies
	 * Optional. An array of registered script handles this script depends on. Use the filename and it will automatically convert to the registered name. Follows the pattern: <project_name> + underscore + <filename>
	 *
	 * @param bool $in_footer
	 * Optional. Whether to enqueue the script before instead of in the . Default 'false'.
	 *
	 * @return string The name used to register the asset
	 */
	public function register_style( $src = '', $dependencies = [] ) {
		$src = $this->prepare_src( $src );
		return $this->register_fullpath_style( $src, $dependencies );
	}

	/**
	 * Wrapper for any style, skips name and version parameter. Doesn't transform $src
	 *
	 * @param string $src Path or URL to the script.
	 *
	 * @param string[] $dependencies
	 * Optional. An array of registered script handles this script depends on. Use the filename and it will automatically convert to the registered name. Follows the pattern: <project_name> + underscore + <filename>
	 *
	 * @param bool $in_footer
	 * Optional. Whether to enqueue the script before instead of in the . Default 'false'.
	 *
	 * @return string The name used to register the asset
	 */
	public function register_fullpath_style( $src = '', $dependencies = [] ) {

		$name         = $this->prepare_name( $src );
		$dependencies = $this->prepare_dependencies( $dependencies );
		wp_register_style(
			$name,
			$src,
			$dependencies,
			$this->project->version
		);
		return $name;
	}

	/**
	 * Wrapper for any local script, skips name and version parameter
	 *
	 * @param string $src
	 * Path of the script relative to the assets directory.
	 *
	 * @param string[] $dependencies
	 * Optional. An array of registered script handles this script depends on. Use the filename and it will automatically convert to the registered name. Follows the pattern: <project_name> + underscore + <filename>
	 *
	 * @param bool $in_footer
	 * Optional. Whether to enqueue the script before instead of in the . Default 'false'.
	 *
	 * @return string The name used to register the asset
	 */
	public function register_script( $src = '', $dependencies = [], $in_footer = \false ) {
		$src = $this->prepare_src( $src );
		return $this->register_fullpath_script( $src, $dependencies, $in_footer );
	}

	/**
	 * Wrapper for any script, skips name and version parameter. Doesn't transform $src
	 *
	 * @param string $src Path or URL to the script.
	 *
	 * @param string[] $dependencies
	 * Optional. An array of registered script handles this script depends on. Use the filename and it will automatically convert to the registered name. Follows the pattern: <project_name> + underscore + <filename>
	 *
	 * @param bool $in_footer
	 * Optional. Whether to enqueue the script before instead of in the . Default 'false'.
	 *
	 * @return string The name used to register the asset
	 */
	public function register_fullpath_script( $src = '', $dependencies = [], $in_footer = \false ) {

		$name         = $this->prepare_name( $src );
		$dependencies = $this->prepare_dependencies( $dependencies );
		wp_register_script(
			$name,
			$src,
			$dependencies,
			$this->project->version,
			$in_footer
		);
		return $name;
	}
}
