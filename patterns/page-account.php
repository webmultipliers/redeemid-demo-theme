<?php
/**
 * Title: Account (page)
 * Slug: sfwp-test/page-account
 * Categories: sfwp-test
 * Post Types: page
 * Description: Account actions available today: connect, log out and the visitor state.
 */
?>
<!-- wp:paragraph {"className":"sfwp-note"} -->
<p class="sfwp-note">The plugin’s account card arrives in Phase 1. Until then this page collects the account actions that exist as blocks today.</p>
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
