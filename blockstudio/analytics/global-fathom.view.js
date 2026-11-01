/**
 * The Ledger's interaction events, sent to Fathom.
 *
 * Built on the plugin's browser client (`redeemid-client`), so the events stay
 * vendor-neutral: the plugin's own events (walls, the meter, sign-in and
 * checkout clicks) and this theme's `site_click` / `site_search` share one
 * stream, and Fathom is one subscriber. Fathom's WordPress plugin loads
 * Fathom's script; until it has, events wait in the stream's history.
 *
 * A Blockstudio global asset: `global-` loads it on every page, `.view.js`
 * keeps it on the front end only. init.php beside it enqueues the plugin's
 * client, which may run before or after this file, so this waits for it.
 *
 * A Fathom event is `<component>: <target>`. The component is where the
 * control sits (a Ledger region by its class, otherwise the block); the target
 * is where it goes (a site path, or a host), never its wording. No user,
 * email, tag or post ID reaches Fathom.
 */
( () => {
	let client = null;

	const ready = () => {
		if ( client || ! window.redeemid || typeof window.redeemid.subscribe !== 'function' ) {
			return;
		}

		client = window.redeemid;
		run();
	};

	const run = () => {

		/* Mapper --------------------------------------------------------------- */

		// Nearest first: the first ancestor matching one of these names the component.
		const REGIONS = [
			[ '.redeemid-paywall', 'Wall' ],
			[ '.ledger-topbar__links', 'Top bar' ],
			[ '.ledger-account', 'Account bar' ],
			[ '.ledger-masthead', 'Masthead' ],
			[ '.ledger-sections', 'Sections' ],
			[ '.ledger-header', 'Header' ],
			[ 'footer.wp-block-template-part, .ledger-footer, footer', 'Footer' ],
			[ '.wp-block-post-title', 'Story' ],
			[ '.wp-block-post-terms', 'Topic' ],
			[ '.wp-block-query-pagination', 'Pagination' ],
			[ '.wp-block-post-navigation-link', 'Story nav' ],
			[ '.wp-block-post-content', 'Story link' ],
		];

		const title = ( slug ) => slug.replace( /-/g, ' ' ).replace( /^./, ( c ) => c.toUpperCase() );

		const component = ( element ) => {
			for ( let node = element; node && node !== document.body; node = node.parentElement ) {
				for ( const [ selector, name ] of REGIONS ) {
					if ( node.matches( selector ) ) {
						return name;
					}
				}

				const block = [ ...node.classList ].find( ( name ) => /^wp-block-redeemid(-demo)?-/.test( name ) );

				if ( block ) {
					return title( block.replace( /^wp-block-redeemid(-demo)?-/, '' ) );
				}
			}

			return 'Page';
		};

		const words = ( text ) => String( text || '' ).replace( /\s+/g, ' ' ).trim().toLowerCase().slice( 0, 40 ).trim();

		const destination = ( link ) => {
			let url;

			try {
				url = new URL( link.getAttribute( 'href' ) || '', window.location.href );
			} catch ( e ) {
				return words( link.getAttribute( 'aria-label' ) || link.textContent );
			}

			if ( ! url.protocol.startsWith( 'http' ) ) {
				return url.protocol.replace( ':', '' );
			}

			if ( url.host !== window.location.host ) {
				return url.hostname.replace( /^www\./, '' );
			}

			if ( url.pathname === window.location.pathname && url.hash ) {
				return 'jump';
			}

			return url.pathname.replace( /^\/+|\/+$/g, '' ) || 'home';
		};

		// The plugin already reports sign-in links and buy-now buttons, so those are
		// left to it; everything else pressed gets one site_click.
		document.addEventListener(
			'click',
			( event ) => {
				const control = event.target instanceof Element ? event.target.closest( 'a[href], button, summary, [role="button"]' ) : null;

				if ( ! control || control.closest( 'form' ) ) {
					return;
				}

				if ( control.matches( 'a[href]' ) ) {
					const href = control.href || '';

					if ( /[?&]action=redeemid_login\b|\/redeemid\/v1\/auth\/login/.test( href ) ) {
						return;
					}

					client.emit( 'site_click', { component: component( control ), target: destination( control ) } );
					return;
				}

				client.emit( 'site_click', { component: component( control ), target: words( control.getAttribute( 'aria-label' ) || control.textContent ) || 'button' } );
			},
			true
		);

		// A search, never what was searched for.
		document.addEventListener(
			'submit',
			( event ) => {
				if ( event.target instanceof HTMLFormElement && event.target.matches( '.wp-block-search, [role="search"]' ) ) {
					client.emit( 'site_search', { component: 'Search', target: 'submit' } );
				}
			},
			true
		);

		/* Fathom --------------------------------------------------------------- */

		const named = ( name, props ) => {
			switch ( name ) {
				case 'wall_shown':
					return `${ 'list' === props.view ? 'Wall in list' : 'Wall' }: ${ props.level || 'any' }`;
				case 'metered_view':
					return 'Meter: view';
				case 'sign_in_click':
					return `Sign in: ${ props.source }`;
				case 'checkout_click':
					return `Checkout: ${ props.subscription ? 'subscription' : 'one-time' } from ${ props.source }`;
				case 'site_click':
				case 'site_search':
					return `${ props.component }: ${ props.target }`;
				default:
					// ready and state carry nothing worth a Fathom row.
					return '';
			}
		};

		const toFathom = ( name, props ) => {
			const label = named( name, props ).replace( /[^\w\s/.:-]/g, '' ).slice( 0, 120 );

			if ( label ) {
				window.fathom.trackEvent( label );
			}
		};

		let started = false;

		const start = () => {
			if ( started || ! window.fathom || typeof window.fathom.trackEvent !== 'function' ) {
				return;
			}

			started = true;
			client.subscribe( toFathom );
		};

		start();

		if ( ! started ) {
			document.querySelector( 'script[src*="usefathom.com/script.js"], script[data-site][src$="/script.js"]' )?.addEventListener( 'load', start );
			window.addEventListener( 'load', start );
		}
	};

	ready();

	if ( ! client ) {
		document.addEventListener( 'DOMContentLoaded', ready, { once: true } );
		window.addEventListener( 'load', ready, { once: true } );
	}
} )();
