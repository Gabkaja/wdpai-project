import { api } from '../api.js';
import { configure } from '../sound.js';
import { messageFor } from '../packs/messages.js';
import { setBusy, showMessage, announce } from '../packs/ui.js';

export const settings = {
    reveal_mode: 'auto',
    multi_open_mode: 'queue',
    sound_enabled: true,
    sound_volume: 70,
};

export function loadSettings() {
    return api.get('/api/settings/packs').then((data) => {
        Object.assign(settings, data);
        configure(settings);
        return settings;
    });
}

const form = document.querySelector('form[data-form="settings"]');

if (form) {
    const statusEl = form.querySelector('[data-status="settings"]');
    const volumeInput = form.querySelector('input[name="sound_volume"]');
    const volumeOutput = form.querySelector('output');
    let pending = null;
    let lastValues = { ...settings };
    let retryBtn = null;

    function readValues() {
        return {
            reveal_mode: form.querySelector('input[name="reveal_mode"]:checked').value,
            multi_open_mode: form.querySelector('input[name="multi_open_mode"]:checked').value,
            sound_enabled: form.querySelector('input[name="sound_enabled"]').checked,
            sound_volume: parseInt(volumeInput.value, 10),
        };
    }

    function restore(values) {
        const reveal = form.querySelector(`input[name="reveal_mode"][value="${values.reveal_mode}"]`);
        const multi = form.querySelector(`input[name="multi_open_mode"][value="${values.multi_open_mode}"]`);
        const sound = form.querySelector('input[name="sound_enabled"]');

        if (reveal) {
            reveal.checked = true;
        }
        if (multi) {
            multi.checked = true;
        }
        if (sound) {
            sound.checked = values.sound_enabled;
        }
        if (volumeInput) {
            volumeInput.value = String(values.sound_volume);
        }
        if (volumeOutput) {
            volumeOutput.textContent = String(values.sound_volume);
        }
    }

    function removeRetry() {
        if (retryBtn) {
            retryBtn.remove();
            retryBtn = null;
        }
    }

    function save() {
        removeRetry();
        const values = readValues();
        lastValues = { ...values };
        setBusy(form, true);
        showMessage(statusEl, 'status', 'Saving...');
        announce('Saving settings...');

        api.post('/api/settings/packs', values).then((data) => {
            Object.assign(settings, data);
            configure(settings);
            lastValues = { ...settings };
            showMessage(statusEl, 'status', 'Saved');
            announce('Settings saved.');
        }).catch((error) => {
            restore(lastValues);
            showMessage(statusEl, 'error', messageFor(error));
            announce(messageFor(error));
            retryBtn = document.createElement('button');
            retryBtn.type = 'button';
            retryBtn.textContent = 'Retry';
            retryBtn.className = 'button button--secondary';
            retryBtn.addEventListener('click', () => {
                removeRetry();
                save();
            });
            statusEl.after(retryBtn);
        }).finally(() => {
            setBusy(form, false);
        });
    }

    const debouncedSave = () => {
        clearTimeout(pending);
        pending = setTimeout(save, 300);
    };

    form.addEventListener('input', (event) => {
        if (event.target.name === 'sound_volume' && volumeOutput) {
            volumeOutput.textContent = event.target.value;
        }
    });

    form.addEventListener('change', debouncedSave);

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        clearTimeout(pending);
        save();
    });
}
