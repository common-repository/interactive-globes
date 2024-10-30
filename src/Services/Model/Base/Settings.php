<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

/**
 * Manage Settings
 */
class Settings {

	public static function get( $path ) {
		$plugins_url = plugin_dir_url( $path );
		$options     = get_option( 'interactive-globes' );
		$dist_dir    = WP_ENV === 'development' ? '' : 'dist';
		$assets_url  = $plugins_url . $dist_dir;

		$actions = array(
			'none'         => __( 'None', 'interactive-globes' ),
			'open_url'     => __( 'Open URL', 'interactive-globes' ),
			'open_url_new' => __( 'Open URL (new tab)', 'interactive-globes' ),
		);

		$tooltip_editor        = isset( $options['tooltip_editor'] ) ? $options['tooltip_editor'] : 'textarea';
		$action_content_editor = isset( $options['actionContent_editor'] ) ? $options['actionContent_editor'] : 'textarea';
		$coordinates_editor    = isset( $options['map_field'] ) && $options['map_field'] ? 'map' : 'fieldset';
		$capability            = isset( $options['capability'] ) ? $options['capability'] : 'page';

		//TODO by pcarvalho: create class model for settings var
		$settings_list = [
			'options'               => $options,
			'capability'            => $capability,
			'assets_url'            => $assets_url,
			'coordinates_editor'    => $coordinates_editor,
			'tooltip_editor'        => $tooltip_editor,
			'action_content_editor' => $action_content_editor,
			'actions_default'       => $actions,
		];

		return $settings_list;
	}
}
