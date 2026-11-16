import { api } from '../api.js';
import { updatePoints, updateStock, config } from './state.js';
import { messageFor, fieldsMessage } from './messages.js';
import { setBusy, showMessage, announce } from './ui.js';

const form = document.querySelector('form[data-form="buy"]');
const messageEl = form ? form.querySelector('[data-message="buy"]') : null;
const quantityInput = form ? form.querySelector('input[name="quantity"]') : null;

if (form && messageEl && quantityInput) {
    if (!messageEl.id) {
        messageEl.id = 'packs-buy-message';
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const max = Math.min(parseInt(quantityInput.max, 10) || 1, config.maxBulk);
        const quantity = parseInt(quantityInput.value, 10);

        if (!Number.isInteger(quantity) || quantity < 1 || quantity > max) {
            showMessage(messageEl, 'error', 'Quantity is out of the allowed range.');
            announce('Quantity is out of the allowed range.');
            return;
        }

        quantityInput.removeAttribute('aria-invalid');
        quantityInput.removeAttribute('aria-describedby');
        showMessage(messageEl, 'status', '');
        setBusy(form, true);

        try {
            const data = await api.post('/api/packs/buy', { quantity });
            updatePoints(data.points);
            updateStock(data.unopened_packs);
            showMessage(messageEl, 'status', `Bought ${data.purchased} packs for ${data.points_spent} points.`);
            announce(`Bought ${data.purchased} packs for ${data.points_spent} points.`);
        } catch (error) {
            const fieldMsg = fieldsMessage(error);
            if (fieldMsg) {
                quantityInput.setAttribute('aria-invalid', 'true');
                quantityInput.setAttribute('aria-describedby', messageEl.id);
                showMessage(messageEl, 'error', fieldMsg);
                announce(fieldMsg);
            } else {
                showMessage(messageEl, 'error', messageFor(error));
                announce(messageFor(error));
            }
        } finally {
            setBusy(form, false);
        }
    });
}
