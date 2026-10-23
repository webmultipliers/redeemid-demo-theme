<?php
/**
 * Title: Become a member (page)
 * Slug: sfwp-test/page-join
 * Categories: sfwp-test
 * Post Types: page
 * Description: Where gate calls to action point: how a test customer gets a membership tag.
 */
?>
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Membership on the Harborview Ledger is a Shopify customer tag: <code>silver-member</code> opens Silver stories, <code>gold-member</code> opens Gold stories and both together open everything. Members stories need only a signed-in account. There are two ways to get a tag for testing, or switch to one of the test accounts listed in the <a href="/test-lab/">Test lab</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Tag the customer by hand</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In the Shopify admin, open Customers, pick your test customer and add the tag <code>gold-member</code> (and <code>silver-member</code> for the “all of” box). Shopify sends <code>customer.tags_added</code>; once the Shopify admin’s webhook log shows the delivery, reload the gated page. “Refresh membership” below (and in the header) pulls the tags straight away; signing in again does too.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Buy a mapped product</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Map a product to a tag in the Shopify admin menu, for example <code>1234567890: gold-member</code>, and buy it with the test customer (Shopify’s Bogus Gateway works on a dev store). On <code>orders/paid</code> the plugin adds the tag in Shopify. A refund or cancellation removes it. After checkout, your next signed-in page views also re-sync for up to 30 minutes.</p>
<!-- /wp:paragraph -->

<!-- wp:sfwp-test/visitor-state {"showRaw":false} /-->

<!-- wp:sfwp-test/resync /-->

<!-- wp:paragraph -->
<p><a href="/tag-gates/">Back to Tag gates</a></p>
<!-- /wp:paragraph -->
