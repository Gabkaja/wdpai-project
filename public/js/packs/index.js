import './state.js';
import './buy.js';
import './open.js';
import './history.js';
import './collection.js';
import { configure } from '../sound.js';
import { loadSettings, settings } from '../settings/pack-settings.js';

document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector('[data-packs-root]')) {
        return;
    }

    loadSettings().then(() => configure(settings));
});

window.addEventListener('pageshow', (event) => {
    if (event.persisted) {
        window.location.reload();
    }
});
