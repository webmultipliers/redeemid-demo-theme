/**
 * Fills sfwp-test/resync shells. One status request per page; the button
 * posts to the plugin's /account/sync route with the REST nonce the status
 * call returned, then reloads so the gates re-render with the new tags.
 */
let statusRequest;

function fetchStatus(url) {
	statusRequest ??= fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
		.then((response) => (response.ok ? response.json() : null))
		.catch(() => null);

	return statusRequest;
}

function describeSync(seconds) {
	if (!seconds) {
		return 'Not synced yet.';
	}

	const minutes = Math.round((Date.now() - seconds * 1000) / 60000);

	if (minutes < 1) {
		return 'Synced from Shopify just now.';
	}

	if (minutes < 120) {
		return `Synced from Shopify ${minutes} min ago.`;
	}

	return minutes < 2880 ? `Synced from Shopify ${Math.round(minutes / 60)} h ago.` : `Synced from Shopify ${Math.round(minutes / 1440)} days ago.`;
}

const messages = {
	synced: 'Membership updated. Reloading…',
	sync_wait: 'You just refreshed. Try again in a minute.',
	sync_failed: 'Shopify could not be reached. Try again later.',
};

async function resync(root, status) {
	const button = root.querySelector('.sfwp-resync__button');
	const message = root.querySelector('.sfwp-resync__message');

	button.disabled = true;
	message.textContent = 'Checking with Shopify…';

	let result = 'sync_failed';

	try {
		const response = await fetch(root.dataset.sfwpResyncEndpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { Accept: 'application/json', 'X-WP-Nonce': status.nonces.rest },
		});
		const body = await response.json().catch(() => null);
		result = response.ok && body && body.result ? body.result : 'sync_failed';
	} catch {
		result = 'sync_failed';
	}

	message.textContent = messages[result] ?? messages.sync_failed;

	if (result === 'synced') {
		window.setTimeout(() => window.location.reload(), 600);
		return;
	}

	button.disabled = false;
}

for (const root of document.querySelectorAll('[data-sfwp-resync-status]')) {
	if (!root.dataset.sfwpResyncStatus) {
		continue;
	}

	fetchStatus(root.dataset.sfwpResyncStatus).then((status) => {
		if (!status || !status.signedIn || !(status.linked || status.shadow) || !status.nonces || !status.nonces.rest) {
			return; // Guests and unlinked accounts have nothing to re-sync.
		}

		if (!root.classList.contains('is-compact')) {
			root.querySelector('.sfwp-resync__message').textContent = describeSync(status.lastSync);
		}

		root.querySelector('.sfwp-resync__button').addEventListener('click', () => resync(root, status));
		root.hidden = false;
	});
}
