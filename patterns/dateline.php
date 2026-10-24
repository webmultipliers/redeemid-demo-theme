<?php
/**
 * Title: Dateline
 * Slug: sfwp-test/dateline
 * Categories: sfwp-test
 * Inserter: no
 * Description: Today's date and the city, for the masthead's top bar.
 */
?>
<!-- wp:paragraph {"className":"sfwp-dateline","fontSize":"small"} -->
<p class="sfwp-dateline has-small-font-size"><?php echo esc_html( wp_date( 'l, F j, Y' ) ); ?> · Harborview</p>
<!-- /wp:paragraph -->
