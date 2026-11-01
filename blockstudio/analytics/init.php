<?php
/**
 * Marks this folder as a Blockstudio code snippet, so `global-fathom.view.js`
 * loads on every front-end page with no block involved.
 *
 * The script listens to the plugin's browser client, which loads only when
 * something asks for it, so this asks for it. Without the plugin the handle
 * isn't registered and WordPress skips it.
 *
 * @package redeemid-demo
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_script( 'redeemid-client' );
	}
);
