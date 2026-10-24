<?php
/**
 * Title: Become a member (page)
 * Slug: sfwp-test/page-join
 * Categories: sfwp-test
 * Post Types: page
 * Description: Where every paywall points: what each membership opens, the plan cards, and for testers how a test customer gets a membership tag.
 */

$sfwp_rows = [
	// What, Free, Account, Silver, Gold.
	[ __( 'City Hall, transit, science and most news', 'sfwp-test' ), 1, 1, 1, 1 ],
	[ __( 'Corrections and most city guides', 'sfwp-test' ), 1, 1, 1, 1 ],
	[ __( 'Every briefing and our election coverage', 'sfwp-test' ), 0, 1, 1, 1 ],
	[ __( 'The editorial board', 'sfwp-test' ), 0, 1, 1, 1 ],
	[ __( 'Culture desk, features, the arts briefing and Fennel Hill guides', 'sfwp-test' ), 0, 0, 1, 0 ],
	[ __( 'Investigations and Ledger Reports', 'sfwp-test' ), 0, 0, 0, 1 ],
];
?>
<!-- wp:group {"className":"sfwp-join-hero","layout":{"type":"constrained"}} -->
<div class="wp-block-group sfwp-join-hero">
	<!-- wp:paragraph {"className":"sfwp-kicker"} -->
	<p class="sfwp-kicker">Membership</p>
	<!-- /wp:paragraph -->
	<!-- wp:heading {"level":2,"className":"sfwp-join-hero__title"} -->
	<h2 class="wp-block-heading sfwp-join-hero__title">Independent reporting for the harbor, paid for by its readers</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"sfwp-join-hero__lede"} -->
	<p class="sfwp-join-hero__lede">Most of the Ledger is free and stays free. A free account adds the briefings and election coverage. Silver opens our culture desk; Gold opens the investigations and Ledger Reports that take months. Take both and you read everything.</p>
	<!-- /wp:paragraph -->
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group">
		<!-- wp:wp2-shopify/auth-action {"mode":{"value":"login","label":"Log in only (hidden when signed in)"}} /-->
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size">Already a member? Sign in with the email you used at checkout.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"sfwp-plans"} -->
<div class="wp-block-columns alignwide sfwp-plans">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:wp2-shopify/plan-card {"productHandle":{"value":"gold-membership","label":"Gold membership"},"heading":"Gold","buttonLabel":"Join Gold","highlight":true} /-->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:wp2-shopify/plan-card {"productHandle":{"value":"silver-membership","label":"Silver membership"},"heading":"Silver","buttonLabel":"Join Silver"} /-->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:heading {"className":"sfwp-section-title"} -->
<h2 class="wp-block-heading sfwp-section-title">What each membership opens</h2>
<!-- /wp:heading -->

<!-- wp:table {"className":"sfwp-compare"} -->
<figure class="wp-block-table sfwp-compare"><table><thead><tr><th></th><th><span class="wp2-shopify-access-badge" data-wp2-level="free">Free</span></th><th><span class="wp2-shopify-access-badge" data-wp2-level="signed-in">Signed in</span></th><th><span class="wp2-shopify-access-badge" data-wp2-level="silver">Silver</span></th><th><span class="wp2-shopify-access-badge" data-wp2-level="gold">Gold</span></th></tr></thead><tbody><?php foreach ( $sfwp_rows as $sfwp_row ) : ?><tr><td><?php echo esc_html( $sfwp_row[0] ); ?></td><?php foreach ( array_slice( $sfwp_row, 1 ) as $sfwp_yes ) : ?><td class="<?php echo $sfwp_yes ? 'is-yes' : 'is-no'; ?>"><?php echo $sfwp_yes ? '✓' : '—'; ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table><figcaption class="wp-element-caption">Gold + Silver together opens every story, including the ones marked Gold + Silver. Ledger Reports open to everyone a year after publication.</figcaption></figure>
<!-- /wp:table -->

<!-- wp:group {"className":"sfwp-testers","layout":{"type":"constrained"}} -->
<div class="wp-block-group sfwp-testers">
<!-- wp:heading {"className":"sfwp-section-title"} -->
<h2 class="wp-block-heading sfwp-section-title">For testers: getting a membership tag</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Membership on the Harborview Ledger is a Shopify customer tag: <code>silver-member</code> opens Silver stories, <code>gold-member</code> opens Gold stories and both together open everything. Signed-in stories need only an account. There are three ways to get a tag for testing, or switch to one of the test accounts listed in the <a href="/test-lab/">Test lab</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Buy a plan</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"sfwp-expect"} -->
<p class="sfwp-expect">Each card shows the live Shopify price and goes straight to checkout, signed in when you are. It works without JavaScript and whether or not Shopify’s own plugin is installed. The cards expect products with the handles <code>gold-membership</code> and <code>silver-membership</code>, published to the Headless channel, and <code>WP2_SHOPIFY_PUBLIC_ACCESS_TOKEN</code> in wp-config. Map each product to its tag (below) so paying grants it.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Tag the customer by hand</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In the Shopify admin, open Customers, pick your test customer and add the tag <code>gold-member</code> (and <code>silver-member</code> for the “all of” box). Shopify sends <code>customer.tags_added</code>; once the Shopify admin’s webhook log shows the delivery, reload the gated page. “Refresh membership” below (and in the header) pulls the tags straight away; signing in again does too.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Buy a mapped product</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Map a product to a tag in the Shopify admin menu, for example <code>1234567890: gold-member</code>, and buy it with the test customer (Shopify’s Bogus Gateway works on a dev store). On <code>orders/paid</code> the plugin adds the tag in Shopify. A refund or cancellation removes it. After checkout, your next signed-in page views also re-sync for up to 30 minutes.</p>
<!-- /wp:paragraph -->

<!-- wp:wp2-shopify/account-status /-->

<!-- wp:wp2-shopify/membership-refresh /-->

<!-- wp:paragraph -->
<p><a href="/tag-gates/">Back to Tag gates</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
