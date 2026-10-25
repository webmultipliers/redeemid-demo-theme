<?php
/**
 * Demo features for the Ledger, consumed through the plugin's feature
 * registry (see the plugin's docs/features.md). The theme only registers
 * them and places the plugin's blocks; who holds what is decided by the
 * plugin from the synced mirror.
 *
 * - ad-free: "Ad-free reading". Gold, or a standalone add-on tag
 *   (ad-free-addon) that stacks with any tier. Mapped in admin
 *   (RedeemID → Access → Features), seeded once here, so the mapping screen
 *   has something to edit. Enforced by block visibility (the ad slots in
 *   the story and home templates) and the body class; it also opts in to
 *   the JS signal for the Test lab's check.
 * - early-briefing: "Early briefing". Silver or Gold, set in code, so the
 *   Features tab shows it as managed in code. Enforced by block visibility
 *   on the Briefings archive.
 *
 * Both are behaviour-only and cache as private: pages that use them are
 * never shared-cached for signed-in visitors.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'redeemid_features',
	static function ( array $features ): array {
		$features['ad-free'] = [
			'label'       => __( 'Ad-free reading', 'redeemid-demo' ),
			'description' => __( 'Hides the sponsor slots in stories and on the home page.', 'redeemid-demo' ),
			'enforcement' => [ 'block', 'body' ],
			'cache'       => 'private',
			'primary'     => true,
			'js'          => true,
		];

		$features['early-briefing'] = [
			'label'       => __( 'Early briefing', 'redeemid-demo' ),
			'description' => __( 'Tomorrow morning’s briefing the evening before, on the Briefings page.', 'redeemid-demo' ),
			'enforcement' => [ 'block' ],
			'cache'       => 'private',
			'primary'     => true,
			'condition'   => "silver\ngold",
		];

		return $features;
	}
);

/**
 * Map ad-free to Gold or the add-on tag, unless an administrator already
 * mapped it. With $restore, put the demo mapping back.
 */
function redeemid_demo_seed_features( bool $restore ): void {
	if ( ! class_exists( \RedeemID\Features\Registry::class ) ) {
		return;
	}

	if ( ! $restore && isset( \RedeemID\Features\Registry::stored()['ad-free'] ) ) {
		return;
	}

	\RedeemID\Features\Registry::flush();
	\RedeemID\Features\Registry::save( 'ad-free', \RedeemID\Features\Route::parse( "gold\nAd-free add-on = tag:ad-free-addon" ) );
}
