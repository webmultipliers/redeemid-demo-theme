// axe-core WCAG 2.2 A/AA checks on the demo, at desktop and phone width:
// every published page, story, demo-type item, section and archive as a guest
// (CI writes the list from WordPress, REDEEMID_URLS, so new content is checked
// without editing this file); the pages that change with membership as each
// kind of test account; and the Test lab admin screen.
const fs = require( 'node:fs' );
const { test, expect } = require( '@playwright/test' );
const AxeBuilder = require( '@axe-core/playwright' ).default;

const tags = [ 'wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa' ];
const listed = fs
	.readFileSync( process.env.REDEEMID_URLS || '/tmp/redeemid-urls.txt', 'utf8' )
	.split( '\n' )
	.map( ( line ) => line.trim() )
	.filter( Boolean );
// Sign-in cookies for the seeded test accounts, by login (REDEEMID_COOKIES).
const cookies = JSON.parse( fs.readFileSync( process.env.REDEEMID_COOKIES || '/tmp/redeemid-cookies.json', 'utf8' ) );

const membershipPages = [ '/', '/account/', '/join/', '/support/', '/test-lab/', '/tag-gates/', '/members-area/', '/sign-in-controls/', '/members-only-article/' ];
const accounts = [ 'ledger-reader', 'ledger-member', 'ledger-gold', 'ledger-lapsed', 'ledger-supporter' ];

function report( violations ) {
	return violations
		.map( ( v ) => `${ v.id } (${ v.impact }): ${ v.help }\n  ${ v.nodes.map( ( n ) => `${ n.target.join( ' ' ) }: ${ n.failureSummary.split( '\n' ).slice( 1 ).join( ' ' ).trim() }` ).join( '\n  ' ) }\n  ${ v.helpUrl }` )
		.join( '\n\n' );
}

async function audit( page, urls, include ) {
	for ( const url of urls ) {
		await test.step( new URL( url, 'http://x' ).pathname, async () => {
			const response = await page.goto( url );
			expect.soft( response.status(), url ).toBeLessThan( 500 );
			// Blockstudio islands fill in after load; wait for them.
			await page.waitForLoadState( 'networkidle' );
			const axe = new AxeBuilder( { page } ).withTags( tags );
			const { violations } = await ( include ? axe.include( include ) : axe ).analyze();
			// Soft, so one run reports every page that fails.
			expect.soft( report( violations ), url ).toBe( '' );
		} );
	}
}

test( 'every page as a guest', async ( { page } ) => {
	expect( listed.length ).toBeGreaterThan( 0 );
	await audit( page, [ ...new Set( [ ...listed, '/?s=harbor', '/this-page-does-not-exist/' ] ) ] );
} );

for ( const login of accounts ) {
	test( `membership pages as ${ login }`, async ( { page, context, baseURL } ) => {
		const [ name, ...value ] = cookies[ login ].split( '=' );
		await context.addCookies( [ { name, value: value.join( '=' ), url: baseURL } ] );
		await audit( page, membershipPages );
	} );
}

// Only the theme's part of the screen; core checks its own admin screens.
test( 'Demo admin screen', async ( { page } ) => {
	await page.goto( '/wp-login.php' );
	await page.fill( '#user_login', 'admin' );
	await page.fill( '#user_pass', 'admin' );
	await Promise.all( [ page.waitForURL( /wp-admin/ ), page.click( '#wp-submit' ) ] );
	await audit( page, [ '/wp-admin/admin.php?page=redeemid-demo', '/wp-admin/admin.php?page=redeemid-demo&tab=content', '/wp-admin/admin.php?page=redeemid-demo&tab=health' ], '#wpbody-content .wrap' );
} );
