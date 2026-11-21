import { api } from '../api.js';
import { fieldsMessage, messageFor } from '../packs/messages.js';

const form = document.querySelector('form[data-form="privacy"]');

if (form) {
    const statusEl = form.querySelector('[data-status="privacy"]');
    const radios = Array.from(form.querySelectorAll('input[name="collection_visibility"]'));
    const selected = form.querySelector('input[name="collection_visibility"]:checked');
    let current = selected ? selected.value : 'public';
    let busy = false;

    function setBusy(value) {
        busy = value;
        form.setAttribute('aria-busy', String(value));
        radios.forEach((radio) => {
            radio.disabled = value;
        });
    }

    function showStatus(type, text) {
        statusEl.classList.toggle('packs__message--error', type === 'error');
        statusEl.classList.toggle('packs__message--success', type === 'success');
        statusEl.textContent = text;
        statusEl.setAttribute('role', type === 'error' ? 'alert' : 'status');
    }

    function restore(value) {
        const radio = form.querySelector(`input[name="collection_visibility"][value="${value}"]`);

        if (radio) {
            radio.checked = true;
        }
    }

    form.addEventListener('change', (event) => {
        if (event.target.name !== 'collection_visibility' || busy) {
            return;
        }

        const previous = current;
        current = event.target.value;
        setBusy(true);
        showStatus('status', 'Saving…');

        api.post('/api/settings/privacy', { collection_visibility: current }).then((data) => {
            current = data.collection_visibility;
            restore(current);
            showStatus('success', 'Saved');
        }).catch((error) => {
            current = previous;
            restore(previous);
            showStatus('error', fieldsMessage(error) || messageFor(error));
        }).finally(() => {
            setBusy(false);
        });
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
    });
}
