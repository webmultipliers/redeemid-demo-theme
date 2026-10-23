/**
 * Fills sfwp-test/visitor-state panels from the WP2 Shopify status endpoint.
 * One request per page, shared by every panel.
 */
let statusRequest;

function fetchStatus(url) {
	statusRequest ??= fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
		.then(async (response) => ({ ok: response.ok, code: response.status, body: await response.json().catch(() => null) }))
		.catch((error) => ({ ok: false, code: 0, body: null, error: String(error) }));

	return statusRequest;
}

const yesNo = (value) => (value ? 'Yes' : 'No');

function describeSync(seconds) {
	if (!seconds) {
		return 'Never';
	}

	const date = new Date(seconds * 1000);
	const minutes = Math.round((Date.now() - date.getTime()) / 60000);
	const ago = minutes < 1 ? 'just now' : minutes < 120 ? `${minutes} min ago` : `${Math.round(minutes / 60)} h ago`;

	return `${date.toLocaleString()} (${ago})`;
}

function redact(status) {
	const copy = { ...status };

	if (copy.nonces) {
		copy.nonces = Object.fromEntries(Object.keys(copy.nonces).map((key) => [key, '(redacted)']));
	}

	return copy;
}

function render(root, result) {
	const message = root.querySelector('[data-sfwp-message]');
	const status = result.body;

	if (!result.ok || !status) {
		message.textContent = `The status endpoint did not answer (HTTP ${result.code}). Check that WP2 Shopify is active.`;
		return;
	}

	const signedIn = Boolean(status.signedIn);
	const values = {
		signedIn: yesNo(signedIn),
		name: signedIn ? status.displayName || '—' : '—',
		linked: signedIn ? yesNo(status.linked) : '—',
		shadow: signedIn ? yesNo(status.shadow) : '—',
		lastSync: signedIn ? describeSync(status.lastSync) : '—',
		available: yesNo(status.available),
		nonces: signedIn ? yesNo(status.nonces && status.nonces.logout && status.nonces.rest) : 'Not for guests',
	};

	for (const cell of root.querySelectorAll('[data-sfwp-field]')) {
		cell.textContent = values[cell.dataset.sfwpField] ?? '—';
	}

	root.classList.toggle('is-signed-in', signedIn);
	root.classList.toggle('is-linked', Boolean(status.linked || status.shadow));

	if (!status.available) {
		message.textContent = 'Shopify sign-in is not available: the store domain or client ID is missing, or Shopify discovery failed. See the Shopify admin menu.';
	} else if (!signedIn) {
		message.textContent = 'You are a guest. Use “Log in with Shopify” in the header to start the flow.';
	} else if (!status.linked && !status.shadow) {
		message.textContent = 'You are signed in to WordPress but not connected to Shopify. Try “Connect Shopify account” on the Account page.';
	} else {
		message.textContent = 'Signed in and connected to Shopify.';
	}

	if (root.dataset.sfwpRaw === '1') {
		const raw = root.querySelector('.sfwp-panel__raw');
		raw.querySelector('[data-sfwp-raw-output]').textContent = JSON.stringify(redact(status), null, 2);
		raw.hidden = false;
	}
}

for (const root of document.querySelectorAll('[data-sfwp-status]')) {
	if (!root.dataset.sfwpStatus || !root.querySelector('[data-sfwp-message]')) {
		continue;
	}

	fetchStatus(root.dataset.sfwpStatus).then((result) => render(root, result));
}
