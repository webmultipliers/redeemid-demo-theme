<?php
/**
 * Title: Members-only article (post)
 * Slug: redeemid-demo/post-members-only-article
 * Categories: redeemid-demo
 * Post Types: post
 * Description: A paywalled post with a public intro, a teaser and a secret phrase for leak checks.
 */
?>
<!-- wp:paragraph -->
<p>This post is the paywall leak check. Everyone can read this introduction. The rest is for customers tagged <code>gold-member</code>.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"teaser-blocks","label":"The Public teaser area"}} -->
<!-- wp:redeemid/membership-gate-content -->
<!-- wp:heading -->
<h2 class="wp-block-heading">The members-only part</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>The secret phrase is LEDGER-SECRET-MEMBERS-ONLY. It must never appear in the page source, the RSS feed, the REST API or a search excerpt for anyone who is not a gold member.</p>
<!-- /wp:paragraph -->
<!-- wp:list -->
<ul>
	<!-- wp:list-item -->
	<li>Members-only list item one.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li>Members-only list item two.</li>
	<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->
<!-- /wp:redeemid/membership-gate-content -->
<!-- wp:redeemid/membership-gate-fallback -->
<!-- wp:paragraph -->
<p><strong>Keep reading as a member.</strong> The rest of this article is for gold members. <a href="/join/">Become a member</a>, or sign in if you already are.</p>
<!-- /wp:paragraph -->
<!-- wp:redeemid/auth-action {"mode":{"value":"login","label":"Log in only (hidden when signed in)"}} /-->
<!-- /wp:redeemid/membership-gate-fallback -->
<!-- /wp:redeemid/membership-gate -->

<!-- wp:paragraph {"className":"ledger-note"} -->
<p class="ledger-note">Leak checks: view source, <a href="/feed/">RSS</a>, <a href="/wp-json/wp/v2/posts?slug=members-only-article">REST</a>, <a href="/?s=LEDGER-SECRET">search</a>.</p>
<!-- /wp:paragraph -->
