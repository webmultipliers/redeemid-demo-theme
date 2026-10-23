<?php
/**
 * Shopify's WordPress plugin, when it is active beside WP2.
 *
 * Its cart toggle is placed in the header's account group (parts/header.html)
 * instead of the row Shopify's plugin hooks in after the navigation. The 1.0.0
 * toggle is an <a> with no href, which a keyboard can't reach, so it gets a
 * button role and a tab stop, and Enter or Space opens the cart.
 *
 * Without Shopify's plugin none of this runs and its blocks render nothing.
 *
 * @package sfwp-test
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'render_block_create-block/shopify-cart-toggle-block',
	static function ( string $html ): string {
		$tags = new WP_HTML_Tag_Processor( $html );

		while ( $tags->next_tag( [ 'tag_name' => 'A', 'class_name' => 'navigation__view-cart-link' ] ) ) {
			if ( null === $tags->get_attribute( 'href' ) ) {
				$tags->set_attribute( 'role', 'button' );
				$tags->set_attribute( 'tabindex', '0' );
				$tags->set_attribute( 'aria-haspopup', 'dialog' );
			}
		}

		return $tags->get_updated_html();
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		if ( ! defined( 'SHOPIFY_WP_VERSION' ) ) {
			return;
		}

		wp_register_script( 'sfwp-test-cart-toggle', '', [], SFWP_TEST_VERSION, [ 'in_footer' => true ] );
		wp_enqueue_script( 'sfwp-test-cart-toggle' );
		wp_add_inline_script(
			'sfwp-test-cart-toggle',
			"document.addEventListener('keydown',function(e){var t=e.target;if((e.key==='Enter'||e.key===' ')&&t.matches&&t.matches('a.navigation__view-cart-link[role=\"button\"]')){e.preventDefault();t.click();}});"
		);
	}
);
