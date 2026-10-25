<?php
/**
 * Title: Members area (page)
 * Slug: redeemid-demo/page-members-area
 * Categories: redeemid-demo
 * Post Types: page
 * Description: A membership gate that admits any signed-in visitor.
 */
?>
<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Rule: sign-in required, no tags. Guests see a message with a sign-in button. Any signed-in visitor sees the members area, and the response carries no-cache headers.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/membership-gate {"requireLogin":true,"fallbackMode":{"value":"message","label":"A message and a call to action"},"fallbackMessage":"Sign in with your Shopify customer account to open the members area.","fallbackCtaLabel":"","showSignIn":true} -->
<!-- wp:redeemid/membership-gate-content -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Welcome in</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>You are signed in, so the server released this area. Guests never receive this paragraph. Marker: <code>LEDGER-SIGNED-IN-ONLY</code>.</p>
<!-- /wp:paragraph -->
<!-- /wp:redeemid/membership-gate-content -->
<!-- wp:redeemid/membership-gate-fallback /-->
<!-- /wp:redeemid/membership-gate -->

<!-- wp:redeemid/account-status /-->
