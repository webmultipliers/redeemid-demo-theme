<?php
/**
 * Title: Sign-in controls (page)
 * Slug: redeemid-demo/page-sign-in-controls
 * Categories: redeemid-demo
 * Post Types: page
 * Description: The Shopify sign-in block in every mode and style.
 */
?>
<!-- wp:paragraph -->
<p>Each row is one <code>redeemid/auth-action</code> block. The server sends the same guest markup to everyone, so the page can be cached; after load, one request to the status endpoint swaps in the right control. With JavaScript off, only the log in links show.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading -->
	<h2 class="wp-block-heading">Auto, button</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Guest: Sign in. Linked or shadow user: Sign out. Signed-in WordPress user who isn’t linked: Confirm your email.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading -->
	<h2 class="wp-block-heading">Auto, link style</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Same as above, rendered as a text link.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action {"controlStyle":{"value":"link","label":"Link"}} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading -->
	<h2 class="wp-block-heading">Sign in only, then go to the members area</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Guest: “Sign in and open the members area”, which lands on /members-area/ after Shopify. Signed in: hidden.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action {"mode":{"value":"login","label":"Log in only (hidden when signed in)"},"loginLabel":"Sign in and open the members area","redirectAfterLogin":{"url":"/members-area/"}} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading -->
	<h2 class="wp-block-heading">Sign out only, then go to Demo config</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Guest: nothing. Linked or shadow user: “Sign out”, which ends the Shopify session too and returns to Demo config.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action {"mode":{"value":"logout","label":"Log out only (hidden for guests)"},"logoutLabel":"Sign out","redirectAfterLogout":{"url":"/demo-config/"}} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading -->
	<h2 class="wp-block-heading">Confirm your email only</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Shows only for a signed-in WordPress user who is not linked yet. Everyone else sees nothing.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action {"mode":{"value":"connect","label":"Connect only (signed-in, unlinked accounts)"}} /-->
</div>
<!-- /wp:group -->

<!-- wp:redeemid/account-status /-->
