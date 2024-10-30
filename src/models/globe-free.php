<?php
if ( ! defined( 'ABSPATH' ) ) {
	return;
}
if ( defined( 'SALTUS_PLAN' ) && SALTUS_PLAN !== 'free' ) {
	return [];
}

// Assemblers
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Assembler;
use Saltus\WP\Plugin\Saltus\InteractiveGlobes\Services\Model\Base;

function itt_globe_model_free() {

	// setup the settings
	$path     = dirname( __DIR__ );
	$custom   = [];
	$settings = Base\Settings::get( $path );
	$settings = Assembler::create_settings( $settings, $custom );
	$settings = Assembler::run_actions( $settings );

	// setup the base layer
	$globe_model    = Base\Model::get();
	$globe_labels   = Base\Labels::get();
	$globe_meta     = Base\Meta::get( $settings );
	$globe_meta     = Base\MetaPoint::merge( $globe_meta, $settings );
	$globe_meta     = Base\MetaDotLabel::merge( $globe_meta, $settings );
	$globe_options  = Base\Options::get( $settings );
	$globe_features = Base\Features::get();
	$globe_model    = array_merge(
		$globe_model,
		[
			'labels'   => $globe_labels,
			'options'  => $globe_options,
			'meta'     => $globe_meta,
			'features' => $globe_features,
		]
	);

	$globe_model = apply_filters( 'itt_globes/globe/model', $globe_model );

	return $globe_model;
}

return itt_globe_model_free();
