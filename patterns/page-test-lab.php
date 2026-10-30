<?php
/**
 * Title: Test lab (page)
 * Slug: redeemid-demo/page-test-lab
 * Categories: redeemid-demo
 * Post Types: page
 * Description: The sandbox landing page: health, visitor state, the feature and content-type tables and a list of things to try.
 */
?>
<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">This sandbox runs the RedeemID plugin against a real Shopify store. The <a href="/">Harborview Ledger</a> is a faux news site with free and paywalled stories; the pages below test one piece of the plugin each. Start with the two panels (Sandbox health shows to administrators only), then work down the list.</p>
<!-- /wp:paragraph -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:redeemid/health {"title":"Sandbox health"} /-->
	</div>
	<!-- /wp:column -->
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:redeemid/account-status {"title":"Visitor state","showRaw":true} /-->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Test accounts</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The theme seeds these WordPress users. As an administrator, open <strong>Test accounts</strong> in the toolbar (it needs the User Switching plugin), pick one, browse the Ledger, then use <strong>Switch back</strong>. Their state is put back each time you switch to them, and RedeemID → Test lab in the admin can point any of them at a real customer in the dev store.</p>
<!-- /wp:paragraph -->

<!-- wp:table {"className":"ledger-matrix"} -->
<figure class="wp-block-table ledger-matrix"><table><thead><tr><th>Account</th><th>State</th><th>Free</th><th>Members</th><th>Silver</th><th>Gold</th><th>Gold + Silver</th></tr></thead><tbody><tr><td>Guest (signed out)</td><td>—</td><td>Open</td><td>Teaser</td><td>Teaser</td><td>Teaser</td><td>Teaser</td></tr><tr><td>Riley Reader (ledger-reader)</td><td>WordPress only, not linked</td><td>Open</td><td>Open</td><td>Teaser</td><td>Teaser</td><td>Teaser</td></tr><tr><td>Milo Member (ledger-member)</td><td>Linked, no tags</td><td>Open</td><td>Open</td><td>Teaser</td><td>Teaser</td><td>Teaser</td></tr><tr><td>Sasha Silver (ledger-silver)</td><td>silver-member</td><td>Open</td><td>Open</td><td>Open</td><td>Teaser</td><td>Teaser</td></tr><tr><td>Gia Gold (ledger-gold)</td><td>gold-member</td><td>Open</td><td>Open</td><td>Teaser</td><td>Open</td><td>Teaser</td></tr><tr><td>Paz Premium (ledger-premium)</td><td>gold-member + silver-member</td><td>Open</td><td>Open</td><td>Open</td><td>Open</td><td>Open</td></tr><tr><td>Lou Lapsed (ledger-lapsed)</td><td>gold-member, account disabled</td><td>Open</td><td>Open</td><td>Teaser</td><td>Teaser</td><td>Teaser</td></tr><tr><td>Stella Stale (ledger-stale)</td><td>gold-member, synced 30 days ago</td><td>Open</td><td>Open</td><td>Teaser</td><td>Open</td><td>Teaser</td></tr><tr><td>Ada Add-on (ledger-addon)</td><td>silver-member + ad-free-addon</td><td>Open</td><td>Open</td><td>Open</td><td>Teaser</td><td>Teaser</td></tr></tbody></table><figcaption class="wp-element-caption">What each story badge should show for each account. Members stories need only a signed-in WordPress user. Stella matches Gia: synced tags never expire, so an old sync still opens Gold.</figcaption></figure>
<!-- /wp:table -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Features</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Features are site behaviours a membership unlocks, from the plugin’s feature registry. The theme registers two and only places the plugin’s blocks. <strong>Ad-free reading</strong> goes to Gold, or to anyone with the <code>ad-free-addon</code> tag, which stacks with any tier. It is mapped on RedeemID → Access → Features, and it hides the sponsor slot on stories and the home page. <strong>Early briefing</strong> goes to Silver or Gold, is set in code (so that screen shows it as managed in code), and shows tomorrow’s briefing on <a href="/briefings/">Briefings</a>. Both appear as chips in the header and on the <a href="/account/">Account</a> page.</p>
<!-- /wp:paragraph -->

<!-- wp:table {"className":"ledger-matrix"} -->
<figure class="wp-block-table ledger-matrix"><table><thead><tr><th>Account</th><th>Ad-free reading</th><th>Early briefing</th></tr></thead><tbody><tr><td>Guest</td><td>No: sponsor slot</td><td>No: prompt</td></tr><tr><td>Riley Reader</td><td>No</td><td>No</td></tr><tr><td>Milo Member</td><td>No</td><td>No</td></tr><tr><td>Sasha Silver</td><td>No</td><td>Yes, from Silver</td></tr><tr><td>Gia Gold</td><td>Yes, from Gold</td><td>Yes, from Gold</td></tr><tr><td>Paz Premium</td><td>Yes, from Gold</td><td>Yes, from Silver and Gold</td></tr><tr><td>Ada Add-on</td><td>Yes, from Ad-free add-on</td><td>Yes, from Silver</td></tr><tr><td>Lou Lapsed</td><td>No (disabled)</td><td>No (disabled)</td></tr><tr><td>Stella Stale</td><td>Yes, from Gold</td><td>Yes, from Gold</td></tr></tbody></table><figcaption class="wp-element-caption">The plugin decides each feature on the server, so a withheld block is not in the page source at all. The early briefing carries the marker <code>LEDGER-FEATURE-EARLY-BRIEFING</code>, which must appear only for Silver and Gold. Ad-free holders also get the <code>redeemid-feature-ad-free</code> body class (the masthead says “Ad-free”) and <code>window.redeemidFeatures</code>, which is presentation only.</figcaption></figure>
<!-- /wp:table -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Content types and their rules</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>The plugin gates more than posts. The theme registers four demo post types and four taxonomies (Editions, Series, Neighborhoods and a Topics taxonomy shared by posts and every type) and seeds rules on each layer: RedeemID → Access → Content rules for the post types, each term’s edit screen for term rules, and the Access box on each item. The table shows which layer should decide each item and the badge a guest should see.</p>
<!-- /wp:paragraph -->

<!-- wp:table {"className":"ledger-matrix"} -->
<figure class="wp-block-table ledger-matrix"><table><thead><tr><th>Item</th><th>Type</th><th>Decided by</th><th>Guest badge</th></tr></thead><tbody><?php foreach ( redeemid_demo_cpt_items() as $ledger_key => $ledger_item ) : ?><?php $ledger_type = get_post_type_object( $ledger_item['type'] ); ?><?php $ledger_base = 'post' === $ledger_item['type'] ? '' : (string) ( $ledger_type->rewrite['slug'] ?? '' ); ?><tr><td><a href="<?php echo esc_url( '/' . ( $ledger_base ? $ledger_base . '/' : '' ) . $ledger_key . '/' ); ?>"><?php echo esc_html( $ledger_item['title'] ); ?></a></td><td><?php echo esc_html( $ledger_type ? $ledger_type->labels->singular_name : $ledger_item['type'] ); ?></td><td><?php echo esc_html( $ledger_item['rule'] ); ?></td><td><?php echo esc_html( $ledger_item['expect'] ); ?></td></tr><?php endforeach; ?></tbody></table><figcaption class="wp-element-caption">Dates count from the last seeding or restore: the storm briefing locks after 7 days, the Tidewater report opens after 14 and the Weekend edition’s Opens rule ends after 30. Restore test content starts them again. Locked items carry a <code>LEDGER-SECRET-CPT-…</code> marker; items that should be open to everyone carry <code>LEDGER-OPEN-CPT-…</code>.</figcaption></figure>
<!-- /wp:table -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What to try</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true,"className":"ledger-steps"} -->
<ol class="ledger-steps">
	<!-- wp:list-item -->
	<li><strong>Sign in.</strong> Use “Log in with Shopify” in the header. Shopify should send you to <a href="/auth-callback/">/auth-callback/</a> and then back to the page you started on. The Visitor state panel should say you are signed in, linked, and (for a new customer) a shadow user.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Check every control.</strong> <a href="/sign-in-controls/">Sign-in controls</a> shows the block in each mode. The page HTML is the same for everyone; only the hydrated control changes.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Open the members area.</strong> <a href="/members-area/">Members area</a> releases its content to any signed-in visitor. As a guest you should see a message and a sign-in button.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Test tag rules.</strong> <a href="/tag-gates/">Tag gates</a> has gates for <code>gold-member</code>, <code>gold-member</code> + <code>silver-member</code>, a teaser fallback, a tag nobody has, and an open control. Add or remove the tags on your customer in Shopify (or buy a mapped product, see <a href="/join/">Become a member</a>) and reload.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Try to leak the paywalled post.</strong> As a guest, open <a href="/members-only-article/">the members article</a>, view its source, its <a href="/feed/">RSS</a>, its <a href="/wp-json/wp/v2/posts?slug=members-only-article">REST response</a> and <a href="/?s=LEDGER-SECRET">a search for the secret phrase</a>. The phrase <code>LEDGER-SECRET-MEMBERS-ONLY</code> must not appear anywhere unless you are a <code>gold-member</code>.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Read the Ledger as each account.</strong> Switch to each test account and open one story of each badge from the <a href="/">home page</a>. The result should match the table above. Paywalled stories carry a <code>LEDGER-SECRET-NEWS-…</code> leak marker that must stay out of the source, feed, REST and search for anyone not entitled.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Check the section rules.</strong> The Investigations stories have no gate block: the plugin's content rules lock them (RedeemID → Access → Content rules, and each category's edit screen). <em>Harbor pilots</em> follows the Gold column above, <em>The arts council’s grant audit</em> the Gold + Silver column (it is in Investigations and Culture), and <em>Ferry fuel contract</em> is open to everyone while its "Free to read" tag lasts. The post list's Access column says where each story’s rule comes from. Change a category’s rule and reload a story: the change applies at once.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Check the content types.</strong> Open each item in the table above as a guest and as each test account. Then try to leak them: <a href="/?s=LEDGER-SECRET-CPT">search</a>, the <a href="/reports/feed/">Reports feed</a> and the REST routes (<a href="/wp-json/wp/v2/ledger_report">reports</a>, <a href="/wp-json/wp/v2/ledger_briefing">briefings</a>, <a href="/wp-json/wp/v2/ledger_guide">guides</a>). Change a post type row, a term rule or an item’s Access box and reload: the badge and the paywall follow at once. Corrections never lock, because gating is switched off for that post type.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Refresh membership.</strong> Signed in and linked, use “Refresh membership” in the header (or in a story’s paywall box). It calls the plugin’s <code>POST /redeemid/v1/account/sync</code> and reloads; a second press within a minute says to wait. For a simulated test account it puts the seeded state back; for a real customer it asks Shopify, so a tag you just added in Shopify opens the matching stories.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Handle a privacy request.</strong> Signed in as a test account, use Download my data or Delete my data on the <a href="/account/">Account</a> page, then confirm from the email. As an administrator, open Tools → Export Personal Data and Tools → Erase Personal Data: each request has a Shopify column with the customer and where the request came from. Export lists the synced fields, the activity log and the webhooks for that customer; Erase removes them and keeps the WordPress account. “Also request erasure in Shopify” is refused for the simulated accounts, which don’t exist in Shopify; for an account pointed at a real dev-store customer it asks Shopify, which erases after its waiting period and then sends <code>customers/redact</code>.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Check the features.</strong> Switch to each account and compare the header chips, a story’s sponsor slot and the <a href="/briefings/">Briefings</a> page with the Features table. As Ada Add-on, remove <code>ad-free-addon</code> on RedeemID → Customers (or let a refund do it): the sponsor slot comes back on the next page, and she keeps Early briefing from Silver. RedeemID → Access → Test prints each account’s features with the route that grants them, and RedeemID → Access → Features lets you remap Ad-free reading.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Redirect mode.</strong> <a href="/gate-redirect/">Redirect gate</a> sends non-members to the Become a member page.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Link an existing account.</strong> Sign in with a plain WordPress (non-admin) user whose email matches your Shopify customer, then use “Connect Shopify account” on the <a href="/account/">Account</a> page. Administrators are refused on purpose.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Sign out.</strong> “Log out” should end both the WordPress and the Shopify session. Signing in again should ask Shopify for a code.</li>
	<!-- /wp:list-item -->
	<!-- wp:list-item -->
	<li><strong>Admin side.</strong> In the <a href="/wp-admin/admin.php?page=redeemid">Shopify</a> admin menu: customers, the webhook and app event logs, and debugging (register webhooks, diagnostics, and the dry run that shows the gate decision for any user). Test lab is the last item in that menu.</li>
	<!-- /wp:list-item -->
</ol>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"ledger-note"} -->
<p class="ledger-note">Not in the plugin yet, so not testable here: the account card, the full member menu (today the header shows only the feature chips) and a cart count. Catalog blocks come from Shopify’s own plugin, not RedeemID.</p>
<!-- /wp:paragraph -->
