<?php
/**
 * Title: Become a member (page)
 * Slug: redeemid-demo/page-join
 * Categories: redeemid-demo
 * Post Types: page
 * Description: Where every paywall points: what each membership opens, the plan cards, and for testers how a test customer gets a membership tag.
 */

$ledger_rows = [
	// What, Free, Account, Silver, Gold.
	[ __( 'City Hall, transit, science and most news', 'redeemid-demo' ), 1, 1, 1, 1 ],
	[ __( 'Corrections and most city guides', 'redeemid-demo' ), 1, 1, 1, 1 ],
	[ __( 'Every briefing and our election coverage', 'redeemid-demo' ), 0, 1, 1, 1 ],
	[ __( 'The editorial board', 'redeemid-demo' ), 0, 1, 1, 1 ],
	[ __( 'Culture desk, features, the arts briefing and Fennel Hill guides', 'redeemid-demo' ), 0, 0, 1, 0 ],
	[ __( 'Investigations and Ledger Reports', 'redeemid-demo' ), 0, 0, 0, 1 ],
];
?>
<!-- wp:group {"className":"ledger-join-hero","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-join-hero">
	<!-- wp:paragraph {"className":"ledger-kicker"} -->
	<p class="ledger-kicker">Membership</p>
	<!-- /wp:paragraph -->
	<!-- wp:heading {"level":2,"className":"ledger-join-hero__title"} -->
	<h2 class="wp-block-heading ledger-join-hero__title">Independent reporting for the harbor, paid for by its readers</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-join-hero__lede"} -->
	<p class="ledger-join-hero__lede">Most of the Ledger is free and stays free. A free account adds the briefings and election coverage. Silver opens our culture desk; Gold opens the investigations and Ledger Reports that take months. Take both and you read everything.</p>
	<!-- /wp:paragraph -->
	<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-group">
		<!-- wp:redeemid/auth-action {"mode":{"value":"login","label":"Log in only (hidden when signed in)"}} /-->
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size">Already a member? Sign in with the email you used at checkout.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:redeemid/membership-gate {"ruleSource":{"value":"level","label":"An access level"},"level":{"value":"gold","label":"Gold"},"fallbackMode":{"value":"teaser-blocks","label":"The Public teaser area"},"showSignIn":false} -->
<!-- wp:redeemid/membership-gate-content -->
<!-- wp:group {"className":"ledger-join-member","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-join-member">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">You're a Gold member</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph -->
	<p>Thanks for supporting the Ledger. Your account page shows when you joined, when you renew and how to manage your subscription.</p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons -->
	<div class="wp-block-buttons"><!-- wp:button -->
	<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/account/">Your membership</a></div>
	<!-- /wp:button --></div>
	<!-- /wp:buttons -->
	<!-- wp:redeemid/plan-card {"productHandle":{"value":"silver-membership","label":"Silver membership"},"heading":"Add Silver","buttonLabel":"Add Silver"} /-->
</div>
<!-- /wp:group -->
<!-- /wp:redeemid/membership-gate-content -->
<!-- wp:redeemid/membership-gate-fallback -->
<!-- wp:columns {"align":"wide","className":"ledger-plans"} -->
<div class="wp-block-columns alignwide ledger-plans">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:redeemid/plan-card {"productHandle":{"value":"gold-membership","label":"Gold membership"},"heading":"Gold","buttonLabel":"Join Gold","highlight":true} /-->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:redeemid/plan-card {"productHandle":{"value":"silver-membership","label":"Silver membership"},"heading":"Silver","buttonLabel":"Join Silver"} /-->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->
<!-- /wp:redeemid/membership-gate-fallback -->
<!-- /wp:redeemid/membership-gate -->

<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Member-aware: Gold members see a thank-you, a link to their account and the Silver add-on instead of the plan cards; everyone else sees both plans. A plan card can sell a subscription: put a selling plan ID in its settings (the editor lists the product's plans), or leave it empty on a subscription-only product.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"className":"ledger-section-title"} -->
<h2 class="wp-block-heading ledger-section-title">What each membership opens</h2>
<!-- /wp:heading -->

<!-- wp:table {"className":"ledger-compare"} -->
<figure class="wp-block-table ledger-compare"><table><thead><tr><th></th><th><span class="redeemid-access-badge" data-redeemid-level="free">Free</span></th><th><span class="redeemid-access-badge" data-redeemid-level="signed-in">Signed in</span></th><th><span class="redeemid-access-badge" data-redeemid-level="silver">Silver</span></th><th><span class="redeemid-access-badge" data-redeemid-level="gold">Gold</span></th></tr></thead><tbody><?php foreach ( $ledger_rows as $ledger_row ) : ?><tr><td><?php echo esc_html( $ledger_row[0] ); ?></td><?php foreach ( array_slice( $ledger_row, 1 ) as $ledger_yes ) : ?><td class="<?php echo $ledger_yes ? 'is-yes' : 'is-no'; ?>"><?php echo $ledger_yes ? '✓' : '—'; ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table><figcaption class="wp-element-caption">Gold + Silver together opens every story, including the ones marked Gold + Silver. Ledger Reports open to everyone a year after publication.</figcaption></figure>
<!-- /wp:table -->

<!-- wp:group {"className":"redeemid-demoers","layout":{"type":"constrained"}} -->
<div class="wp-block-group redeemid-demoers">
<!-- wp:heading {"className":"ledger-section-title"} -->
<h2 class="wp-block-heading ledger-section-title">For testers: getting a membership tag</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Membership on the Harborview Ledger is a Shopify customer tag: <code>silver-member</code> opens Silver stories, <code>gold-member</code> opens Gold stories and both together open everything. Signed-in stories need only an account. There are three ways to get a tag for testing, or switch to one of the test accounts listed in the <a href="/test-lab/">Test lab</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Buy a plan</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Each card shows the live Shopify price and goes straight to checkout, signed in when you are. It works without JavaScript and whether or not Shopify’s own plugin is installed. The cards expect products with the handles <code>gold-membership</code> and <code>silver-membership</code>, published to the Headless channel, and a Storefront token in RedeemID → Settings. A Flow workflow (below) tags the buyer so paying grants the level.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Tag the customer by hand</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In the Shopify admin, open Customers, pick your test customer and add the tag <code>gold-member</code> (and <code>silver-member</code> for the “all of” box). Shopify sends <code>customer.tags_added</code>; once the Shopify admin’s webhook log shows the delivery, reload the gated page. “Refresh membership” below (and in the header) pulls the tags straight away; signing in again does too.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Buy a membership product</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In Shopify Flow, add a workflow that tags the customer <code>gold-member</code> when an order for the Gold product is paid, and one that removes it on cancellation (the plugin’s <code>docs/shopify-flow.md</code> has the recipes). Buy it with the test customer (Shopify’s Bogus Gateway works on a dev store). The tag webhook unlocks Gold within seconds, or press Refresh Membership.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/account-status /-->

<!-- wp:redeemid/membership-refresh /-->

<!-- wp:paragraph -->
<p><a href="/tag-gates/">Back to Tag gates</a></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
