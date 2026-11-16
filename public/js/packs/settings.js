import { api } from '../api.js';
import { configure } from '../sound.js';
import { messageFor } from './messages.js';
import { setBusy, showMessage, announce } from './ui.js';

export const settings = {
    reveal_mode: 'auto',
    multi_open_mode: 'queue',
    sound_enabled: false,
    sound_volume: 50,
};

const root = document.querySelector('[data-packs-root]');
const form = document.querySelector('form[data-form="settings"]');

if (root) {
    settings.reveal_mode = root.dataset.revealMode ?? settings.reveal_mode;
    settings.multi_open_mode = root.dataset.multiOpenMode ?? settings.multi_open_mode;
    settings.sound_enabled = root.dataset.soundEnabled === 'true';
    settings.sound_volume = root.dataset.soundVolume !== undefined ? parseInt(root.dataset.soundVolume, 10) : settings.sound_volume;
    if (!Number.isInteger(settings.sound_volume)) {
        settings.sound_volume = 50;
    }
}

if (form) {
    const statusEl = form.querySelector('[data-status="settings"]');
    let pending = null;
    let lastValues = { ...settings };
    let retryBtn = null;

    function readValues() {
        return {
            reveal_mode: form.querySelector('input[name="reveal_mode"]:checked').value,
            multi_open_mode: form.querySelector('input[name="multi_open_mode"]:checked').value,
            sound_enabled: form.querySelector('input[name="sound_enabled"]').checked,
            sound_volume: parseInt(form.querySelector('input[name="sound_volume"]').value, 10),
        };
    }

    function restore(values) {
        const reveal = form.querySelector(`input[name="reveal_mode"][value="${values.reveal_mode}"]`);
        const multi = form.querySelector(`input[name="multi_open_mode"][value="${values.multi_open_mode}"]`);
        const sound = form.querySelector('input[name="sound_enabled"]');
        const volume = form.querySelector('input[name="sound_volume"]');
        const output = form.querySelector('output');

        if (reveal) {
            reveal.checked = true;
        }
        if (multi) {
            multi.checked = true;
        }
        if (sound) {
            sound.checked = values.sound_enabled;
        }
        if (volume) {
            volume.value = String(values.sound_volume);
        }
        if (output) {
            output.textContent = String(values.sound_volume);
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
        if (event.target.name === 'sound_volume') {
            const output = form.querySelector('output');
            if (output) {
                output.textContent = event.target.value;
            }
        }
    });

    form.addEventListener('change', debouncedSave);
}
