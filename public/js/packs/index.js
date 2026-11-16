import './state.js';
import './buy.js';
import './open.js';
import './settings.js';
import './history.js';
import { configure } from '../sound.js';
import { settings } from './settings.js';

document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector('[data-packs-root]')) {
        return;
    }

    configure(settings);
});
