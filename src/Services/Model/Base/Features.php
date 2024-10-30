<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

/**
 * common labels
 */
class Features {

	public static function get() {
		return [
			'single_export' => [
				'label' => __( 'Download Globe Export File', 'interactive-globes' ),
			],
			'admin_cols'    => [
				'title',
				'id'        => [
					'title'      => 'ID',
					'post_field' => 'ID',
				],
				'shortcode' => [
					'title'    => __( 'Shortcode', 'interactive-globes' ),
					'function' => function () {
						global $post;
						echo esc_html( '[display-globe id="' . $post->ID . '"]' );
					},
				],
			],
			'remember_tabs' => true,
		];
	}
}
