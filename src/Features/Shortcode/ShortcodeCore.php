<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Shortcode;

use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Project;
use Saltus\WP\Framework\Infrastructure\Container\Invalid;
use Saltus\WP\Framework\Infrastructure\Container\ServiceContainer;
use Saltus\WP\Framework\Infrastructure\Plugin\Registerable;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Assets\HasAssets;

/**
 * The Shortcode class
 */
class ShortcodeCore implements Registerable {

	private $project;

	private $services;
	/**
	 * Options for Globe CPT
	 */
	private $options;

	/**
	 * Meta fields
	 */
	private $meta;

	public function __construct( ...$dependencies ) {

		if ( empty( $dependencies[0] ) ) {
			throw Invalid::from( 'Project' );
		}
		if ( ! $dependencies[0] instanceof Project ) {
			throw Invalid::from( $dependencies[0] );
		}

		if ( empty( $dependencies[1] ) ) {
			throw Invalid::from( 'ShortcodeCore' );
		}
		if ( ! $dependencies[1] instanceof ServiceContainer ) {
			throw Invalid::from( $dependencies[1] );
		}

		$this->project  = $dependencies[0];
		$this->services = $dependencies[1];
	}
	/**
	 * Register Shortcode
	 */
	public function register() {
		add_shortcode( 'display-globe', array( $this, 'render_shortcode' ) );

		// Hook the function to admin_footer
		add_action( 'admin_footer', [ $this, 'render_shortcode_in_cpt_footer' ] );
	}

	/**
	 * Render shortcode
	 */
	public function render_shortcode( $atts ) {

		// normalize attribute keys, lowercase
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// override default attributes with user attributes
		$globe_atts = shortcode_atts(
			array(
				'id' => null,
			),
			$atts
		);

		if ( ! isset( $globe_atts['id'] ) ) {

			if ( ! is_admin() ) {
				return;
			}
			$globe_atts['id'] = null;
		}

		// if it's null and we're not in the admin, then it's not preview and null is invalid
		if ( $globe_atts['id'] === null && ! is_admin() ) {
			return;
		}

		// if still null and in admin screen
		if ( $globe_atts['id'] === null && is_admin() ) {

			// check if submission
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! empty( $_POST['post_ID'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$globe_atts['id'] = (int) $_POST['post_ID'];
			}

			// then check url.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $_GET['post'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$globe_atts['id'] = (int) $_GET['post'];
			}
		}

		// make sure its sanitzed and int
		$globe_atts['id'] = sanitize_key( $globe_atts['id'] );
		$globe_atts['id'] = (int) $globe_atts['id'];

		if ( $globe_atts['id'] !== 0 ) {
			$id_post_type = get_post_type( $globe_atts['id'] );
			if ( $id_post_type !== 'iglobe' ) {
				return;
			}
		}

		$html = $this->render( $globe_atts, $this );
		return $html;
	}

	/**
	 * Render html for the map and enqueue necessary assets
	 *
	 * @param array $atts Shortcode attributes
	 * @param Core $core  The plugin core
	 *
	 * @return string html code for the map container
	 */
	private function render( array $atts ) {

		try {
			$assets = $this->services->get( 'assets' )->create();
		} catch ( \Exception $exception ) {

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions
				error_log( 'Failed to load styles' );
			}
		}

		$id   = absint( $atts['id'] );
		$data = [
			'assetsUrl' => plugins_url( $assets->dir . '/assets/', $this->project->file_path ),
			'imagesUrl' => plugins_url( $assets->dir . '/assets/imgs/', $this->project->file_path ),
		];
		if ( is_admin() ) {
			$data['isAdmin'] = true;
		}

		$name = $assets->register_script(
			'/assets/js/public/globe.js',
			[ '/assets/js/vendor/public/globe.gl.js' ],
			true
		);
		wp_enqueue_script( $name );
		wp_localize_script( $name, 'ittGlobeData', $data );

		// globe pro
		if ( defined( 'SALTUS_PLAN' ) && SALTUS_PLAN === 'pro' ) {
			$name = $assets->register_script(
				'/assets/js/public/globe-pro.js',
				[ '/assets/js/public/globe.js' ],
				true
			);
			wp_enqueue_script( $name );
		}

		if ( is_admin() ) {
			$name = $assets->register_script(
				'/assets/js/admin/hoist-globe.js',
				[
					'/assets/js/public/globe.js',
					'/assets/js/vendor/public/globe.gl.js',
				],
				true
			);
			wp_enqueue_script( $name );
		}

		// prepare meta
		$this->setup_meta( $atts );
		$this->meta = apply_filters( 'itt_globes/shortcode/post_setup_meta', $this->meta, $id );
		$namespace  = 'Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\ClickActions\\';

		$enabled_click_actions = $this->meta['enabled_click_actions'];
		foreach ( $enabled_click_actions as $click_action ) {
			$click_action_classname = $namespace . $click_action;
			if ( ! class_exists( $click_action_classname ) ) {
				continue;
			}
			$click_action_class = new $click_action_classname();
			if ( $click_action_class instanceof Registerable ) {
				$click_action_class->register();
			}
			if ( $click_action_class instanceof HasAssets ) {
				$click_action_class->register_assets( $assets );
			}
		}
		$container_class = 'itt_globe_wrapper';
		$container_style = '';

		// padding top / aspect ratio
		$padding_top        = isset( $this->meta['paddingTop'] ) ? floatval( $this->meta['paddingTop'] ) : '56.25';
		$padding_top_mobile = isset( $this->meta['paddingTopMobile'] ) ? floatval( $this->meta['paddingTopMobile'] ) : $padding_top;
		$max_width          = isset( $this->meta['maxWidth'] ) && '' !== $this->meta['maxWidth'] && '0' !== $this->meta['maxWidth'] ? floatval( $this->meta['maxWidth'] ) : '';

		// max Width
		if ( ! empty( $max_width ) ) {
			$container_style = sprintf( 'max-width:%spx;', intval( $max_width ) );
		}

		/**
		 * Filters the class for the container that wraps the globe block
		 *
		 * @param string $container_class The class for the globe container
		 * @param int $id                 The globe id of the current block
		 */
		$container_class = apply_filters( 'itt_globes/render/container_class', $container_class, $id );

		/**
		 * Filters the style for the container that wraps the globe block
		 *
		 * @param string $container_style The style for the globe container
		 * @param int $id                 The globe id of the current block
		 */
		$container_style = apply_filters( 'itt_globes/render/container_style', $container_style, $id );

		$before = '';
		$after  = '';
		/**
		 * Filters the content added before
		 *
		 * @param string $before Content added before the globe block inside the container
		 * @param int $id        The globe id of the current block
		 */
		$before = apply_filters( 'itt_globes/render/content_before', $before, $id );

		/**
		 * Filters the content added after
		 *
		 * @param string $after Content added after the globe block inside the container
		 * @param int $id       The globe id of the current block
		 */
		$after = apply_filters( 'itt_globes/render/content_after', $after, $id );

		$html_globe = sprintf(
			'<div class="itt_globe_render itt_globe js-itt-globe-render" id="itt_globe_%1$s" data-globe_id="%1$s" data-globe_meta="%2$s"></div>',
			$id,
			htmlspecialchars( wp_json_encode( $this->meta ), ENT_QUOTES ) // 2
		);
		$html_block = sprintf(
			'<div class="itt_globe_container"><div class="itt_globe_aspect_ratio" style="padding-top: %1$s" data-padding-top="%2$s" data-padding-top-mobile="%3$s">
				%4$s
			</div></div>',
			$padding_top . '%',  // 1
			$padding_top,        // 2
			$padding_top_mobile, // 3
			$html_globe          // 4
		);

		$html = sprintf(
			'<div id="itt_globe_wrapper_%1$s" class="%2$s" style="%3$s">
				%4$s
				%5$s
				%6$s
			</div>',
			$id,              // 1
			$container_class, // 2
			$container_style, // 3
			$before,          // 4
			$html_block,      // 5
			$after            // 6
		);

		// remove tabs maybe also remove line breaks in the future?
		$html = trim( preg_replace( '/\t+/', '', $html ) );

		if ( isset( $_GET['debug'] ) ) {
			$html .= sprintf(
				'<pre>%s</pre>',
				wp_json_encode( $this->meta, JSON_PRETTY_PRINT )
			);
		}

		return $html;
	}


	/**
	 * Render shortcode in footer to be hoisted
	 *
	 * @return void
	 */
	public function render_shortcode_in_cpt_footer() {

		global $pagenow;
		// Check if we're on the edit page for a specific CPT
		if ( get_post_type() === 'iglobe' &&
			( $pagenow === 'post.php' && isset( $_GET['post'] ) ) ||
			( $pagenow === 'post-new.php' ) ) {
			// Render the shortcode output
			echo '<div id="custom-footer-shortcode">';
			echo do_shortcode( '[display-globe]' );
			echo '</div>';
		}
	}

	/**
	 * Setup proper data needed to render the globe
	 *
	 * @param array $atts
	 * @return void
	 */
	private function setup_meta( $atts ) {

		$id   = absint( $atts['id'] );
		$meta = $this->get_meta( $id );

		/**
		 * Filters the meta retrieved before its added to the block
		 *
		 * @param string $meta The meta data that will be used by the shortcode to render the globe
		 * @param int $id      The globe id of the current block
		 */
		$meta       = apply_filters( 'itt_globes/meta/setup', $meta, $id );
		$meta['id'] = $id;
		$this->meta = $meta;
	}

	/**
	 * Get globe_info meta data
	 *
	 * @param int $id globe id
	 *
	 * @return array Globe meta data
	 */
	private function get_meta( int $id ) {

		if ( $id === 0 && is_admin() ) {
			return array(
				'emptyPreview' => true,
			);
		}

		$meta = get_post_meta( $id, 'globe_info', true );
		if ( empty( $meta ) || ! $meta ) {
			$meta = [];
		}

		return $meta;
	}
}
