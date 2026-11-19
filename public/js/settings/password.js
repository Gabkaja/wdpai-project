import { api } from '../api.js';
import { messageFor } from '../packs/messages.js';

const form = document.querySelector('form[data-form="password"]');

if (form) {
    const statusEl = form.querySelector('[data-status="password"]');
    const submit = form.querySelector('button[type="submit"]');
    const submitLabel = submit ? submit.textContent : '';
    const inputs = {
        current_password: form.querySelector('input[name="current_password"]'),
        new_password: form.querySelector('input[name="new_password"]'),
        new_password_confirm: form.querySelector('input[name="new_password_confirm"]'),
    };
    let busy = false;

    function clearInvalid() {
        Object.values(inputs).forEach((input) => {
            if (input) {
                input.removeAttribute('aria-invalid');
            }
        });
    }

    function markInvalid(fields) {
        clearInvalid();

        if (!fields || typeof fields !== 'object') {
            return;
        }

        let first = null;

        Object.keys(fields).forEach((name) => {
            const input = inputs[name];

            if (input) {
                input.setAttribute('aria-invalid', 'true');

                if (!first) {
                    first = input;
                }
            }
        });

        if (first) {
            first.focus();
        }
    }

    function showStatus(type, text) {
        statusEl.classList.toggle('packs__message--error', type === 'error');
        statusEl.classList.toggle('packs__message--success', type === 'success');
        statusEl.textContent = text;
        statusEl.setAttribute('role', type === 'error' ? 'alert' : 'status');
    }

    function setBusy(value) {
        busy = value;
        form.setAttribute('aria-busy', String(value));

        if (submit) {
            submit.disabled = value;
            submit.textContent = value ? 'Saving…' : submitLabel;
        }
    }

    function fieldsMessage(fields) {
        if (!fields || typeof fields !== 'object') {
            return '';
        }

        return Object.values(fields).filter((message) => typeof message === 'string').join(' ');
    }

    form.addEventListener('input', (event) => {
        if (event.target.name && inputs[event.target.name]) {
            event.target.removeAttribute('aria-invalid');
        }
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        if (busy) {
            return;
        }

        clearInvalid();
        setBusy(true);
        showStatus('status', 'Saving…');

        api.post('/api/settings/password', {
            current_password: inputs.current_password.value,
            new_password: inputs.new_password.value,
            new_password_confirm: inputs.new_password_confirm.value,
        }).then(() => {
            form.reset();
            showStatus('success', 'Password changed.');
        }).catch((error) => {
            markInvalid(error.fields);
            showStatus('error', fieldsMessage(error.fields) || messageFor(error));
        }).finally(() => {
            setBusy(false);
        });
    });
}
