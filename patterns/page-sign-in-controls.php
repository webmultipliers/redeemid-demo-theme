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
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">Auto, button</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Guest: Log in. Linked or shadow user: Log out. Signed-in WordPress user without Shopify: Connect.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">Auto, link style</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Same as above, rendered as a text link.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action {"controlStyle":{"value":"link","label":"Link"}} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">Log in only, then go to the members area</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Guest: “Sign in and open the members area”, which lands on /members-area/ after Shopify. Signed in: hidden.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action {"mode":{"value":"login","label":"Log in only (hidden when signed in)"},"loginLabel":"Sign in and open the members area","redirectAfterLogin":{"url":"/members-area/"}} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">Log out only, then go to the Test lab</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Guest: nothing. Linked or shadow user: “Sign out of both”, which ends the Shopify session and returns to the Test lab.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action {"mode":{"value":"logout","label":"Log out only (hidden for guests)"},"logoutLabel":"Sign out of both","redirectAfterLogout":{"url":"/"}} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"ledger-case","layout":{"type":"constrained"}} -->
<div class="wp-block-group ledger-case">
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">Connect only</h3>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"ledger-expect"} -->
	<p class="ledger-expect">Shows only for a signed-in WordPress user who is not linked yet. Everyone else sees nothing.</p>
	<!-- /wp:paragraph -->
	<!-- wp:redeemid/auth-action {"mode":{"value":"connect","label":"Connect only (signed-in, unlinked accounts)"}} /-->
</div>
<!-- /wp:group -->

<!-- wp:redeemid/account-status /-->
