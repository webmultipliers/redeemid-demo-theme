<?php
/**
 * Title: Test lab (page)
 * Slug: sfwp-test/page-test-lab
 * Categories: sfwp-test
 * Post Types: page
 * Description: The sandbox landing page: health, visitor state and a list of things to try.
 */
?>
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">This sandbox runs the WP2 Shopify plugin against a real Shopify store. Each page in the header tests one piece of it. Start with the two panels, then work down the list.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:sfwp-test/sandbox-health /-->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:sfwp-test/visitor-state /-->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What to try</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"sfwp-steps"} -->
<ol class="sfwp-steps">
	<!-- wp:list-item -->
	<li><strong>Sign in.</strong> Use “Log in with Shopify” in the header. Shopify should send you to <a href="/auth-callback/">/auth-callback/</a> and then back here. The Visitor state panel should say you are signed in, linked, and (for a new customer) a shadow user.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Check every control.</strong> <a href="/sign-in-controls/">Sign-in controls</a> shows the block in each mode. The page HTML is the same for everyone; only the hydrated control changes.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Open the members area.</strong> <a href="/members-area/">Members area</a> releases its content to any signed-in visitor. As a guest you should see a message and a sign-in button.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Test tag rules.</strong> <a href="/tag-gates/">Tag gates</a> has gates for <code>gold-member</code>, <code>gold-member</code> + <code>silver-member</code>, a teaser fallback, a tag nobody has, and an open control. Add or remove the tags on your customer in Shopify (or buy a mapped product, see <a href="/join/">Become a member</a>) and reload.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Try to leak the paywalled post.</strong> As a guest, open <a href="/members-only-article/">the members article</a>, view its source, its <a href="/feed/">RSS</a>, its <a href="/wp-json/wp/v2/posts?slug=members-only-article">REST response</a> and <a href="/?s=SFWP-SECRET">a search for the secret phrase</a>. The phrase <code>SFWP-SECRET-MEMBERS-ONLY</code> must not appear anywhere unless you are a <code>gold-member</code>.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Redirect mode.</strong> <a href="/gate-redirect/">Redirect gate</a> sends non-members to the Become a member page.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Link an existing account.</strong> Sign in with a plain WordPress (non-admin) user whose email matches your Shopify customer, then use “Connect Shopify account” on the <a href="/account/">Account</a> page. Administrators are refused on purpose.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Sign out.</strong> “Log out” should end both the WordPress and the Shopify session. Signing in again should ask Shopify for a code.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Admin side.</strong> In <a href="/wp-admin/options-general.php?page=wp2-shopify">Settings → Shopify</a>: register webhooks, check Diagnostics, and use the dry run to see the gate decision for any user.</li>
	<!-- /wp:list-item -->
</ol>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"sfwp-note"} -->
<p class="sfwp-note">Coming in later plugin phases, so not testable yet: the account card, the member menu, cart count and catalog blocks.</p>
<!-- /wp:paragraph -->
