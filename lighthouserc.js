// Lighthouse budgets (mobile, simulated throttling) for the demo's main page
// shapes as a guest: the Ledger home, a section, a paywalled story, Join,
// Account and Support. CI's "Accessibility and performance" job runs it.
const base = ( process.env.REDEEMID_URL || 'http://127.0.0.1:8080' ).replace( /\/$/, '' );

module.exports = {
	ci: {
		collect: {
			url: [ '/', '/category/investigations/', '/harbor-pilot-overtime/', '/join/', '/account/', '/support/' ].map( ( path ) => base + path ),
			// The median of three runs, so one slow request doesn't fail the job.
			numberOfRuns: 3,
			settings: {
				onlyCategories: [ 'performance' ],
				chromeFlags: '--no-sandbox --headless=new',
			},
		},
		assert: {
			assertions: {
				'categories:performance': [ 'error', { minScore: 0.9 } ],
				'largest-contentful-paint': [ 'error', { maxNumericValue: 2500 } ],
				'cumulative-layout-shift': [ 'error', { maxNumericValue: 0.1 } ],
				'total-blocking-time': [ 'error', { maxNumericValue: 200 } ],
				'server-response-time': [ 'error', { maxNumericValue: 600 } ],
				'total-byte-weight': [ 'error', { maxNumericValue: 1000 * 1024 } ],
			},
		},
		upload: {
			target: 'filesystem',
			outputDir: '.lighthouseci',
		},
	},
};
