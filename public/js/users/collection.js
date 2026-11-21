import { api } from '../api.js';
import { createCollectionItem, readRarityLevels } from '../shared/collection-item.js';
import { messageFor } from '../packs/messages.js';

(() => {
    const section = document.querySelector('[data-user-collection]');
    if (!section) {
        return;
    }

    const userId = section.getAttribute('data-user-id');
    const list = section.querySelector('[data-collection-list]');
    const template = section.querySelector('template[data-template="collection-item"]');
    const raritySelect = section.querySelector('#user-collection-rarity');
    const sortSelect = section.querySelector('#user-collection-sort');
    const searchInput = section.querySelector('#user-collection-search');
    const loadMore = section.querySelector('[data-action="load-more"]');
    const loadingEl = section.querySelector('.collection__loading');
    const emptyEl = section.querySelector('.collection__empty');
    const emptyText = section.querySelector('.collection__empty-text');
    const errorEl = section.querySelector('.collection__error');
    const errorMessage = section.querySelector('.collection__error-message');
    const retryBtn = section.querySelector('.collection__retry');
    const live = section.querySelector('[data-user-collection-live]');

    const rarityLevels = readRarityLevels(document);

    let offset = 0;
    let hasMore = true;
    let loading = false;
    let requestId = 0;
    const params = { rarity: '', q: '', sort: 'rarity' };

    function buildUrl() {
        const qs = new URLSearchParams();
        if (params.rarity) {
            qs.set('rarity', params.rarity);
        }
        if (params.q) {
            qs.set('q', params.q);
        }
        qs.set('sort', params.sort);
        qs.set('offset', String(offset));
        return `/api/users/${encodeURIComponent(userId)}/collection?${qs.toString()}`;
    }

    function showLoading(show) {
        if (loadingEl) {
            loadingEl.hidden = !show;
        }
        if (loadMore) {
            loadMore.disabled = show;
            loadMore.setAttribute('aria-busy', String(show));
        }
        section.setAttribute('aria-busy', String(show));
    }

    function showEmpty(text) {
        emptyText.textContent = text;
        emptyEl.hidden = false;
        list.hidden = true;
        if (loadMore) {
            loadMore.hidden = true;
        }
    }

    function hideEmpty() {
        emptyEl.hidden = true;
        list.hidden = false;
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorEl.hidden = false;
        list.hidden = true;
        if (loadMore) {
            loadMore.hidden = true;
        }
        emptyEl.hidden = true;
        if (live) {
            live.textContent = message;
        }
    }

    function hideError() {
        errorEl.hidden = true;
    }

    function renderItems(items) {
        const fragment = document.createDocumentFragment();
        for (const item of items) {
            fragment.appendChild(createCollectionItem(template, item, rarityLevels));
        }
        list.appendChild(fragment);
    }

    async function load(reset) {
        if (loading) {
            return;
        }

        const id = ++requestId;
        loading = true;

        if (reset) {
            offset = 0;
            hasMore = true;
            list.replaceChildren();
            hideError();
            if (loadMore) {
                loadMore.hidden = false;
            }
            showLoading(true);
            if (live) {
                live.textContent = 'Loading collection…';
            }
        } else {
            if (loadMore) {
                loadMore.disabled = true;
                loadMore.setAttribute('aria-busy', 'true');
            }
            section.setAttribute('aria-busy', 'true');
            if (live) {
                live.textContent = 'Loading more cards…';
            }
        }

        try {
            const data = await api.get(buildUrl());
            if (id !== requestId) {
                return;
            }

            renderItems(data.items);
            hasMore = data.has_more === true;
            offset = data.next_offset;

            const total = data.summary?.unique_owned ?? 0;
            if (total === 0) {
                showEmpty('This collection is empty.');
            } else if (reset && data.items.length === 0) {
                showEmpty('No cards match your filters.');
            } else {
                hideEmpty();
            }

            if (loadMore) {
                loadMore.hidden = !hasMore;
            }

            if (live) {
                live.textContent = '';
            }
        } catch (error) {
            if (id !== requestId) {
                return;
            }
            showError(messageFor(error));
        } finally {
            if (id === requestId) {
                loading = false;
                showLoading(false);
            }
        }
    }

    if (list && template && raritySelect && sortSelect && searchInput && loadMore) {
        let debounceTimer = null;

        raritySelect.addEventListener('change', () => {
            params.rarity = raritySelect.value;
            load(true);
        });

        sortSelect.addEventListener('change', () => {
            params.sort = sortSelect.value;
            load(true);
        });

        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                params.q = searchInput.value.trim();
                load(true);
            }, 300);
        });

        loadMore.addEventListener('click', () => {
            load(false);
        });

        retryBtn.addEventListener('click', () => {
            hideError();
            load(true);
        });

        load(true);
    }
})();
