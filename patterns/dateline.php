<?php
/**
 * Title: Dateline
 * Slug: redeemid-demo/dateline
 * Categories: redeemid-demo
 * Inserter: no
 * Description: Today's date and the city, for the masthead's top bar.
 */
?>
<!-- wp:paragraph {"className":"ledger-dateline","fontSize":"small"} -->
<p class="ledger-dateline has-small-font-size"><?php echo esc_html( wp_date( 'l, F j, Y' ) ); ?> · Harborview</p>
<!-- /wp:paragraph -->
