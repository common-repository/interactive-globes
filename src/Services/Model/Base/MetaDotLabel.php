<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Meta\MetaCore;

/**
 * Dot label meta
 */
class MetaDotLabel {
	public static function merge( $meta, $settings ) {

		$options               = $settings['options'];
		$tooltip_editor        = $settings['tooltip_editor'];
		$action_content_editor = $settings['action_content_editor'];
		$coordinates_editor    = $settings['coordinates_editor'];
		$assets_url            = $settings['assets_url'];
		$actions_default       = $settings['actions_default'];

		// Default Point meta
		$meta['globe_info']['sections']['labelLegacy'] = [
			'title'  => __( 'Dot Labels', 'interactive-globes' ),
			'icon'   => 'fa fa-font fa-lg',
			'class'  => 'labels_tab',
			'fields' => array(
				'labels_info'   => array(
					'type'    => 'content',
					'content' => '<div class="itt_globe_admin_example"><div>' . __( 'Add <strong>text labels</strong> with dots to the globe. <br>Click "Add New Label" below to start adding labels.', 'interactive-globes' )
					. '<br> ' .
					/* translators: %s will be a link to an external website */
					sprintf( __( 'You can get the latitude and longitude for a specific location clicking it on the globe or from sites like %s.', 'interactive-globes' ), '<a href="https://www.latlong.net/" target="_blank">LatLong.net</a>' )
					. '<br> ' .
					__( 'Correct format: Latitude: 41.1579, Longitude: -8.6291<br> Wrong format: Latitude: <strike>41.1579° N</strike> Longitude: <strike>8.6291° W</strike>', 'interactive-globes' )
					. '</div><div>'
					. sprintf( '<img src="%1$s/assets/imgs/admin/labels_example.png">', $assets_url )
					. '</div></div>',
				),
				'labels'        => array(
					'type'         => 'group',
					'class'        => 'hidden',
					'button_title' => __( 'Add New Label', 'interactive-globes' ),
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
									'type'       => 'text',
									'title'      => __( 'Location', 'interactive-globes' ),
									'class'      => 'geocoding geocoding-hide',
									'attributes' => array(
										'class' => 'geocoding-input',
									),
								),
								'latitude'  => array(
									'type'     => 'text',
									'title'    => __( 'Latitude', 'interactive-globes' ),
									'validate' => 'csf_validate_numeric',

								),
								'longitude' => array(
									'type'     => 'text',
									'title'    => __( 'Longitude', 'interactive-globes' ),
									'validate' => 'csf_validate_numeric',

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
				'labels_cpt'    => array(
					'type'     => 'callback',
					'function' => [ MetaCore::class, 'cpt_manager' ],
					'args'     => [
						'cpt'          => 'itt_globe_dotlabel',
						'data'         => [ 'latitude', 'longitude' ],
						'button_label' => __( 'Add New Label', 'interactive-globes' ),
					],
				),
				'labelDefaults' => array(
					'type'   => 'fieldset',
					'title'  => __( 'Default values', 'interactive-globes' ),
					'desc'   => '',
					'fields' => array(

						'action'         => array(
							'type'    => 'select',
							'title'   => __( 'Click Action', 'interactive-globes' ),
							'desc'    => '',
							'options' => $actions_default,
							'default' => 'none',
						),

						'altitude'       => array(
							'type'    => 'spinner',
							'default' => 1,
							'step'    => 1,
							'title'   => __( 'Altitude', 'interactive-globes' ),
						),
						'size'           => array(
							'type'    => 'spinner',
							'default' => 20,
							'step'    => 1,
							'title'   => __( 'Label Size', 'interactive-globes' ),
						),
						'color'          => array(
							'type'    => 'color',
							'title'   => __( 'Fill Color', 'interactive-globes' ),
							'default' => isset( $options['defaultActiveColor'] ) ? $options['defaultActiveColor'] : '#99d8c9',
						),
						'hover'          => array(
							'type'    => 'color',
							'title'   => __( 'Hover Color', 'interactive-globes' ),
							'default' => isset( $options['defaultHoverColor'] ) ? $options['defaultHoverColor'] : '#2ca25f',
						),
						'includeDot'     => array(
							'title'   => __( 'Display Dot', 'interactive-globes' ),
							'desc'    => __( 'Display a dot marker next to the text indicating the exact coordinates of the label.', 'interactive-globes' ),
							'type'    => 'switcher',
							'default' => true,
						),
						'radius'         => array(
							'type'       => 'spinner',
							'default'    => 20,
							'step'       => 1,
							'title'      => __( 'Dot Radius', 'interactive-globes' ),
							'dependency' => [ [ 'includeDot', '==', true ] ],

						),
						'dotOrientation' => array(
							'type'       => 'select',
							'default'    => 'bottom',
							'options'    => array(
								'bottom' => __( 'Bottom', 'interactive-globes' ),
								'top'    => __( 'Top', 'interactive-globes' ),
								'right'  => __( 'Right', 'interactive-globes' ),
							),
							'title'      => __( 'Dot Orientation', 'interactive-globes' ),
							'dependency' => array( array( 'includeDot', '==', true ) ),
						),

					),
				),
				'labelFont'     => array(
					'type'    => 'select',
					'title'   => __( 'Font Typeface', 'interactive-globes' ),
					'desc'    => 'The fonts used in the labels are in a specific format, so it\'s not possible to load any font we want. Here you can select from some fonts available.',
					'default' => 'default',
					'options' => array(
						'default'          => __( 'Default (Helvetiker Regular)', 'interactive-globes' ),
						'OpenSans_Regular' => __( 'Open Sans Regular (125kb)', 'interactive-globes' ),
					),
				),
			),
		];

		return $meta;
	}
}
