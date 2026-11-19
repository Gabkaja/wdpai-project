import { api } from '../api.js';
import { messageFor } from '../packs/messages.js';

const root = document.querySelector('[data-avatar]');

if (root) {
    const preview = root.querySelector('[data-avatar-preview]');
    const input = root.querySelector('#avatar-file');
    const uploadButton = root.querySelector('[data-avatar-upload]');
    const removeButton = root.querySelector('[data-avatar-remove]');
    const statusEl = root.querySelector('[data-status="avatar"]');
    const initial = root.dataset.initial || '';
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const maxBytes = 2 * 1024 * 1024;
    let busy = false;

    function showStatus(type, text) {
        statusEl.classList.toggle('packs__message--error', type === 'error');
        statusEl.classList.toggle('packs__message--success', type === 'success');
        statusEl.textContent = text;
        statusEl.setAttribute('role', type === 'error' ? 'alert' : 'status');
    }

    function renderPreview(url) {
        if (url) {
            const image = document.createElement('img');
            image.className = 'avatar__image';
            image.src = url;
            image.alt = '';
            image.width = 96;
            image.height = 96;
            preview.replaceChildren(image);
        } else {
            preview.textContent = initial;
        }

        removeButton.hidden = !url;
    }

    function setBusy(value) {
        busy = value;
        root.setAttribute('aria-busy', String(value));

        if (uploadButton) {
            uploadButton.disabled = value;
        }

        if (removeButton) {
            removeButton.disabled = value;
        }
    }

    function upload() {
        if (busy) {
            return;
        }

        const file = input.files && input.files[0];

        if (!file) {
            showStatus('error', 'Choose an image to upload.');
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            showStatus('error', 'Choose a JPG, PNG or WebP image.');
            return;
        }

        if (file.size > maxBytes) {
            showStatus('error', 'Image must be 2 MB or smaller.');
            return;
        }

        const body = new FormData();
        body.append('avatar', file);

        setBusy(true);
        showStatus('status', 'Uploading…');

        api.post('/api/settings/avatar', body).then((data) => {
            renderPreview(data.avatar_url);
            input.value = '';
            showStatus('success', 'Avatar updated.');
        }).catch((error) => {
            showStatus('error', (error.fields && error.fields.avatar) || messageFor(error));
        }).finally(() => {
            setBusy(false);
        });
    }

    function remove() {
        if (busy) {
            return;
        }

        if (!window.confirm('Remove your avatar?')) {
            return;
        }

        setBusy(true);
        showStatus('status', 'Removing…');

        api.post('/api/settings/avatar/delete', {}).then(() => {
            renderPreview(null);
            input.value = '';
            showStatus('success', 'Avatar removed.');
        }).catch((error) => {
            if (error.code === 'avatar_not_found') {
                renderPreview(null);
            }

            showStatus('error', messageFor(error));
        }).finally(() => {
            setBusy(false);
        });
    }

    if (uploadButton) {
        uploadButton.addEventListener('click', upload);
    }

    if (removeButton) {
        removeButton.addEventListener('click', remove);
    }

    if (input) {
        input.addEventListener('change', () => {
            showStatus('status', '');
        });
    }
}
