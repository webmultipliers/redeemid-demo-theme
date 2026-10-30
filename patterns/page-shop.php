<?php
/**
 * Title: Shop (page)
 * Slug: redeemid-demo/page-shop
 * Categories: redeemid-demo
 * Post Types: page
 * Description: RedeemID's own storefront: a collection, a product, the cart and the product pages.
 */
?>
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">The Ledger’s shop runs on RedeemID’s own storefront blocks: rendered on the server, no JavaScript needed, and checkout opens signed in for linked customers.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"ledger-expect"} -->
<p class="ledger-expect">Expect products from the <code>frontpage</code> collection (Shopify’s default “Home page” collection) published to the sales channel of RedeemID’s Storefront token (Headless). Add one to the cart: the header’s Cart link shows the count, and <a href="/cart/">/cart/</a> lists it. Product names open RedeemID’s product pages at <code>/products/{handle}/</code>. Signed in as a linked customer, Check out should open Shopify checkout signed in. While Shopify’s own WordPress plugin is active and RedeemID doesn’t take precedence (Settings → Storefront pages), the cart link hides and the buttons say Buy now instead.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid/collection {"collectionHandle":{"value":"frontpage","label":"Home page"},"perPage":6,"columns":3,"showTitle":true} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Memberships</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Plans are sold with RedeemID’s plan cards, which go straight to checkout without the cart. See <a href="/join/">Become a member</a>.</p>
<!-- /wp:paragraph -->
