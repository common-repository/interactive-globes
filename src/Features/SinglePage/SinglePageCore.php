<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\SinglePage;

use Saltus\WP\Framework\Infrastructure\Plugin\Registerable;

/**
 * The Meta class
 */
class SinglePageCore implements Registerable {

	public function __construct( ...$dependencies ) {}

	/**
	 * Register Shortcode
	 */
	public function register() {
		// single globe page filter
		add_filter( 'the_content', [ $this, 'content_shortcode' ] );
	}

	public function content_shortcode( $content ) {
		if ( ! is_singular( 'iglobe' ) ) {
			return $content;
		}

		global $post;
		$content = do_shortcode( "[display-globe id='" . $post->ID . "']" );

		return $content;
	}
}
