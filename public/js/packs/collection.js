import { api } from '../api.js';
import { messageFor } from './messages.js';
import { announce } from './ui.js';

const root = document.querySelector('[data-packs-root]');
if (!root) {
  return;
}

const section = document.querySelector('section.collection');
if (!section) {
  return;
}

const rarityMap = new Map();
const rarityJson = document.querySelector('script[data-rarities]');
if (rarityJson) {
  try {
    for (const entry of JSON.parse(rarityJson.textContent)) {
      rarityMap.set(entry.code, entry.level);
    }
  } catch (error) {}
}

const list = section.querySelector('[data-collection-list]');
const template = section.querySelector('template[data-template="collection-item"]');
const raritySelect = section.querySelector('#collection-rarity');
const sortSelect = section.querySelector('#collection-sort');
const searchInput = section.querySelector('#collection-search');
const loadMore = section.querySelector('[data-action="load-more"]');
const summaryUnique = section.querySelector('[data-summary="unique"]');
const summaryTotal = section.querySelector('[data-summary="total"]');
const summaryQuantity = section.querySelector('[data-summary="quantity"]');
const loadingEl = section.querySelector('.collection__loading');
const emptyEl = section.querySelector('.collection__empty');
const emptyText = section.querySelector('.collection__empty-text');
const errorEl = section.querySelector('.collection__error');
const errorMessage = section.querySelector('.collection__error-message');
const retryBtn = section.querySelector('.collection__retry');
const live = section.querySelector('[data-live]');

let offset = 0;
let hasMore = true;
let loading = false;
let requestId = 0;
let params = { rarity: '', q: '', sort: 'rarity' };

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
  return `/api/collection?${qs.toString()}`;
}

function setSummary(summary) {
  if (summaryUnique) {
    summaryUnique.textContent = String(summary.unique_owned ?? 0);
  }
  if (summaryTotal) {
    summaryTotal.textContent = String(summary.unique_total ?? 0);
  }
  if (summaryQuantity) {
    summaryQuantity.textContent = String(summary.total_quantity ?? 0);
  }
}

function showLoading(show) {
  if (loadingEl) {
    loadingEl.hidden = !show;
  }
  if (loadMore) {
    loadMore.disabled = show;
    loadMore.setAttribute('aria-busy', String(show));
  }
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
  announce(message);
}

function hideError() {
  errorEl.hidden = true;
}

function renderItems(items) {
  const fragment = document.createDocumentFragment();
  for (const item of items) {
    const clone = document.importNode(template.content, true);
    const card = clone.querySelector('.collection__card');
    const chip = clone.querySelector('.collection__chip');
    const name = clone.querySelector('.collection__name');
    const quantity = clone.querySelector('.collection__quantity');

    chip.className = `collection__chip rarity-chip rarity-chip--level-${rarityMap.get(item.rarity_code) ?? 1}`;
    chip.textContent = item.rarity_code.toUpperCase();
    name.textContent = item.name;
    quantity.textContent = `×${item.quantity}`;

    fragment.appendChild(card);
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
  }

  try {
    const data = await api.get(buildUrl());
    if (id !== requestId) {
      return;
    }
    setSummary(data.summary);
    renderItems(data.items);
    hasMore = data.has_more === true;
    offset = data.next_offset;

    const total = data.summary?.unique_owned ?? 0;
    if (total === 0 || (reset && data.items.length === 0)) {
      showEmpty(params.rarity || params.q ? 'No cards match your filters.' : 'You don\'t have any cards yet.');
    } else {
      hideEmpty();
    }
    if (loadMore) {
      loadMore.hidden = !hasMore;
    }
    showLoading(false);
    if (live) {
      live.textContent = '';
    }
  } catch (error) {
    if (id !== requestId) {
      return;
    }
    showLoading(false);
    showError(messageFor(error));
    if (live) {
      live.textContent = messageFor(error);
    }
  } finally {
    if (id === requestId) {
      loading = false;
    }
  }
}

if (list && template && raritySelect && sortSelect && searchInput) {
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
