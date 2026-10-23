<?php
/**
 * Title: Shop (page)
 * Slug: sfwp-test/page-shop
 * Categories: sfwp-test
 * Post Types: page
 * Description: Shopify's own collection block, for the co-install checks. Seeded only while Shopify's WordPress plugin is active.
 */
?>
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">This page uses Shopify’s WordPress plugin for the catalog. WP2 doesn’t ship catalog blocks: it signs the visitor in and opens gated content, and Shopify’s plugin sells.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"sfwp-expect"} -->
<p class="sfwp-expect">Expect products from the <code>frontpage</code> collection (Shopify’s default “Home page” collection) published to the Sell on WordPress channel. Add one to the cart while signed in as a linked customer, then open checkout: it should show you signed in. Debug in the Shopify admin menu shows the cart sign-in bridge’s last attach.</p>
<!-- /wp:paragraph -->

<!-- wp:shopify-collection/shopify-collection-block {"selectedCollection":{"handle":"frontpage","title":"Home page"},"maxProductsPerRow":3,"maxProductsPerPage":6} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Memberships</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Plans are sold with WP2’s plan cards, which go straight to checkout without the cart. See <a href="/join/">Become a member</a>.</p>
<!-- /wp:paragraph -->
