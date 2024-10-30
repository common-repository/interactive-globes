<?php
namespace Saltus\WP\Plugin\Saltus\InteractiveGlobes\Features\Sales;

use Saltus\WP\Framework\Infrastructure\Container\Invalid;
use Saltus\WP\Framework\Infrastructure\Container\ServiceContainer;
use Saltus\WP\Framework\Infrastructure\Plugin\Registerable;

/**
 * The Meta class
 */
class UpsellPage implements Registerable {


	private $services;

	public function __construct( ...$dependencies ) {

		if ( empty( $dependencies[1] ) ) {
			throw Invalid::from( 'Services' );
		}
		if ( ! $dependencies[1] instanceof ServiceContainer ) {
			throw Invalid::from( $dependencies[1] );
		}

		$this->services = $dependencies[1];
	}

	/**
	 * Register Shortcode
	 */
	public function register() {
		try {
			$assets = $this->services->get( 'assets' )->create();
			$assets->load_admin_styles( '/assets/css/features/sales/upsell-page.css' );
		} catch ( \Exception $exception ) {

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions
				error_log( 'Failed to load styles' );
			}
		}
		// single globe page filter
		add_action(
			'admin_menu',
			function () {
				add_submenu_page(
					'edit.php?post_type=iglobe',
					__( 'Try Pro Version', 'interactive-globes' ),
					__( 'Try Pro Version', 'interactive-globes' ),
					'manage_options',
					'trypro',
					[ $this, 'globe_go_pro_page' ]
				);
			}
		);
	}


	public function globe_go_pro_page() {

		$html = '
			<div class="upsell-container">
			<h1>Upgrade to Interactive Globes Pro</h1>
			<p>Unlock more globe styles, regions, arc lines and advanced click actions with our Pro version.</p>
			<div class="features">
			<h2>Pro Version Features</h2>

			<div class="features-content">
				<ul>
					<li>More Globe Styles</li>
					<li>More Click Actions</li>
					<li>Custom globe image</li>
					<li>Lines to connect markers</li>
					<li>Vector Regions</li>
					<li>Custom globe colours</li>
					<li>Priority Support</li>
					<li>Regular Updates</li>
				</ul>
				<video width="250" height="250" autoplay loop>
					<source src="https://wpinteractiveglobes.com/wp-content/uploads/2024/07/0729.mp4" type="video/mp4">
				</video>
				</div>
		</div>

		<div class="pricing">
				<h2>Special Launch Prices</h2>
				<div class="pricing-options">
					<div class="pricing-option">
						<h3>Annual Subscription</h3>
						<p class="price">$29/year</p>
						<p>Save more with our annual subscription.</p>
					</div>
					<div class="pricing-option">
						<h3>Lifetime License</h3>
						<p class="price"><s>$89</s></p>
						<p class="price">$49</p>
						<p>One-time payment.</p>
					</div>
				</div>
			</div>

			<div class="cta">
				<h2>Ready to Upgrade?</h2>
				<p>Get the Pro version today and take your 3D Globes to the next level!</p>
				<a href="https://wpinteractiveglobes.com/get-pro/" target="_blank" class="upgrade-button">Get Pro</a>
			</div>

			<div class="comparison">
				<h2>Free vs Pro Comparison</h2>
				<table>
					<thead>
						<tr>
							<th>Feature</th>
							<th>Free Version</th>
							<th>Pro Version</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Basic Globe Styles</td>
							<td><i class="checkmark">&#10003;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Advanced Globe Styles</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Custom Globe Colours</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Arc Lines</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Vector Interactive Countries & Continents</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Custom globe images</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Advanced Click Actions</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Open content in lightbox</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Display content next to the globe</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Priority Support</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
						<tr>
							<td>Regular Updates</td>
							<td><i class="cross">&#10007;</i></td>
							<td><i class="checkmark">&#10003;</i></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="cta">
				<h2>Take your globes to the next level</h2>
				<a href="https://wpinteractiveglobes.com/features/" target="_blank" class="upgrade-button">See Feature Demos</a>  <a href="https://wpinteractiveglobes.com/contact/" target="_blank" class="upgrade-button">Contact Us</a>
			</div>
		</div>
		<style>

		</style>';
		echo $html;
	}
}
