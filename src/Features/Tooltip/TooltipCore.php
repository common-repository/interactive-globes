<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Tooltip;

use Saltus\WP\Framework\Infrastructure\Plugin\Registerable;

/**
 * The Meta class
 */
class TooltipCore implements Registerable {

	public function __construct( ...$dependencies ) {}

	/**
	 * Register Tooltip filters
	 */
	public function register() {
		// single globe page filter
		add_filter( 'itt_globes/render/container_class', [ $this, 'container_class' ] );
		add_filter( 'itt_globes/render/content_before', [ $this, 'content_before' ], 10, 2 );
	}

	public function container_class( $container_class ) {
		return $container_class . ' itt_globe_wrapper_pro';
	}

	public function content_before( $before, $id ) {
		$globe = get_post_meta( $id, 'globe_info', true );

		if ( empty( $globe['tooltip'] ) ) {
			return $before;
		}

		$tooltip = $globe['tooltip'];

		$bg = ! empty( $tooltip['backgroundColor'] ) ? $tooltip['backgroundColor'] : '#FFFFFF';
		$color = ! empty( $tooltip['font']['color'] ) ? $tooltip['font']['color'] : '#000000';
		$padding = ! empty( $tooltip['padding'] ) ? $tooltip['padding']['top'] . $tooltip['padding']['unit'] . ' ' . $tooltip['padding']['right'] . $tooltip['padding']['unit'] . ' ' .  $tooltip['padding']['bottom'] . $tooltip['padding']['unit'] . ' ' . $tooltip['padding']['left'] . $tooltip['padding']['unit'] . ' ' : '8px';
		$border_radius = ! empty( $tooltip['cornerRadius'] ) ? $tooltip['cornerRadius'] .'px' : '3px';
		$box_shadow = '';
		$font_family = ! empty( $tooltip['fontFamily'] ) ? $tooltip['fontFamily'] : 'inherit';
		$text_align = ! empty( $tooltip['font']['text-align'] ) ? $tooltip['font']['text-align'] : 'initial';
		$font_size = ! empty( $tooltip['font']['font-size'] ) ? $tooltip['font']['font-size'] . 'px' : 'inherit';
		$max_width = ! empty( $tooltip['maxWidth'] ) ? 'max-width: ' . $tooltip['maxWidth'] . 'px' : '';
		$border = ! empty( $tooltip['border'] ) && ! empty( $tooltip['border']['all'] ) ? $tooltip['border'] : false;
		$border_rule = $border ? $border['all'] .'px ' . $border['style'] . ' ' . $border['color'] : 'none';

		$style = sprintf( '<style>
			.itt_globe_wrapper_pro .itt_globe_tooltip {
				background:%1$s;
				color:%2$s;
				padding:%3$s;
				border-radius: %4$s;
				box-shadow: %5$s;
				font-family: %6$s;
				text-align: %7$s;
				font-size: %8$s;
				border: %9$s;
				%10$s;
			}',
			$bg,
			$color,
			$padding,
			$border_radius,
			$box_shadow,
			$font_family,
			$text_align,
			$font_size, // 8
			$border_rule,
			$max_width
		);
		$style .= '</style>';
		return $before . $style;
	}
}
