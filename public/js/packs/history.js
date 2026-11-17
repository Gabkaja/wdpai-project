import { api } from '../api.js';
import { messageFor } from './messages.js';
import { setBusy, showMessage, announce } from './ui.js';

const list = document.querySelector('ol[data-history-list]');
const loadMore = document.querySelector('button[data-action="load-more"]');
const template = document.querySelector('template[data-template="history-item"]');
const messageEl = document.querySelector('[data-message="history"]');

const rarityLevels = {};
const rarityJson = document.querySelector('script[data-rarities]');
if (rarityJson) {
    try {
        for (const entry of JSON.parse(rarityJson.textContent)) {
            rarityLevels[entry.code] = entry.level;
        }
    } catch (error) {}
}

const displayedIds = new Set();

export function prependOpenings(openings) {
    if (!list) {
        return 0;
    }
    let added = 0;
    for (const opening of openings) {
        if (displayedIds.has(opening.id)) {
            continue;
        }
        displayedIds.add(opening.id);
        list.prepend(renderOpening(opening));
        added++;
    }
    if (loadMore) {
        const offset = parseInt(loadMore.dataset.nextOffset, 10) || 0;
        loadMore.dataset.nextOffset = String(offset + added);
    }
    return added;
}

function renderOpening(opening) {
    const clone = document.importNode(template.content, true);
    const item = clone.querySelector('.pack-history__item');
    item.dataset.openingId = String(opening.id);

    const link = clone.querySelector('.pack-history__link');
    link.href = `/packs/${opening.id}`;

    const time = clone.querySelector('.pack-history__time');
    time.dateTime = opening.opened_at;
    time.textContent = new Date(opening.opened_at).toLocaleString();

    const title = clone.querySelector('.pack-history__title');
    title.textContent = `Opening #${opening.id}`;

    const cardsList = clone.querySelector('.pack-history__cards');
    cardsList.replaceChildren();
    for (const entry of opening.cards) {
        const card = entry.card;
        const li = document.createElement('li');
        li.className = 'pack-history__card';

        const chip = document.createElement('span');
        chip.className = `rarity-chip rarity-chip--level-${rarityLevels[card.rarity_code] ?? 1}`;
        chip.textContent = card.rarity_code.toUpperCase();
        li.appendChild(chip);

        const name = document.createElement('span');
        name.className = 'pack-history__card-name';
        name.textContent = card.name;
        li.appendChild(name);

        if (entry.is_new) {
            const badge = document.createElement('span');
            badge.className = 'pack-history__new';
            badge.setAttribute('aria-label', 'New card');
            badge.textContent = 'NEW';
            li.appendChild(badge);
        }

        cardsList.appendChild(li);
    }

    return item;
}

if (list && loadMore && template) {
    document.querySelectorAll('li[data-opening-id]').forEach((item) => {
        if (item.dataset.openingId) {
            displayedIds.add(item.dataset.openingId);
        }
    });

    let loading = false;
    let retryBtn = null;

    function removeRetry() {
        if (retryBtn) {
            retryBtn.remove();
            retryBtn = null;
        }
    }

    loadMore.addEventListener('click', async () => {
        if (loading) {
            return;
        }
        removeRetry();
        loading = true;
        loadMore.setAttribute('aria-busy', 'true');

        const offset = parseInt(loadMore.dataset.nextOffset, 10) || 0;

        try {
            const data = await api.get(`/api/packs/history?offset=${offset}&limit=10`);
            prependOpenings(data.items);
            loadMore.dataset.nextOffset = String(data.next_offset);
            if (data.has_more === false) {
                loadMore.hidden = true;
            }
            if (messageEl) {
                showMessage(messageEl, 'status', '');
            }
        } catch (error) {
            removeRetry();
            if (messageEl) {
                showMessage(messageEl, 'error', messageFor(error));
            }
            announce(messageFor(error));
            retryBtn = document.createElement('button');
            retryBtn.type = 'button';
            retryBtn.textContent = 'Retry';
            retryBtn.className = 'button button--secondary';
            retryBtn.addEventListener('click', () => {
                removeRetry();
                loadMore.click();
            });
            loadMore.after(retryBtn);
        } finally {
            loading = false;
            loadMore.setAttribute('aria-busy', 'false');
        }
    });
}
