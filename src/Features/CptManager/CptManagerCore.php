<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\CptManager;

use Saltus\WP\Framework\Infrastructure\Plugin\Registerable;

/**
 * The Meta class
 */
class CptManagerCore implements Registerable {

	public function __construct( ...$dependencies ) {}

	/**
	 * Register Shortcode
	 */
	public function register() {
		// single globe page filter
		add_filter( 'admin_head', [ $this, 'prepare_iframe' ] );
	}

	public function prepare_iframe() {

		// security check, maybe nonce?
		if ( ! isset( $_GET['iframe'] ) ) {
			return;
		}
		global $post;

		$parent_id = isset( $_GET['parent_id'] ) ? (int) $_GET['parent_id'] : null;
		if ( $parent_id === null ) {
			return;
		}
		echo '<style>
		body { overflow: scroll; background:#FFFFFF; }
		html.wp-toolbar { padding-top:0; }
		.csf-theme-light .csf-nav-inline {
			border-top: 1px solid #ccd0d4;
		}
		.geocoding-hide { display:none; }
		</style>';

		$this->render_fields( $post, $parent_id );
		do_action( 'admin_print_footer_scripts' );
		exit();
	}

	// create the fields
	public function render_fields( \WP_Post $post, $parent_id ) {

		global $wp_meta_boxes;
		$post_title        = $post->post_title;
		$post_id           = $post->ID;
		$post_type         = $post->post_type;
		$content           = '';
		$locations         = array( 'side', 'normal', 'advanced' );
		$priorities        = array( 'high', 'sorted', 'core', 'default', 'low' );
		$meta_box_content  = '';
		$cpt_meta_boxes    = [];

		// autofill from url parameter
		if( isset( $_GET['title'] ) ){
			$post_title = esc_attr( $_GET['title'] );
		}

		// cpt labels
		$cptobg            = get_post_type_object( $post->post_type );
		$title_label       = $cptobg->labels->add_new_item;
		$title_placeholder = __( 'Add title', 'interactive-globes' );

		$context   = 'normal';
		$priority  = 'high';
		// TODO iterate over locations and priorities
		if ( ! empty( $wp_meta_boxes[ $post_type ][ $context ][ $priority ] ) ) {
			$cpt_meta_boxes = $wp_meta_boxes[ $post_type ][ $context ][ $priority ];
		}
		foreach ( $cpt_meta_boxes as $name => $meta_box ) {
			if ( empty( $meta_box['callback'] ) ) {
				continue;
			}
			ob_start(); // start capturing output.
			call_user_func(
				$meta_box['callback'],
				$post_type,
				$meta_box
			);
			$meta_box_content .= ob_get_contents();
			ob_end_clean();
		}

		// encode meta boxes
		$cpt_meta_boxes_encoded = json_encode( $cpt_meta_boxes );
		$cpt_meta_boxes_encoded = htmlspecialchars( $cpt_meta_boxes_encoded, ENT_QUOTES, 'UTF-8' );

		$extra_values = [
			'title',
		];
		// repeat for extra values
		$cpt_extra_values_encoded = json_encode( $extra_values );
		$cpt_extra_values_encoded = htmlspecialchars( $cpt_extra_values_encoded, ENT_QUOTES, 'UTF-8' );

		$modal = sprintf(
			'<form id="cpt-post" name="cpt-post"
				action="#"
				data-parent_id="%6$s"
				data-refresh_lis="%6$s"
				>
				<div class="csf-field-text modal-title-field" style="margin:20px 0">
					<header class="csf-field csf-field-text" style="padding:0;">
						<div class="csf-title">
							<h4>%7$s</h4>
						</div>
						<div class="csf-fieldset">
							<input placeholder="%8$s" id="input-field" class="" type="text" name="title" value="%1$s">
						</div>
						<div class="clear"></div>
					</header>
				</div>
				%3$s
				<input type="hidden" name="cpt_meta_boxes" value="%4$s">
				<input type="hidden" name="extra_values" value="%5$s">
				<input type="hidden" name="relationship_parent_id" value="%6$s">
				<input type="hidden" name="cpt_post_id" value="%2$s">
				<input type="hidden" name="post_type" value="%9$s">
			</form>',
			$post_title,               // 1 post title
			$post_id,                  // 2 post id
			$meta_box_content,         // 3 meta box content
			$cpt_meta_boxes_encoded,   // 4 meta box list
			$cpt_extra_values_encoded, // 5 extra values list
			$parent_id,                // 6 relationship parent id
			$title_label,              // 7 title label
			$title_placeholder,        // 8 title placeholder
			$post_type                 // 9 post type
		);

		$content .= sprintf(
			'<div>
				%1$s
			</div>',
			$modal
		);

		$content = sprintf(
			'<div style="padding:20px; background:#FFFFFF;" id="iglobes_cpt_modals">
				%1$s
			</div>',
			$content
		);

		echo $content;
	}

	// get globe items
	public function get_globe_items( int $globe_id, string $cpt ) {
		$args = [
			'post_type'      => $cpt,
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'globe_id',
					'value'   => $globe_id,
					'compare' => '=',
					'type'    => 'NUMERIC',
				),
			),
		];

		return new \WP_Query( $args );
	}
}
