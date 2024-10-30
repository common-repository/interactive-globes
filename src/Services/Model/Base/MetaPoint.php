<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Meta\MetaCore;
/**
 * common meta
 */
class MetaPoint {
	public static function merge( $meta, $settings ) {

		$options               = $settings['options'];
		$tooltip_editor        = $settings['tooltip_editor'];
		$action_content_editor = $settings['action_content_editor'];
		$coordinates_editor    = $settings['coordinates_editor'];
		$assets_url            = $settings['assets_url'];
		$actions_default       = $settings['actions_default'];

		// Default Point meta
		$meta['globe_info']['sections']['points'] = [

			'title'  => __( 'Points', 'interactive-globes' ),
			'icon'   => 'fa fa-circle fa-lg',
			'class'  => 'tab_itt_globe_point',
			'fields' => array(
				'points_info'   => array(
					'type'    => 'content',
					'content' => '<div class="itt_globe_admin_example"><div>' . __( 'Add <strong>cylinder point markers</strong> to the globe. <br>Click "Add New Point" below to start adding points.', 'interactive-globes' )
						. '<br> ' .
						/* translators: %s will be a link to an external website */
						sprintf( __( 'You can get the latitude and longitude for a specific location clicking it on the globe or from sites like %s.', 'interactive-globes' ), '<a href="https://www.latlong.net/" target="_blank">LatLong.net</a>' )
						. '<br> ' .
						__( 'Correct format: Latitude: 41.1579, Longitude: -8.6291<br> Wrong format: Latitude: <strike>41.1579° N</strike> Longitude: <strike>8.6291° W</strike>', 'interactive-globes' )
						. '</div><div>'
						. sprintf( '<img src="%1$s/assets/imgs/admin/points_example.png">', $assets_url )
						. '</div></div>',
				),
				'points'        => array(
					'type'         => 'group',
					'class'        => 'hidden',
					'button_title' => __( 'Add New Point', 'interactive-globes' ),
					'fields'       => array(
						'id'             => array(
							'type'       => 'text',
							'title'      => __( 'Title', 'interactive-globes' ),
							'attributes' => array(
								'class' => 'skip-preview',
							),
						),
						'coordinates'    => array(
							'type'   => $coordinates_editor,
							'title'  => __( 'Coordinates', 'interactive-globes' ),
							'fields' => array(
								'name'      => array(
									'type'  => 'text',
									'title' => __( 'Location', 'interactive-globes' ),
									'class' => 'geocoding geocoding-hide',
									'attributes' => array(
										'class' => 'geocoding-input',
									),
								),
								'latitude'  => array(
									'type'     => 'text',
									'title'    => __( 'Latitude', 'interactive-globes' ),
									'validate' => 'csf_validate_numeric',
									'default'  => isset( $_GET['latitude'] ) ? (float) $_GET['latitude'] : '',
								),
								'longitude' => array(
									'type'     => 'text',
									'title'    => __( 'Longitude', 'interactive-globes' ),
									'validate' => 'csf_validate_numeric',
									'default'  => isset( $_GET['longitude'] ) ? (float) $_GET['longitude'] : '',
								),
							),
						),

						'tooltipContent' => array(
							'type'  => $tooltip_editor,
							'title' => __( 'Tooltip Content', 'interactive-globes' ),
						),
						'content'        => array(
							'type'     => $action_content_editor,
							'title'    => __( 'Action Content', 'interactive-globes' ),
							'subtitle' => __( 'URL or content to trigger when marker is clicked.', 'interactive-globes' ) . '<br><span class="igm_select_marker_click_reminder">' . __( 'Don\'t forget to select a click action below.', 'interactive-globes' ) . '</span>',
						),

					),
				),
				'points_cpt' => array(
					'type'     => 'callback',
					'function' => [ MetaCore::class, 'cpt_manager' ],
					'args'     => [
						'cpt'          => 'itt_globe_point',
						'data'         => [ 'latitude', 'longitude' ],
						'button_label' => __( 'Add New Point', 'interactive-globes' )
					],
				),
				'pointDefaults' => array(
					'type'   => 'fieldset',
					'title'  => __( 'Default values', 'interactive-globes' ),
					'desc'   => '',
					'fields' => array(
						'action'   => array(
							'type'    => 'select',
							'title'   => __( 'Click Action', 'interactive-globes' ),
							'desc'    => '',
							'options' => $actions_default,
							'default' => 'none',
						),
						'radius'   => array(
							'type'    => 'spinner',
							'default' => 20,
							'step'    => 1,
							'title'   => __( 'Radius', 'interactive-globes' ),
						),
						'altitude' => array(
							'type'    => 'spinner',
							'default' => 1,
							'step'    => 1,
							'title'   => __( 'Altitude', 'interactive-globes' ),
						),
						'color'    => array(
							'type'    => 'color',
							'title'   => __( 'Fill Color', 'interactive-globes' ),
							'default' => isset( $options['defaultActiveColor'] ) ? $options['defaultActiveColor'] : '#99d8c9',
						),
						'hover'    => array(
							'type'    => 'color',
							'title'   => __( 'Hover Color', 'interactive-globes' ),
							'default' => isset( $options['defaultHoverColor'] ) ? $options['defaultHoverColor'] : '#2ca25f',
						),
					),
				),
			),
		];

		return $meta;
	}
}
