<?php
/**
 * Title: Support the Ledger (page)
 * Slug: redeemid-demo/page-support
 * Categories: redeemid-demo
 * Post Types: page
 * Description: Reader contributions: give any amount through Shopify checkout, and how a contribution is thanked through a customer tag.
 */
?>
<!-- wp:paragraph {"className":"ledger-article__dek"} -->
<p class="ledger-article__dek">Most of what the Ledger publishes is free, and we want to keep it that way. Readers who chip in pay for the council meetings nobody else sits through.</p>
<!-- /wp:paragraph -->

<!-- wp:redeemid-demo/contribute {"heading":"Give what you like","body":"Pick an amount or type your own. Every dollar goes to reporting."} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Contribution or membership?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A contribution is a gift: it doesn’t open members’ stories. If you want the investigations and Ledger Reports too, <a href="/join/">become a member</a>. Contributors get a quiet thank-you across the site instead of being asked again.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How it works</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"ledger-steps"} -->
<ol class="ledger-steps">
	<!-- wp:list-item -->
	<li><strong>One product, priced at $1.</strong> Shopify has no “enter your own price” field, so the store sells a Contribution product at $1 and the amount is the quantity. A $25 gift is 25 × $1 in the same checkout as the rest of the shop.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Straight to checkout.</strong> The form posts the amount to the store’s cart and opens Shopify checkout. There’s no JavaScript and nothing stored on this site.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Thanked by a tag.</strong> When the order is paid, a Shopify Flow workflow tags the customer <code>supporter</code>. RedeemID brings the tag to this site with the reader’s account, and the form above says thank you instead of asking.</li>
	<!-- /wp:list-item -->
</ol>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"ledger-note"} -->
<p class="ledger-note">The Ledger is fiction and this page is a demo of RedeemID. The checkout is a real Shopify store, so look and then close it without paying. The full recipe, including a crowdfunding goal tracked with inventory, is RedeemID’s guide <a href="https://redeemid.com/guides/shopify-donations-pay-what-you-want/">Accept donations and pay-what-you-want contributions on Shopify</a>.</p>
<!-- /wp:paragraph -->
