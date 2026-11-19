import { api } from '../api.js';
import { messageFor } from '../packs/messages.js';

const root = document.querySelector('[data-kick]');

if (root) {
    const statusText = root.querySelector('[data-kick-status]');
    const actions = root.querySelector('[data-kick-actions]');
    const statusEl = root.querySelector('[data-status="kick"]');
    let busy = false;

    function showStatus(type, text) {
        statusEl.classList.toggle('packs__message--error', type === 'error');
        statusEl.classList.toggle('packs__message--success', type === 'success');
        statusEl.textContent = text;
        statusEl.setAttribute('role', type === 'error' ? 'alert' : 'status');
    }

    function renderDisconnected() {
        statusText.textContent = 'Not connected.';

        const link = document.createElement('a');
        link.className = 'button button--primary';
        link.href = '/profile/kick/connect';
        link.textContent = 'Connect Kick';

        actions.replaceChildren(link);
    }

    root.addEventListener('click', (event) => {
        const button = event.target.closest('[data-kick-disconnect]');

        if (!button || busy) {
            return;
        }

        if (!window.confirm('Disconnect your Kick account?')) {
            return;
        }

        const label = button.textContent;
        busy = true;
        button.disabled = true;
        button.textContent = 'Disconnecting…';
        root.setAttribute('aria-busy', 'true');
        showStatus('status', 'Disconnecting…');

        api.post('/api/settings/kick/disconnect', {}).then(() => {
            renderDisconnected();
            showStatus('success', 'Kick account disconnected.');
        }).catch((error) => {
            showStatus('error', messageFor(error));
        }).finally(() => {
            busy = false;
            root.setAttribute('aria-busy', 'false');

            if (button.isConnected) {
                button.disabled = false;
                button.textContent = label;
            }
        });
    });
}
