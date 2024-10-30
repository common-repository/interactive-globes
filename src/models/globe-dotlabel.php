<?php

$options = get_option( 'ittglobes' );

$capability            = isset( $options['capability'] ) && ! empty( $options['capability'] ) ? $options['capability'] : 'page';
$coordinates_editor    = isset( $options['map_field'] ) && $options['map_field'] ? 'map' : 'fieldset';
$tooltip_editor        = isset( $options['tooltip_editor'] ) && ! empty( $options['tooltip_editor'] ) ? $options['tooltip_editor'] : 'textarea';
$action_content_editor = isset( $options['actionContent_editor'] ) && ! empty( $options['actionContent_editor'] ) ? $options['actionContent_editor'] : 'text';
$public_cpts           = isset( $options['public_cpts'] ) && $options['public_cpts'] ? true : false;


$dot_labels = [
	'active'       => true,
	'type'         => 'cpt',
	'name'         => 'itt_globe_dotlabel',
	'features'     => [
		'duplicate'     => array(
			'label'      => __( 'Clone Dot Label', 'interactive-globes' ),
			'attr_title' => __( 'Create a copy of this dot label', 'interactive-globes' ),
		),
		'single_export' => array(
			'label' => __( 'Download Dot Labels Export File', 'interactive-globes' ),
		),
		'admin_cols'    => array(
			'title',
			'id'        => array(
				'title'      => 'ID',
				'post_field' => 'ID',
			),
		),
	],
	'supports'     => [
		'title',
		'custom-fields',
	],
	'labels'       => [
		'has_one'     => 'Dot Label',
		'has_many'    => 'Dot Labels',
		'text_domain' => 'interactive-globes',

		// optional, but better for translation
		'overrides'   => array(
			'labels'        => array(
				'name'                  => __( 'Dot Labels', 'interactive-globes' ),
				'singular_name'         => __( 'Dot Label', 'interactive-globes' ),
				'menu_name'             => __( 'Dot Labels', 'interactive-globes' ),
				'name_admin_bar'        => __( 'Dot Labels', 'interactive-globes' ),
				'add_new'               => __( 'Create New', 'interactive-globes' ),
				'add_new_item'          => __( 'Create New Dot Label', 'interactive-globes' ),
				'edit_item'             => __( 'Edit', 'interactive-globes' ),
				'new_item'              => __( 'New Dot Label', 'interactive-globes' ),
				'view_item'             => __( 'View Dot Label', 'interactive-globes' ),
				'view_items'            => __( 'View Dot Labels', 'interactive-globes' ),
				'search_items'          => __( 'Search Dot Labels', 'interactive-globes' ),
				'not_found'             => __( 'No Dot Labels found.', 'interactive-globes' ),
				'not_found_in_trash'    => __( 'No Dot Labels found in Trash.', 'interactive-globes' ),
				'parent_item-colon'     => __( 'Parent Globe:', 'interactive-globes' ),
				'all_items'             => '&#9900; ' . __( 'Dot Labels', 'interactive-globes' ),
				'archives'              => __( 'Dot Label Archives', 'interactive-globes' ),
				'attributes'            => __( 'Dot Label Attributes', 'interactive-globes' ),
				'insert_into_item'      => __( 'Insert into Dot Label', 'interactive-globes' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Dot Label', 'interactive-globes' ),
				'filter_items_list'     => __( 'Filter Dot Label list', 'interactive-globes' ),
				'items_list_navigation' => __( 'Dot Label list navigation', 'interactive-globes' ),
				'items_list'            => __( 'Dot Label list', 'interactive-globes' ),
				'featured_image'        => __( 'Dot Label Cover Image', 'interactive-globes' ),
				'set_featured_image'    => __( 'Set Dot Label Cover Image', 'interactive-globes' ),
				'remove_featured_image' => __( 'Remove Dot Label Cover', 'interactive-globes' ),
				'use_featured_image'    => __( 'Use as Dot Label Cover', 'interactive-globes' ),
			),
			// you can use the placeholders {permalink}, {preview_url}, {date}
			'messages'      => array(
				'post_updated'         => __( 'Dot Label information updated. <a href="{permalink}" target="_blank">View Globe</a>', 'interactive-globes' ),
				'post_updated_short'   => __( 'Dot Label info updated', 'interactive-globes' ),
				'custom_field_updated' => __( 'Custom field updated', 'interactive-globes' ),
				'custom_field_deleted' => __( 'Custom field deleted', 'interactive-globes' ),
				'restored_to_revision' => __( 'Dot Label content restored from revision', 'interactive-globes' ),
				'post_published'       => __( 'Dot Label Published', 'interactive-globes' ),
				'post_saved'           => __( 'Dot Label information saved.', 'interactive-globes' ),
				'post_submitted'       => __( 'Dot Label submitted. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
				'post_schedulled'      => __( 'Dot Label scheduled for {date}. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
				'post_draft_updated'   => __( 'Dot Label draft updated. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
			),
			'bulk_messages' => array(
				'updated_singular'   => __( 'Dot Label updated. Yay!', 'interactive-globes' ),
				'updated_plural'     => __( '%s Dot Label updated. Yay!', 'interactive-globes' ),
				'locked_singular'    => __( 'Dot Label not updated, somebody is editing it', 'interactive-globes' ),
				'locked_plural'      => __( '%s Dot Label not updated, somebody is editing them', 'interactive-globes' ),
				'deleted_singular'   => __( 'Dot Label permanetly deleted. Fahrenheit 451 team was here?', 'interactive-globes' ),
				'deleted_plural'     => __( '%s Dot Label permanently deleted. Why? :(', 'interactive-globes' ),
				'trashed_singular'   => __( 'Dot Label moved to the trash. I\'m sad :(', 'interactive-globes' ),
				'trashed_plural'     => __( '%s Dot Label moved to the trash. Why? :(', 'interactive-globes' ),
				'untrashed_singular' => __( 'Dot Label recovered from trash. Well done!', 'interactive-globes' ),
				'untrashed_plural'   => __( '%s Dot Label saved from the enemies!', 'interactive-globes' ),
			),
			// overrides some of the available button labels and placeholders
			'ui'            => array(
				'enter_title_here' => __( 'Enter Dot Label name here', 'interactive-globes' ),
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
		'dotLabels_info' => [
			'id'                => 'dotLabels_info',
			'data_type'         => 'serialize',
			'register_rest_api' => true,
			'title'             => __( 'Information', 'interactive-globes' ),
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
						),
					),
				],
			],
		],
		'relationship_dotlabel' => [
			'id'                => 'dotlabel_globe',
			'title'             => __( 'Globe - Dot Label relationship', 'interactive-globes' ),
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

add_filter( 'itt_globes/meta/remove_meta_box_post_types', 'add_itt_globe_dotlabel_post_type', 10, 2 );
function add_itt_globe_dotlabel_post_type( $post_types ) {
	$post_types[] = 'itt_globe_dotlabel';
	return $post_types;
}

add_filter( 'itt_globes/shortcode/post_setup_meta', 'add_dotlabel_to_shortcode', 10, 2 );
function add_dotlabel_to_shortcode( $globe_meta, $globe_id ) {
	// dotlabel
	$args = [
		'post_type'      => 'itt_globe_dotlabel',
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
	$cpt_query    = new \WP_Query( $args );
	$click_actions = [];

	if ( isset( $globe_meta['labelDefaults']['action'] ) &&
		$globe_meta['labelDefaults']['action'] !== 'none' ) {
		$click_actions[] = $globe_meta['labelDefaults']['action'];
	}

	if ( $cpt_query->have_posts() ) {
		while ( $cpt_query->have_posts() ) {
			$cpt_query->the_post();
			$cpt_id    = get_the_ID();
			$metadata  = get_post_meta( $cpt_id );

			if ( empty( $metadata['dotLabels_info'][0] ) ) {
				continue;
			}

			$cpt_info          = maybe_unserialize( $metadata['dotLabels_info'][0] );
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

	$globe_meta['dotLabels'] = $cpt_list;

	$globe_meta['enabled_click_actions'] = $globe_meta['enabled_click_actions'] ?? [];
	$globe_meta['enabled_click_actions'] = array_merge( $globe_meta['enabled_click_actions'], $click_actions );
	$globe_meta['enabled_click_actions'] = array_unique( $globe_meta['enabled_click_actions'] );

	return $globe_meta;
}

$dot_labels = apply_filters( 'itt_globes/dotlabel/model', $dot_labels );
return $dot_labels;
