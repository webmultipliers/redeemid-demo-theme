// Accessibility checks against a running demo site. CI's "Accessibility
// and performance" job starts one; locally, set REDEEMID_URL and REDEEMID_URLS.
const { defineConfig, devices } = require( '@playwright/test' );

module.exports = defineConfig( {
	testDir: 'tests',
	// PHP's built-in server answers only a few requests at a time.
	workers: 1,
	timeout: 10 * 60 * 1000,
	forbidOnly: !! process.env.CI,
	reporter: process.env.CI ? [ [ 'github' ], [ 'list' ] ] : 'list',
	use: {
		baseURL: process.env.REDEEMID_URL || 'http://127.0.0.1:8080',
	},
	// The header, section nav and story rows change layout at phone width.
	projects: [
		{ name: 'desktop', use: { ...devices[ 'Desktop Chrome' ] } },
		{ name: 'phone', use: { ...devices[ 'Pixel 7' ] } },
	],
} );
