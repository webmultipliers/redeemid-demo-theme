<?php
/**
 * Title: Account (page)
 * Slug: sfwp-test/page-account
 * Categories: sfwp-test
 * Post Types: page
 * Description: The member's account card, then the other account blocks: membership features, connect, log out and the visitor state.
 */
?>
<!-- wp:wp2-shopify/member-account {"title":"Your membership","showFeatures":true,"joinUrl":"/join/"} /-->

<!-- wp:paragraph {"className":"sfwp-expect"} -->
<p class="sfwp-expect">Shows your levels and when each started (from the <code>membership.member_since</code> metafield, or when WordPress first saw the tag), the renewal date when Flow writes <code>membership.renews_on</code>, levels that ended, your features, when it last synced with a refresh button, and links to your Shopify account and subscription management. Guests get a log-in button, unlinked accounts a connect button and lapsed members a link to Join. Bruno Banned (disabled in Shopify) is told to contact the store.</p>
<!-- /wp:paragraph -->

<!-- wp:wp2-shopify/member-features {"title":"Your membership features","showMissing":true} /-->

<!-- wp:paragraph {"className":"sfwp-expect"} -->
<p class="sfwp-expect">Lists the features you hold and where each comes from (“Ad-free reading, from Gold”), then the ones you don’t, marked “not included”. Ada Add-on holds Ad-free reading from the add-on, not from Gold.</p>
<!-- /wp:paragraph -->

<!-- wp:wp2-shopify/account-status {"title":"Visitor state","showRaw":true} /-->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Connect an existing WordPress account</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"sfwp-expect"} -->
<p class="sfwp-expect">Shows only to a signed-in WordPress user who is not linked. The Shopify email must be verified and match the WordPress email; you then confirm on the callback page. Administrators are refused.</p>
<!-- /wp:paragraph -->

<!-- wp:wp2-shopify/auth-action {"mode":{"value":"connect","label":"Connect only (signed-in, unlinked accounts)"},"connectLabel":"Connect my Shopify account"} /-->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Sign out</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"sfwp-expect"} -->
<p class="sfwp-expect">Ends the WordPress session and the Shopify session together. WordPress’s own logout link ends only the WordPress session.</p>
<!-- /wp:paragraph -->

<!-- wp:wp2-shopify/auth-action {"mode":{"value":"logout","label":"Log out only (hidden for guests)"},"redirectAfterLogout":{"url":"/"}} /-->
