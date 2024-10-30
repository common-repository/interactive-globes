<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

/**
 * common labels
 */
class Labels {
	public static function get() {
		return array(
			'has_one'     => 'Globe',
			'has_many'    => 'Globes',
			'text_domain' => 'interactive-globes',

			// optional, but better for translation
			'overrides'   => array(
				'labels'        => array(
					'name'                  => __( 'Globes', 'interactive-globes' ),
					'singular_name'         => __( 'Globe', 'interactive-globes' ),
					'menu_name'             => __( 'Globes', 'interactive-globes' ),
					'name_admin_bar'        => __( 'Globe', 'interactive-globes' ),
					'add_new'               => __( 'Create New', 'interactive-globes' ),
					'add_new_item'          => __( 'Create New Globe', 'interactive-globes' ),
					'edit_item'             => __( 'Edit Globe', 'interactive-globes' ),
					'new_item'              => __( 'New Globe', 'interactive-globes' ),
					'view_item'             => __( 'View Globe', 'interactive-globes' ),
					'view_items'            => __( 'View Globes', 'interactive-globes' ),
					'search_items'          => __( 'Search Globes', 'interactive-globes' ),
					'not_found'             => __( 'No Globes found.', 'interactive-globes' ),
					'not_found_in_trash'    => __( 'No Globes found in Trash.', 'interactive-globes' ),
					'parent_item-colon'     => __( 'Parent Globes:', 'interactive-globes' ),
					'all_items'             => __( 'All Globes', 'interactive-globes' ),
					'archives'              => __( 'Globe Archives', 'interactive-globes' ),
					'attributes'            => __( 'Globe Attributes', 'interactive-globes' ),
					'insert_into_item'      => __( 'Insert into Globe', 'interactive-globes' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Globe', 'interactive-globes' ),
					'filter_items_list'     => __( 'Filter Globes list', 'interactive-globes' ),
					'items_list_navigation' => __( 'Globes list navigation', 'interactive-globes' ),
					'items_list'            => __( 'Globes list', 'interactive-globes' ),
					'featured_image'        => __( 'Globe Cover Image', 'interactive-globes' ),
					'set_featured_image'    => __( 'Set Globe Cover Image', 'interactive-globes' ),
					'remove_featured_image' => __( 'Remove Globe Cover', 'interactive-globes' ),
					'use_featured_image'    => __( 'Use as Globe Cover', 'interactive-globes' ),
				),
				// you can use the placeholders {permalink}, {preview_url}, {date}
				'messages'      => array(
					'post_updated'         => __( 'Globe information updated.', 'interactive-globes' ),
					'post_updated_short'   => __( 'Globe info updated', 'interactive-globes' ),
					'custom_field_updated' => __( 'Custom field updated', 'interactive-globes' ),
					'custom_field_deleted' => __( 'Custom field deleted', 'interactive-globes' ),
					'restored_to_revision' => __( 'Globe content restored from revision', 'interactive-globes' ),
					'post_published'       => __( 'Globe Published', 'interactive-globes' ),
					'post_saved'           => __( 'Globe information saved.', 'interactive-globes' ),
					'post_submitted'       => __( 'Globe submitted. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
					'post_schedulled'      => __( 'Globe scheduled for {date}. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
					'post_draft_updated'   => __( 'Globe draft updated. <a href="{preview_url}" target="_blank">Preview</a>', 'interactive-globes' ),
				),
				'bulk_messages' => array(
					'updated_singular'   => __( 'Globe updated. Yay!', 'interactive-globes' ),
					'updated_plural'     => __( '%s Globes updated. Yay!', 'interactive-globes' ),
					'locked_singular'    => __( 'Globe not updated, somebody is editing it', 'interactive-globes' ),
					'locked_plural'      => __( '%s Globes not updated, somebody is editing them', 'interactive-globes' ),
					'deleted_singular'   => __( 'Globe permanetly deleted. Fahrenheit 451 team was here?', 'interactive-globes' ),
					'deleted_plural'     => __( '%s Globes permanently deleted. Why? :(', 'interactive-globes' ),
					'trashed_singular'   => __( 'Globe moved to the trash. I\'m sad :(', 'interactive-globes' ),
					'trashed_plural'     => __( '%s Globes moved to the trash. Why? :(', 'interactive-globes' ),
					'untrashed_singular' => __( 'Globe recovered from trash. Well done!', 'interactive-globes' ),
					'untrashed_plural'   => __( '%s Globes saved from the enemies!', 'interactive-globes' ),
				),
				// overrides some of the available button labels and placeholders
				'ui'            => array(
					'enter_title_here' => __( 'Enter Globe name here', 'interactive-globes' ),
				),
			),
		);
	}
}
