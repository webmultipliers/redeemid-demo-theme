/**
 * Click handler for sfwp-test/resync. The button and its REST nonce arrive in
 * the block's island fragment after load, so listen on the document rather
 * than on the button. Posts to the plugin's /account/sync route, then reloads
 * so the gates re-render with the new tags.
 */
const messages = {
	synced: 'Membership updated. Reloading…',
	sync_wait: 'You just refreshed. Try again in a minute.',
	sync_failed: 'Shopify could not be reached. Try again later.',
};

async function resync(root) {
	const button = root.querySelector('.sfwp-resync__button');
	const message = root.querySelector('.sfwp-resync__message');

	button.disabled = true;
	message.textContent = 'Checking with Shopify…';

	let result = 'sync_failed';

	try {
		const response = await fetch(root.dataset.sfwpResyncEndpoint, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { Accept: 'application/json', 'X-WP-Nonce': root.dataset.sfwpResyncNonce },
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

document.addEventListener('click', (event) => {
	const root = event.target.closest('.sfwp-resync__button')?.closest('[data-sfwp-resync-nonce]');

	if (root) {
		resync(root);
	}
});
