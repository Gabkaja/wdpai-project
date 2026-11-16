import { api } from '../api.js';
import { updateStock, config } from './state.js';
import { prependOpenings } from './history.js';
import { show as stageShow } from './stage.js';
import { settings } from './settings.js';
import { messageFor } from './messages.js';
import { setBusy, showMessage, announce } from './ui.js';

const openForm = document.querySelector('form[data-form="open"]');
const openAllBtn = document.querySelector('button[data-action="open-all"]');
const messageEl = openForm ? openForm.querySelector('[data-message="open"]') : null;

if (openForm && messageEl) {
    async function open(count) {
        setBusy(openForm, true);
        showMessage(messageEl, 'status', '');

        try {
            const data = await api.post('/api/packs/open', { count });
            updateStock(data.unopened_packs);
            prependOpenings(data.openings);
            stageShow(data.openings, settings);
            announce(`Opened ${data.openings.length} pack(s).`);
        } catch (error) {
            showMessage(messageEl, 'error', messageFor(error));
            announce(messageFor(error));
        } finally {
            setBusy(openForm, false);
        }
    }

    openForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const count = parseInt(openForm.querySelector('input[name="count"]').value, 10);
        open(count);
    });

    openAllBtn.addEventListener('click', () => {
        open(Math.min(config.stock, config.maxBulk));
    });
}
