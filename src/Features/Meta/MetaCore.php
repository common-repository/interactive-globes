<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Meta;

use Saltus\WP\Framework\Infrastructure\Container\Invalid;
use Saltus\WP\Framework\Infrastructure\Container\ServiceContainer;
use Saltus\WP\Framework\Infrastructure\Plugin\Registerable;

/**
 * The Meta class
 */
class MetaCore implements Registerable {

	private $services;

	public function __construct( ...$dependencies ) {

		if ( empty( $dependencies[1] ) ) {
			throw Invalid::from( 'Services' );
		}
		if ( ! $dependencies[1] instanceof ServiceContainer ) {
			throw Invalid::from( $dependencies[1] );
		}

		$this->services = $dependencies[1];
	}

	/**
	 * Register Shortcode
	 */
	public function register() {

		try {
			$assets = $this->services->get( 'assets' )->create();
			$assets->load_admin_styles( '/assets/css/features/meta/meta-admin.css' );
		} catch ( \Exception $exception ) {

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions
				error_log( 'Failed to load styles' );
			}
		}

		add_action( 'add_meta_boxes', [ $this, 'meta_box_shortcode' ] );

		add_action( 'rest_api_init', [ $this, 'custom_endpoints' ] );

		add_action( 'add_meta_boxes', [ $this, 'remove_custom_meta_form' ] );
	}

	public function remove_custom_meta_form() {

		$post_types = [ 'iglobe' ];
		/**
		 * Filters the default list of post types to remove the postcustom meta box .
		 *
		 * @param string[]  $post_types An array of CPTs names.
		 */
		$post_types = apply_filters( 'itt_globes/meta/remove_meta_box_post_types', $post_types );
		foreach ( $post_types as $post_type ) {
			remove_meta_box( 'postcustom', $post_type, 'normal' );
		}
	}

	// Add custom endpoint with ID parameter and an additional segment
	public function custom_endpoints() {

		register_rest_route(
			'ittglobes/v1',
			'/globe/(?<id>\d+)/(?<post_type>\w+)/list',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'rest_globe_cpt_list' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	public function rest_globe_cpt_list( $data ) {
		// Retrieve ID from request
		$parent_id = $data->get_param( 'id' );
		$post_type = $data->get_param( 'post_type' );
		if ( ! $parent_id || ! $post_type ) {
			return rest_ensure_response( '' );
		}

		$list = self::fill_cpt_posts( $parent_id, $post_type );

		return rest_ensure_response( $list );
	}

	/**
	 * @param int $globe id The parent globe id
	 * @return \WP_Query
	 */
	private static function get_globe_items( int $parent_id, string $cpt ) {
		$args = [
			'post_type'      => $cpt,
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'globe_id',
					'value'   => $parent_id,
					'compare' => '=',
					'type'    => 'NUMERIC',
				),
			),
		];

		return new \WP_Query( $args );
	}

	// Register a custom meta box
	public function meta_box_shortcode() {
		add_meta_box(
			'meta_box_shortcode',
			'Shortcode',
			array( $this, 'render_custom_data_meta_box' ),
			'iglobe',
			'side',
			'high'
		);
	}

	public function render_custom_data_meta_box() {
		global $post;
		printf( '<div class="meta_box_shortcode-code_container"><span id="meta_box_shortcode-code">[display-globe id="%1$s"]</span> <button id="ittglobe-copy-button" type="button" class="dashicons dashicons-admin-page" title="Copy to clipboard"></button></div>', (int) esc_attr( $post->ID ) );
	}


	/**
	 * Mandatory field passed in $args:
	 * - cpt : the cpt name for the association to the main cpt
	 */
	public static function cpt_manager( $args ) {
		if ( empty( $args['cpt'] ) ) {
			return;
		}

		$cpt = sanitize_text_field( $args['cpt'] );

		$parent_id = null;
		// check if submission
		if ( ! empty( $_POST['post_ID'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$parent_id = (int) $_POST['post_ID'];
		}

		// then check url.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $parent_id && ! empty( $_GET['post'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$parent_id = (int) $_GET['post'];
		}
		if ( ! $parent_id ) {
			global $post_id;
			$parent_id = (int) $post_id;
		}

		if ( ! $parent_id ) {
			global $post;
			$parent_id = (int) esc_attr( $post->ID );
		}

		$html = sprintf(
			'<div id="fill_%1$s">%2$s</div>',
			esc_attr( $cpt ),
			self::fill_cpt_posts( $parent_id, $cpt )
		);
		$button_label = __( 'Add new', 'interactive-globes' );
		if ( ! empty( $args['button_label'] ) ) {
			$button_label = $args['button_label'];
		}

		// get admin url and remove query param from tab
		$admin_url = get_admin_url();
		$admin_url = explode( '?', $admin_url );
		$admin_url = $admin_url[0];

		$data = '';

		if ( ! empty( $args['data'] ) && is_array( $args['data'] ) ) {
			foreach ( $args['data'] as $datakey ) {
				$data .= ' data-' . $datakey . '=""';
			}
		}

		$html .= sprintf(
			'<div id="%1$s_modal" class="overlay">

				<div class="overlay-content">
					<iframe style="display:none;" scrolling="yes" width="100%%" height="100%%" src=""></iframe>
					<div class="overlay-content-loading">Loading...</div>
				</div>
				<a
					class="closebtn js-modal-close"
					data-post_type="%1$s"
					>&times;</a>

				<div class="overlay-controls">
					<a
						data-post_type="%1$s"
						data-parent_id="%2$s"
						class="button button-primary js-modal-save">Save</a>
					<a
						class="button js-modal-close"
						data-post_type="%1$s"
						>Cancel</a>
				</div>
			</div>',
			$cpt,
			$parent_id
		);

		// add new button
		$html .= sprintf(
			'<a
				class="button button-primary js-modal-add-new-cpt"
				data-parent_id="%1$s"
				data-post_type="%2$s"
				%3$s
				>%4$s</a>',
			$parent_id, // 1
			$cpt,       // 2
			$data,      // 3
			$button_label // 4
		);
		// js & styles
		$html .= '
			<script>
				document.addEventListener("DOMContentLoaded", function() {
					if (typeof init_cpt_events === "function" ) {
						// The function exists
						init_cpt_events( "' . $cpt . '" );
					}
				});
			</script>
			<style>
				.overlay {
					height: 100%;
					width: 100%;
					display: none;
					position: fixed;
					z-index: 9991;
					top: 0;
					left: 0;
					background-color: rgb(0,0,0);
					background-color: rgba(0,0,0, 0.9);
					transition:0.9s;
				}

				.overlay-content {
					position: relative;
					width: 80%;
					height: 80%;
					text-align: center;
					margin:5vh auto 0 auto;
					border-radius:5px;
				}

				.overlay-controls {
					width: 80%;
					height: 60px;
					background:#f9f9f9;
					text-align: right;
					margin:0 auto;
					border-top:1px solid #ccc;
					box-shadow: 0px -4px 5px -3px rgba(0,0,0,0.75);
				}

				.overlay a.button {
					font-size:1.2em;
					margin:10px 10px 0 0;
				}

				.overlay a.closebtn {
					position: absolute;
					top: 20px;
					right: 45px;
					font-size: 60px;
					padding: 8px;
					text-decoration: none;
					color: #818181;
					display: block;
					transition: 0.3s;
					cursor:pointer;
				}

				.overlay a.closebtn:hover, .overlay a.closebtn:focus {
					color: #f1f1f1;
				}

				.overlay .overlay-content-loading {
					position: absolute;
					top: 50%;
					left: 50%;
					transform: translate(-50%, -50%);
				}

				</style>';

		$html = sprintf(
			'<div id="block_%1$s">%2$s</div>',
			$cpt,
			$html
		);
		echo $html;
	}

	private static function fill_cpt_posts( int $parent_id, string $cpt ) {
		$content = '';
		$cpt     = sanitize_text_field( $cpt );

		$iglobe_cpt_query = self::get_globe_items( $parent_id, $cpt );
		if ( ! $iglobe_cpt_query->have_posts() ) {
			return $content;
		}

		$content = '<table class="itt_globes_cpt_manager_table">
		<thead>
			<tr>
				<th>' . __( 'Title', 'interactive-globes' ) . '</th>
				<th class="buttons">' . __( 'Actions', 'interactive-globes' ) . '</th>
			</tr>
		</thead>
		<tbody>';

		foreach ( $iglobe_cpt_query->posts as $cpt_post ) {
			if ( ! is_a( $cpt_post, 'WP_POST' ) ) {
				continue;
			}
			$cpt_id      = $cpt_post->ID;
			$cpt_post_title = $cpt_post->post_title;

			$content .= sprintf(
				'<tr data-entry-id="%3$s">
					<td>%1$s</td>
					<td class="buttons">
						<a
							class="button manage_cpt js-modal-edit-cpt"
							data-cpt_post_id="%3$s"
							data-post_type="%5$s"
							data-parent_id="%6$s"
							href="#edit-%3$s"
							>Edit</a>
							<a
							class="button manage_cpt_post js-remove-cpt"
							data-cpt_post_id="%3$s"
							data-parent_id="%6$s"
							data-post_type="%5$s"
							href="#delete-%3$s"
							>Delete</a>
					</div>

				</div>',
				$cpt_post_title,
				get_edit_post_link( $cpt_id ),
				$cpt_id, // 3
				null, // 4
				$cpt_post->post_type, // 5
				$parent_id
			);
		}

		$content .= '</tbody>
		</table>';

		return $content;
	}
}
