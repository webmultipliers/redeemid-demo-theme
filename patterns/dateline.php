<?php
/**
 * Title: Dateline
 * Slug: redeemid-demo/dateline
 * Categories: redeemid-demo
 * Inserter: no
 * Description: Today's date and the city, for the masthead's top bar. Phones get the short date.
 */
?>
<!-- wp:paragraph {"className":"ledger-dateline","fontSize":"small"} -->
<p class="ledger-dateline has-small-font-size"><span class="ledger-dateline__long"><?php echo esc_html( wp_date( 'l, F j, Y' ) ); ?> · Harborview</span><span class="ledger-dateline__short"><?php echo esc_html( wp_date( 'D, M j' ) ); ?></span></p>
<!-- /wp:paragraph -->
