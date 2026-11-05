<?php
/**
 * Title: Account (page)
 * Slug: redeemid-demo/page-account
 * Categories: redeemid-demo
 * Post Types: page
 * Description: The member's account card (with the privacy request links), then the other account blocks: membership features, confirm your email, sign out and the visitor state.
 */
?>
<!-- wp:redeemid/member-account {"title":"Your membership","showFeatures":true,"joinUrl":"/join/","showPrivacyLinks":true} /-->

<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Shows your levels and when each started (from the <code>membership.member_since</code> metafield, or when WordPress first saw the tag), the renewal date when Flow writes <code>membership.renews_on</code>, levels that ended, your features, when it last synced with a refresh button, and links to your Shopify account and subscription management. Guests get a log-in button, unlinked accounts a connect button and lapsed members a link to Join. Bruno Banned (disabled in Shopify) is told to contact the store. At the bottom, Download my data and Delete my data create WordPress privacy requests that you confirm by email; they then show under Tools → Export / Erase Personal Data with a Shopify column, and on the account’s RedeemID → Customers page.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/member-features {"title":"Your membership features","showMissing":true} /-->

<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Lists the features you hold and where each comes from (“Ad-free reading, from Gold”), then the ones you don’t, marked “not included”. Ada Add-on holds Ad-free reading from the add-on, not from Gold.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/account-status {"title":"Visitor state","showRaw":true} /-->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Confirm your email</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Shows only to a signed-in WordPress user who is not linked. Shopify’s sign-in page opens with this account’s email filled in; enter the code and the account links straight away, back on this page. The code must be for the same email. Administrators are refused.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/auth-action {"mode":{"value":"connect","label":"Connect only (signed-in, unlinked accounts)"},"connectLabel":"Confirm your email"} /-->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Sign out</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Ends the WordPress session and the Shopify session together. WordPress’s own logout link in the admin bar does the same.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/auth-action {"mode":{"value":"logout","label":"Log out only (hidden for guests)"},"redirectAfterLogout":{"url":"/"}} /-->
