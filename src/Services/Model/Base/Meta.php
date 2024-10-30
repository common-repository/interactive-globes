<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

/**
 * common meta
 */
class Meta {
	public static function get( $settings ) {

		// prevent loading meta in other screens
		global $pagenow;
		if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' ) {
			return [];
		}

		$options               = $settings['options'];
		$tooltip_editor        = $settings['tooltip_editor'];
		$action_content_editor = $settings['action_content_editor'];
		$coordinates_editor    = $settings['coordinates_editor'];
		$assets_url            = $settings['assets_url'];
		$actions_default       = $settings['actions_default'];

		return [
			'globe_preview' => array(
				'class'     => 'itt_globe_meta',
				'title'     => __( 'Preview', 'interactive-globes' ),
				'data_type' => 'serialize',
				'fields'    => array(
					'preview' => array(
						'type'    => 'content',
						'content' => '<div class="itt_globe_preview_container">
										<div class="itt_globe_preview">
										<div id="itt_globe_preview_admin"></div>'
										/*. do_shortcode( '[display-globe]' ) */
											. '<div class="itt_globe_save_warning">'
											. __( 'Changes detected. Save your globe to update the preview.', 'interactive-globes' ) . '
												</div>
										</div>
										<div class="itt_globe_preview_info" >
											<div id="itt_globe_preview_data_center" class="postbox">
												<div class="postbox-header">
													<div class="itt_globe_preview_title">Center Coordinates</div>
												</div>
												<div class="inside">
													<div class="itt_globe_preview_data"></div>
													<div class="itt_globe_preview_action">
														<button onclick="ittGlobesAdmin.setCenter(this)" data-altitude="" data-lat="" data-lng="" type="button" class="itt_globe_preview_action button">Set initial center</button>
													</div>
												</div>
											</div>

											<div id="itt_globe_preview_data_clicked" class="postbox">
												<div class="postbox-header">
													<div class="itt_globe_preview_title">Clicked Location</div>
													<button style="display:none;" onclick="ittGlobesAdmin.edit(this)" data-id="" data-post-type="" type="button" class="itt_globe_preview_action itt_globe_preview_action_edit button">Edit</button>
												</div>
												<div class="inside">
													<div class="itt_globe_preview_data">
													Click the globe to get coordinates
													</div>
													<div class="itt_globe_preview_action">
														<select style="display:none;" onChange="ittGlobesAdmin.addNew(this)" class="itt_globe_preview_action itt_globe_preview_action_add_new">
															<option value="">Add new...</option>
															<option value="itt_globe_point">Point</option>
															<option value="itt_globe_dotlabel">Dot Label</option>
														</select>
														
													</div>
												</div>
											</div>
										</div>
									</div>',
					),
				),
			),
			'globe_info'   => array(
				'class'     => 'itt_globe_info',
				'title'     => __( 'Globe Settings', 'interactive-globes' ),
				'data_type' => 'serialize',
				'register'  => true,
				'sections'  => array(
					'globe'     => array(
						'title'  => __( 'Globe', 'interactive-globes' ),
						'icon'   => 'fa fa-globe',
						'fields' => array(
							'globeImage'        => array(
								'type'    => 'image_select',
								'title'   => 'Globe Image',
								'options' => array(
									'earth-blue-marble.jpg' => sprintf( '%1$s/assets/imgs/earth-blue-marble-thumb.png', $assets_url ),
									'earth-dark.jpg'        => sprintf( '%1$s/assets/imgs/earth-dark-thumb.png', $assets_url ),
									'earth-day.jpg'         => sprintf( '%1$s/assets/imgs/earth-day-thumb.png', $assets_url ),
									'earth-night.jpg'       => sprintf( '%1$s/assets/imgs/earth-night-thumb.png', $assets_url ),
									'earth-noClouds.jpg'    => sprintf( '%1$s/assets/imgs/earth-noClouds-thumb.png', $assets_url ),
									'earth-clouds.jpeg'     => sprintf( '%1$s/assets/imgs/earth-clouds-thumb.png', $assets_url ),
								),
								'default' => 'earth-day.jpg',
							),
							'globeColor'    => array(
								'dependency' => array( array( 'globeImage', '==', 'noImage' ) ),
							),
							'customImage'    => array(
								'dependency' => array( array( 'globeImage', '==', 'customImage' ) ),
							),
							'showGraticules'    => array(
								'title'   => __( 'Show Graticules', 'interactive-globes' ),
								'desc'    => __( 'Show a graticule grid demarking latitude and longitude lines.', 'interactive-globes' ),
								'type'    => 'switcher',
								'default' => false,
							),
							'atmosphere'        => array(
								'type'   => 'fieldset',
								/* translators: legend refers to a caption or visual element explaining colours on map */
								'title'  => __( 'Athmosphere', 'interactive-globes' ),
								'desc'   => __( 'Show a bright halo surrounding the globe, representing the atmosphere.', 'interactive-globes' ),
								'fields' => array(
									'enabled'            => array(
										'type'    => 'switcher',
										'title'   => __( 'Enabled', 'interactive-globes' ),
										'default' => false,
									),
									'atmosphereColor'    => array(
										'type'       => 'color',
										'title'      => __( 'Color', 'interactive-globes' ),
										'default'    => '#87CEFA',
										'dependency' => array( array( 'enabled', '==', true ) ),
									),
									'atmosphereAltitude' => array(
										'type'       => 'spinner',
										'title'      => __( 'Altitude', 'interactive-globes' ),
										'default'    => '0.15',
										'min'        => 0,
										'max'        => 10,
										'step'       => 0.01,
										'desc'       => __( 'Max altitude of the atmosphere, in terms of globe radius units.<br>The size of the athmosphere added outside the globe in terms of percentage of the globe size.', 'interactive-globes' ),
										'dependency' => array( array( 'enabled', '==', true ) ),
									),
								),
							),
							'altitude'          => array(
								'type'    => 'spinner',
								'default' => 1.7,
								'step'    => 0.01,
								'desc'    => __( 'Controls the initial globe scale', 'interactive-globes' ),
								'title'   => __( 'Initial altitude/scale', 'interactive-globes' ),
							),
							'centerCoordinates' => array(
								'type'   => $coordinates_editor,
								'title'  => __( 'Center coordinates', 'interactive-globes' ),
								'desc'   => __( 'Center of the globe when initially loaded.', 'interactive-globes' )
								/* translators: %s will be a link to an external website */
								. sprintf( __( 'You can get the latitude and longitude for a specific location clicking it on the globe or from sites like %s.', 'interactive-globes' ), '<a href="https://www.latlong.net/" target="_blank">LatLong.net</a>' )
								. '<br> ' .
								__( 'Correct format: Latitude: 41.1579, Longitude: -8.6291<br> Wrong format: Latitude: <strike>41.1579° N</strike> Longitude: <strike>8.6291° W</strike>', 'interactive-globes' ),
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
										'default'  => 0,

									),
									'longitude' => array(
										'type'     => 'text',
										'title'    => __( 'Longitude', 'interactive-globes' ),
										'validate' => 'csf_validate_numeric',
										'default'  => 0,
									),
								),
							),

							'interactions' => array(
								'type'   => 'fieldset',
								/* translators: legend refers to a caption or visual element explaining colours on map */
								'title'  => __( 'Interactions & Controls', 'interactive-globes' ),
								'desc'   => __( 'Allow interactions with globe, like zoom and pan.', 'interactive-globes' ),
								'fields' => [
									'zoom' => [
										'type'   => 'switcher',
										'default'    => true,
										'title'      => __( 'Zoom', 'interactive-globes' ),
									],
									'pan' => [
										'type'   => 'switcher',
										'default'    => true,
										'title'      => __( 'Pan & Rotate', 'interactive-globes' ),
									]
								],
							),
							'animateIn' => array(
								'type'   => 'switcher',
								/* translators: legend refers to a caption or visual element explaining colours on map */
								'title'  => __( 'Animate In', 'interactive-globes' ),
								'desc'   => __( 'Animate globe on load', 'interactive-globes' ),
								'default' => true,
							),
						),
					),
					'container' => array(
						'title'  => __( 'Container', 'interactive-globes' ),
						'icon'   => 'fa fa-desktop',
						'fields' => array(

							'backgroundColor'  => array(
								'type'    => 'color',
								'title'   => __( 'Background Color', 'interactive-globes' ),
								'default' => 'transparent',
							),
							'paddingTop'       => array(
								'type'    => 'spinner',
								'title'   => __( 'Container Height', 'interactive-globes' ),
								'default' => '56',
								'min'     => 10,
								'max'     => 200,
								'step'    => 1,
								'unit'    => '%',
								'desc'    => __( 'The default 56% corresponds to a 16:9 aspect ratio. 100% would be a square. We use percentual values to make sure the map is responsive and calculates the height based on it\'s width.', 'interactive-globes' ),
							),
							'paddingTopMobile' => array(
								'type'    => 'spinner',
								'title'   => __( 'Container Height on Mobile', 'interactive-globes' ),
								'default' => '',
								'min'     => 10,
								'max'     => 100,
								'step'    => 1,
								'unit'    => '%',
								'desc'    => __( 'Leave blank to use the same value above. On mobile there might be the need for the globe container to take more vertical space. You can increase the map height here, to change its aspect ratio.', 'interactive-globes' ),
							),
							'maxWidth'         => array(
								'type'    => 'spinner',
								'title'   => __( 'Container Max-Width', 'interactive-globes' ),
								'desc'    => __( 'Leave empty if you always want your globe to take 100% of the available space. Otherwise set a maximum pixel width for the globe.', 'interactive-globes' ),
								'default' => '',
								'min'     => 10,
								'max'     => 100,
								'step'    => 1,
								'unit'    => 'px',
							),

						),
					),
				),
			),
		];
	}
}
