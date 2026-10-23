<?php
/**
 * Title: Members area (page)
 * Slug: sfwp-test/page-members-area
 * Categories: sfwp-test
 * Post Types: page
 * Description: A membership gate that admits any signed-in visitor.
 */
?>
<!-- wp:paragraph {"className":"sfwp-expect"} -->
<p class="sfwp-expect">Rule: sign-in required, no tags. Guests see a message with a sign-in button. Any signed-in visitor sees the members area, and the response carries no-cache headers.</p>
<!-- /wp:paragraph -->

<!-- wp:wp2-shopify/membership-gate {"requireLogin":true,"fallbackMode":{"value":"message","label":"A message and a call to action"},"fallbackMessage":"Sign in with your Shopify customer account to open the members area.","fallbackCtaLabel":"","showSignIn":true} -->
<!-- wp:wp2-shopify/membership-gate-content -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Welcome in</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>You are signed in, so the server released this area. Guests never receive this paragraph. Marker: <code>SFWP-SIGNED-IN-ONLY</code>.</p>
<!-- /wp:paragraph -->
<!-- /wp:wp2-shopify/membership-gate-content -->
<!-- wp:wp2-shopify/membership-gate-fallback /-->
<!-- /wp:wp2-shopify/membership-gate -->

<!-- wp:sfwp-test/visitor-state {"showRaw":false} /-->
