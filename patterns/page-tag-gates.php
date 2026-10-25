<?php
/**
 * Title: Tag gates (page)
 * Slug: redeemid-demo/page-tag-gates
 * Categories: redeemid-demo
 * Post Types: page
 * Description: Membership gates with different Shopify tag rules and fallbacks.
 */
?>
<!-- wp:paragraph -->
<p>Each box is one Membership gate. Tag your test customer in Shopify (Customers → the customer → Tags), wait for the <code>customer.tags_added</code> webhook or sign in again, and reload. If your store uses other tag names, edit the gates; the rules are in the block settings.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">1. Any of: gold-member</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Customers tagged gold-member see the members area. Others see a message, a “Become a member” button and, for guests, sign-in.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"message","label":"A message and a call to action"},"fallbackMessage":"This box is for gold members.","fallbackCtaLabel":"Become a member","fallbackCtaUrl":{"url":"/join/"},"showSignIn":true} -->
	<!-- wp:redeemid/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Gold confirmed. Marker: <code>LEDGER-GOLD</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:redeemid/membership-gate-content -->
	<!-- wp:redeemid/membership-gate-fallback /-->
	<!-- /wp:redeemid/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">2. All of: gold-member and silver-member</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Needs both tags. With only gold-member, box 1 opens and this one stays locked.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/membership-gate {"requiredTags":[{"tag":"gold-member"},{"tag":"silver-member"}],"matchMode":{"value":"all","label":"All of these tags"},"requireLogin":true,"fallbackMode":{"value":"message","label":"A message and a call to action"},"fallbackMessage":"This box needs both gold-member and silver-member.","fallbackCtaLabel":"","showSignIn":true} -->
	<!-- wp:redeemid/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Both tags confirmed. Marker: <code>LEDGER-GOLD-AND-SILVER</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:redeemid/membership-gate-content -->
	<!-- wp:redeemid/membership-gate-fallback /-->
	<!-- /wp:redeemid/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">3. Any of: gold-member, with a teaser fallback</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Non-members see the Public teaser area instead of a message. Members see only the members area, never the teaser.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"teaser-blocks","label":"The Public teaser area"}} -->
	<!-- wp:redeemid/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Full version for gold members. Marker: <code>LEDGER-GOLD-FULL</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:redeemid/membership-gate-content -->
	<!-- wp:redeemid/membership-gate-fallback -->
	<!-- wp:paragraph -->
	<p>Teaser for everyone else. <a href="/join/">Become a member</a> to read the full version.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:redeemid/membership-gate-fallback -->
	<!-- /wp:redeemid/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">4. Negative control: a tag nobody has</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Needs ledger-nobody-has-this. Should stay locked for everyone, administrators included.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/membership-gate {"requiredTags":[{"tag":"ledger-nobody-has-this"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"message","label":"A message and a call to action"},"fallbackMessage":"Locked for everyone, as expected.","fallbackCtaLabel":"","showSignIn":false} -->
	<!-- wp:redeemid/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>If you can read this, the gate failed open. Marker: <code>LEDGER-SHOULD-NEVER-SHOW</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:redeemid/membership-gate-content -->
	<!-- wp:redeemid/membership-gate-fallback /-->
	<!-- /wp:redeemid/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">5. Open control: no rule</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">No tags and sign-in not required, so everyone, guests included, sees the members area.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/membership-gate {"requireLogin":false,"fallbackMode":{"value":"message","label":"A message and a call to action"}} -->
	<!-- wp:redeemid/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Open to everyone. Marker: <code>LEDGER-OPEN</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:redeemid/membership-gate-content -->
	<!-- wp:redeemid/membership-gate-fallback /-->
	<!-- /wp:redeemid/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">6. Shopify’s own product block inside a gate</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Needs Shopify’s WordPress plugin. Gold members see the product block and the marker. Everyone else sees a plan card, and the page source holds neither the marker nor a <code>&lt;shopify-context type="product"&gt;</code> element (the header’s cart toggle has a <code>type="cart"</code> one on every page). Without Shopify’s plugin, the product block renders nothing but the marker still shows to gold members.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"teaser-blocks","label":"The Public teaser area"}} -->
	<!-- wp:redeemid/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Gold product unlocked. Marker: <code>LEDGER-SECRET-PRODUCT</code>.</p>
	<!-- /wp:paragraph -->
	<!-- wp:shopify-product/shopify-product-block {"selectedProduct":{"handle":"ledger-gated-product","title":"Gold-only product"}} /-->
	<!-- /wp:redeemid/membership-gate-content -->
	<!-- wp:redeemid/membership-gate-fallback -->
	<!-- wp:paragraph -->
	<p>This product is for gold members.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/plan-card {"productHandle":{"value":"gold-membership","label":"Gold membership"},"heading":"Gold","buttonLabel":"Join Gold"} /-->
	<!-- /wp:redeemid/membership-gate-fallback -->
	<!-- /wp:redeemid/membership-gate -->
</div>
<!-- /wp:group -->
