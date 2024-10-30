<?php

$options = get_option( 'ittglobes' );
$capability            = isset( $options['capability'] ) && ! empty( $options['capability'] ) ? $options['capability'] : 'page';
$coordinates_editor    = isset( $options['map_field'] ) && $options['map_field'] ? 'map' : 'fieldset';
$tooltip_editor        = isset( $options['tooltip_editor'] ) && ! empty( $options['tooltip_editor'] ) ? $options['tooltip_editor'] : 'textarea';
$action_content_editor = isset( $options['actionContent_editor'] ) && ! empty( $options['actionContent_editor'] ) ? $options['actionContent_editor'] : 'text';
$public_cpts           = isset( $options['public_cpts'] ) && $options['public_cpts'] ? true : false;


$points = [
	'active'       => true,
	'type'         => 'cpt',
	'name'         => 'itt_globe_point',
	'features'     => [
		'dragAndDrop'   => true,
		'duplicate'     => true,
	],
	'supports'     => [
		'title',
		'custom-fields',
	],
	'labels'       => [
		'has_one'  => 'Point',
		'has_many' => 'Points',
		// optional, but better for translation
		'overrides'   => array(
			'labels'        => array(
				'name'                  => __( 'Points', 'interactive-globes' ),
				'singular_name'         => __( 'Point', 'interactive-globes' ),
				'menu_name'             => __( 'Points', 'interactive-globes' ),
				'name_admin_bar'        => __( 'Points', 'interactive-globes' ),
				'add_new'               => __( 'Create New', 'interactive-globes' ),
				'add_new_item'          => __( 'Create New Point', 'interactive-globes' ),
				'edit_item'             => __( 'Edit', 'interactive-globes' ),
				'new_item'              => __( 'New Point', 'interactive-globes' ),
				'view_item'             => __( 'View Point', 'interactive-globes' ),
				'view_items'            => __( 'View Points', 'interactive-globes' ),
				'search_items'          => __( 'Search Points', 'interactive-globes' ),
				'not_found'             => __( 'No Points found.', 'interactive-globes' ),
				'not_found_in_trash'    => __( 'No Points found in Trash.', 'interactive-globes' ),
				'parent_item-colon'     => __( 'Parent Globe:', 'interactive-globes' ),
				'all_items'             => '&#9900; ' . __( 'Points', 'interactive-globes' ),
				'archives'              => __( 'Point Archives', 'interactive-globes' ),
				'attributes'            => __( 'Point Attributes', 'interactive-globes' ),
				'insert_into_item'      => __( 'Insert into Point', 'interactive-globes' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Point', 'interactive-globes' ),
				'filter_items_list'     => __( 'Filter Point list', 'interactive-globes' ),
				'items_list_navigation' => __( 'Point list navigation', 'interactive-globes' ),
				'items_list'            => __( 'Point list', 'interactive-globes' ),
				'featured_image'        => __( 'Point Cover Image', 'interactive-globes' ),
				'set_featured_image'    => __( 'Set Point Cover Image', 'interactive-globes' ),
				'remove_featured_image' => __( 'Remove Point Cover', 'interactive-globes' ),
				'use_featured_image'    => __( 'Use as Point Cover', 'interactive-globes' ),
			),
			// you can use the placeholders {permalink}, {preview_url}, {date}
			'messages'      => array(
				'post_updated'         => __( 'Point information updated. <a href="{permalink}" target="_blank">View Globe</a>', 'interactive-globes' ),
				'post_updated_short'   => __( 'Point info updated', 'interactive-globes' ),
				'custom_field_updated' => __( 'Custom field updated', 'interactive-globes' ),
				'custom_field_deleted' => __( 'Custom field deleted', 'interactive-globes' ),
				'restored_to_revision' => __( 'Point content restored from revision', 'interactive-globes' ),
				'post_published'       => __( 'Point Published', 'interactive-globes' ),
				'post_saved'           => __( 'Point information saved.', 'interactive-globes' ),
				'post_submitted'       => __( 'Point submitted. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
				'post_schedulled'      => __( 'Point scheduled for {date}. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
				'post_draft_updated'   => __( 'Point draft updated. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
			),
			'bulk_messages' => array(
				'updated_singular'   => __( 'Point updated. Yay!', 'interactive-globes' ),
				'updated_plural'     => __( '%s Point updated. Yay!', 'interactive-globes' ),
				'locked_singular'    => __( 'Point not updated, somebody is editing it', 'interactive-globes' ),
				'locked_plural'      => __( '%s Point not updated, somebody is editing them', 'interactive-globes' ),
				'deleted_singular'   => __( 'Point permanetly deleted. Fahrenheit 451 team was here?', 'interactive-globes' ),
				'deleted_plural'     => __( '%s Point permanently deleted. Why? :(', 'interactive-globes' ),
				'trashed_singular'   => __( 'Point moved to the trash. I\'m sad :(', 'interactive-globes' ),
				'trashed_plural'     => __( '%s Point moved to the trash. Why? :(', 'interactive-globes' ),
				'untrashed_singular' => __( 'Point recovered from trash. Well done!', 'interactive-globes' ),
				'untrashed_plural'   => __( '%s Point saved from the enemies!', 'interactive-globes' ),
			),
			// overrides some of the available button labels and placeholders
			'ui'            => array(
				'enter_title_here' => __( 'Enter Point name here', 'interactive-globes' ),
			),
		),
	],
	'options'      => [
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_in_rest'        => true,
		'show_in_nav_menus'   => $public_cpts,
		'show_ui'             => true,
		'capability_type'     => $capability,
		'show_in_menu'        => $public_cpts ? 'edit.php?post_type=iglobe' : false,
	],
	'block_editor' => false,
	'meta'         => [
		'points_info' => [
			'id'                => 'points_info',
			'data_type'         => 'serialize',
			'register_rest_api' => true,
			'title'             => __( 'Information', 'interactive-globes' ),
			'nav'               => 'inline',
			'sections'          => [
				'details' => [
					'icon'   => 'fa fa-info-circle fa-lg',
					'title'  => __( 'Details', 'interactive-globes' ),
					'fields' => array(
						'coordinates'    => array(
							'type'   => $coordinates_editor,
							'title'  => __( 'Coordinates', 'interactive-globes' ),
							'register' => true,
							'fields' => array(
								'zoom'      => array(
									'type'  => 'text',
									'title' => __( 'zoom', 'interactive-globes' ),
									'class' => 'geocoding geocoding-hide',
									'attributes' => array(
										'class' => 'geocoding-input',
									),
									'default' => 2,
								),
								'address'      => array(
									'type'  => 'text',
									'title' => __( 'address', 'interactive-globes' ),
									'class' => 'geocoding geocoding-hide',
									'attributes' => array(
										'class' => 'geocoding-input',
									),
								),
								'latitude'  => array(
									'type'     => 'text',
									'title'    => __( 'Latitude', 'interactive-globes' ),
									'validate' => 'csf_validate_numeric',
									'default'  => ! empty( $_GET['latitude'] ) ? (float) $_GET['latitude'] : '',

								),
								'longitude' => array(
									'type'     => 'text',
									'title'    => __( 'Longitude', 'interactive-globes' ),
									'validate' => 'csf_validate_numeric',
									'default'  => ! empty( $_GET['longitude'] ) ? (float) $_GET['longitude'] : '',
								),
							),
							// if it's a map field, defaults have a different syntax and are set at this level
							'default' => array(
								'latitude'  => ! empty( $_GET['latitude'] ) ? (float) $_GET['latitude'] : '',
								'longitude' => ! empty( $_GET['longitude'] ) ? (float) $_GET['longitude'] : '',
							),
						),

						'tooltipContent' => array(
							'type'  => $tooltip_editor,
							'title' => __( 'Tooltip Content', 'interactive-globes' ),
						),
						'content'        => array(
							'type'     => $action_content_editor,
							'title'    => __( 'Action Content', 'interactive-globes' ),
							'subtitle' => __( 'URL or content to trigger when marker is clicked.', 'interactive-globes' ),
							'register' => true,
						),
					),
				],
			],
		],
		'relationship_point' => [
			'id'                => 'points_globe',
			'title'             => __( 'Globe rel', 'interactive-globes' ),
			'class'             => isset ( $_GET['iframe'] ) ? 'hidden' : '',
			'register_rest_api' => true,
			'sections'          => [
				'details' => [
					'fields'       => array(
						'globe_id'        => array(
							'type'     => 'number',
							'class'    => isset ( $_GET['iframe'] ) ? 'hidden' : '',
							'title'    => __( 'Associated Globe', 'interactive-globes' ),
							'register_rest_api' => true,
						),
					),
				],
			],
		],
	],
];

add_filter( 'itt_globes/meta/remove_meta_box_post_types', 'add_itt_globe_point_post_type', 10, 2 );
function add_itt_globe_point_post_type( $post_types ) {
	$post_types[] = 'itt_globe_point';
	return $post_types;
}

add_filter( 'itt_globes/shortcode/post_setup_meta', 'add_point_to_shortcode', 10, 2 );
function add_point_to_shortcode( $globe_meta, $globe_id ) {
	$args = [
		'post_type'      => 'itt_globe_point',
		'posts_per_page' => -1,
		'meta_query'     => [
			[
				'key'     => 'globe_id',
				'value'   => $globe_id,
				'compare' => '=',
				'type'    => 'NUMERIC',
			],
		],
	];
	$cpt_list      = [];
	$cpt_query     = new \WP_Query( $args );
	$click_actions = [];
	if ( isset( $globe_meta['pointDefaults']['action'] ) &&
		$globe_meta['pointDefaults']['action'] !== 'none' ) {
		$click_actions[] = $globe_meta['pointDefaults']['action'];
	}

	if ( $cpt_query->have_posts() ) {
		while ( $cpt_query->have_posts() ) {
			$cpt_query->the_post();
			$cpt_id   = get_the_ID();
			$metadata = get_post_meta( $cpt_id );

			if ( empty( $metadata['points_info'][0] ) ) {
				continue;
			}
			$cpt_info          = maybe_unserialize( $metadata['points_info'][0] );
			$cpt_info['id']    = $cpt_id;
			$cpt_info['title'] = get_the_title();

			// convert natural line breaks to <br>
			$cpt_info['content'] = nl2br( $cpt_info['content'] );

			$cpt_list[]        = $cpt_info;

			if ( ! empty( $cpt_info['action'] ) &&
				$cpt_info['action'] !== 'none' ) {
				$click_actions[] = $cpt_info['action'];
			}
		}
	}

	wp_reset_query();

	$globe_meta['points'] = $cpt_list;

	$globe_meta['enabled_click_actions'] = $globe_meta['enabled_click_actions'] ?? [];
	$globe_meta['enabled_click_actions'] = array_merge( $globe_meta['enabled_click_actions'], $click_actions );
	$globe_meta['enabled_click_actions'] = array_unique( $globe_meta['enabled_click_actions'] );

	return $globe_meta;
}


$points = apply_filters( 'itt_globes/point/model', $points );
return $points;
