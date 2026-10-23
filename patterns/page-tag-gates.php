<?php
/**
 * Title: Tag gates (page)
 * Slug: sfwp-test/page-tag-gates
 * Categories: sfwp-test
 * Post Types: page
 * Description: Membership gates with different Shopify tag rules and fallbacks.
 */
?>
<!-- wp:paragraph -->
<p>Each box is one Membership gate. Tag your test customer in Shopify (Customers → the customer → Tags), wait for the <code>customer.tags_added</code> webhook or sign in again, and reload. If your store uses other tag names, edit the gates; the rules are in the block settings.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"sfwp-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group sfwp-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">1. Any of: gold-member</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"sfwp-expect"} -->
	<p class="sfwp-expect">Customers tagged gold-member see the members area. Others see a message, a “Become a member” button and, for guests, sign-in.</p>
	<!-- /wp:paragraph -->
	<!-- wp:wp2-shopify/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"message","label":"A message and a call to action"},"fallbackMessage":"This box is for gold members.","fallbackCtaLabel":"Become a member","fallbackCtaUrl":"/join/","showSignIn":true} -->
	<!-- wp:wp2-shopify/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Gold confirmed. Marker: <code>SFWP-GOLD</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:wp2-shopify/membership-gate-content -->
	<!-- wp:wp2-shopify/membership-gate-fallback /-->
	<!-- /wp:wp2-shopify/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"sfwp-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group sfwp-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">2. All of: gold-member and silver-member</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"sfwp-expect"} -->
	<p class="sfwp-expect">Needs both tags. With only gold-member, box 1 opens and this one stays locked.</p>
	<!-- /wp:paragraph -->
	<!-- wp:wp2-shopify/membership-gate {"requiredTags":[{"tag":"gold-member"},{"tag":"silver-member"}],"matchMode":{"value":"all","label":"All of these tags"},"requireLogin":true,"fallbackMode":{"value":"message","label":"A message and a call to action"},"fallbackMessage":"This box needs both gold-member and silver-member.","fallbackCtaLabel":"","showSignIn":true} -->
	<!-- wp:wp2-shopify/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Both tags confirmed. Marker: <code>SFWP-GOLD-AND-SILVER</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:wp2-shopify/membership-gate-content -->
	<!-- wp:wp2-shopify/membership-gate-fallback /-->
	<!-- /wp:wp2-shopify/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"sfwp-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group sfwp-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">3. Any of: gold-member, with a teaser fallback</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"sfwp-expect"} -->
	<p class="sfwp-expect">Non-members see the Public teaser area instead of a message. Members see only the members area, never the teaser.</p>
	<!-- /wp:paragraph -->
	<!-- wp:wp2-shopify/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"teaser-blocks","label":"The Public teaser area"}} -->
	<!-- wp:wp2-shopify/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Full version for gold members. Marker: <code>SFWP-GOLD-FULL</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:wp2-shopify/membership-gate-content -->
	<!-- wp:wp2-shopify/membership-gate-fallback -->
	<!-- wp:paragraph -->
	<p>Teaser for everyone else. <a href="/join/">Become a member</a> to read the full version.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:wp2-shopify/membership-gate-fallback -->
	<!-- /wp:wp2-shopify/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"sfwp-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group sfwp-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">4. Negative control: a tag nobody has</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"sfwp-expect"} -->
	<p class="sfwp-expect">Needs sfwp-nobody-has-this. Should stay locked for everyone, administrators included.</p>
	<!-- /wp:paragraph -->
	<!-- wp:wp2-shopify/membership-gate {"requiredTags":[{"tag":"sfwp-nobody-has-this"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"message","label":"A message and a call to action"},"fallbackMessage":"Locked for everyone, as expected.","fallbackCtaLabel":"","showSignIn":false} -->
	<!-- wp:wp2-shopify/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>If you can read this, the gate failed open. Marker: <code>SFWP-SHOULD-NEVER-SHOW</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:wp2-shopify/membership-gate-content -->
	<!-- wp:wp2-shopify/membership-gate-fallback /-->
	<!-- /wp:wp2-shopify/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"sfwp-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group sfwp-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">5. Open control: no rule</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"sfwp-expect"} -->
	<p class="sfwp-expect">No tags and sign-in not required, so everyone, guests included, sees the members area.</p>
	<!-- /wp:paragraph -->
	<!-- wp:wp2-shopify/membership-gate {"requireLogin":false,"fallbackMode":{"value":"message","label":"A message and a call to action"}} -->
	<!-- wp:wp2-shopify/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Open to everyone. Marker: <code>SFWP-OPEN</code>.</p>
	<!-- /wp:paragraph -->
	<!-- /wp:wp2-shopify/membership-gate-content -->
	<!-- wp:wp2-shopify/membership-gate-fallback /-->
	<!-- /wp:wp2-shopify/membership-gate -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"sfwp-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group sfwp-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">6. Shopify’s own product block inside a gate</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"sfwp-expect"} -->
	<p class="sfwp-expect">Needs Shopify’s WordPress plugin. Gold members see the product block and the marker. Everyone else sees a plan card, and the page source holds neither the marker nor the product handle <code>sfwp-gated-product</code>. Without Shopify’s plugin, the product block renders nothing but the marker still shows to gold members.</p>
	<!-- /wp:paragraph -->
	<!-- wp:wp2-shopify/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"teaser-blocks","label":"The Public teaser area"}} -->
	<!-- wp:wp2-shopify/membership-gate-content -->
	<!-- wp:paragraph -->
	<p>Gold product unlocked. Marker: <code>SFWP-SECRET-PRODUCT</code>.</p>
	<!-- /wp:paragraph -->
	<!-- wp:shopify-product/shopify-product-block {"selectedProduct":{"handle":"sfwp-gated-product","title":"Gold-only product"}} /-->
	<!-- /wp:wp2-shopify/membership-gate-content -->
	<!-- wp:wp2-shopify/membership-gate-fallback -->
	<!-- wp:paragraph -->
	<p>This product is for gold members.</p>
	<!-- /wp:paragraph -->
	<!-- wp:wp2-shopify/plan-card {"productHandle":"gold-membership","heading":"Gold","buttonLabel":"Join Gold"} /-->
	<!-- /wp:wp2-shopify/membership-gate-fallback -->
	<!-- /wp:wp2-shopify/membership-gate -->
</div>
<!-- /wp:group -->
