import { api } from '../api.js';
import { fieldsMessage, messageFor } from '../packs/messages.js';

const form = document.querySelector('form[data-form="performance"]');

if (form) {
    const statusEl = form.querySelector('[data-status="performance"]');
    const toggle = form.querySelector('input[name="lite_mode"]');
    let busy = false;

    function setBusy(value) {
        busy = value;
        form.setAttribute('aria-busy', String(value));

        if (toggle) {
            toggle.disabled = value;
        }
    }

    function showStatus(type, text) {
        statusEl.classList.toggle('packs__message--error', type === 'error');
        statusEl.classList.toggle('packs__message--success', type === 'success');
        statusEl.textContent = text;
        statusEl.setAttribute('role', type === 'error' ? 'alert' : 'status');
    }

    function applyLite(value) {
        document.documentElement.classList.toggle('is-lite', value);
    }

    if (toggle) {
        toggle.addEventListener('change', () => {
            if (busy) {
                return;
            }

            const next = toggle.checked;
            const previous = !next;

            setBusy(true);
            showStatus('status', 'Saving…');
            applyLite(next);

            api.post('/api/settings/performance', { lite_mode: next }).then((data) => {
                toggle.checked = data.lite_mode;
                applyLite(data.lite_mode);
                showStatus('success', 'Saved');
            }).catch((error) => {
                toggle.checked = previous;
                applyLite(previous);
                showStatus('error', fieldsMessage(error) || messageFor(error));
            }).finally(() => {
                setBusy(false);
            });
        });
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
    });
}
