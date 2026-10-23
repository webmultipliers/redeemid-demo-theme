<?php
/**
 * Title: Redirect gate (page)
 * Slug: sfwp-test/page-gate-redirect
 * Categories: sfwp-test
 * Post Types: page
 * Description: A membership gate that redirects non-members.
 */
?>
<!-- wp:paragraph {"className":"sfwp-expect"} -->
<p class="sfwp-expect">Rule: any of gold-member, redirect mode. Non-members never see this page: they land on Become a member. Gold members stay here and see the box below.</p>
<!-- /wp:paragraph -->

<!-- wp:wp2-shopify/membership-gate {"requiredTags":[{"tag":"gold-member"}],"matchMode":{"value":"any","label":"Any of these tags"},"requireLogin":true,"fallbackMode":{"value":"redirect","label":"A redirect to another page"},"redirectUrl":"/join/?from=gate-redirect"} -->
<!-- wp:wp2-shopify/membership-gate-content -->
<!-- wp:paragraph -->
<p>You were not redirected because you are a gold member. Marker: <code>SFWP-REDIRECT-GOLD</code>.</p>
<!-- /wp:paragraph -->
<!-- /wp:wp2-shopify/membership-gate-content -->
<!-- wp:wp2-shopify/membership-gate-fallback /-->
<!-- /wp:wp2-shopify/membership-gate -->
