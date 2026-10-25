<?php
/**
 * Title: Redirect gate (page)
 * Slug: redeemid-demo/page-gate-redirect
 * Categories: redeemid-demo
 * Post Types: page
 * Description: A membership gate that redirects non-members.
 */
?>
<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Rule: any of gold-member, redirect mode. Non-members never see this page: they land on Become a member. Gold members stay here and see the box below.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"redirect","label":"A redirect to another page"},"redirectUrl":{"url":"/join/?from=gate-redirect"}} -->
<!-- wp:redeemid/membership-gate-content -->
<!-- wp:paragraph -->
<p>You were not redirected because you are a gold member. Marker: <code>LEDGER-REDIRECT-GOLD</code>.</p>
<!-- /wp:paragraph -->
<!-- /wp:redeemid/membership-gate-content -->
<!-- wp:redeemid/membership-gate-fallback /-->
<!-- /wp:redeemid/membership-gate -->
