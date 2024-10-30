<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model;

/**
 * Manage Assets like scripts and styles.
 */
class Assembler {

	public static function run_actions( $settings ) {

		if ( ! empty( $settings['actions_default'] ) ) {
			$settings['actions_default'] = apply_filters( 'itt_globes/click_actions', $settings['actions_default'] );
		}
		return $settings;
	}

	public static function create_settings( $default_settings, $custom_settings ) {

		foreach ( $custom_settings as $key => $setting ) {
			if ( empty( $setting ) ) {
				continue;
			}
			if ( ! is_array( $setting ) ) {
				continue;
			}
			$default_settings[] = $key;
			$default_settings[ $key ] = array_merge( $default_settings[ $key ], $setting );
		}

		return $default_settings;
	}
}
