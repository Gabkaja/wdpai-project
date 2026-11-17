import { play } from '../sound.js';
import { announce } from './ui.js';

const TIME = {
    tear: 700,
    flip: 450,
};

const stage = document.querySelector('[data-stage]');
const reveal = document.querySelector('.packs__reveal');
const closeBtn = document.querySelector('.packs__stage-close');
const root = document.querySelector('[data-packs-root]');
const stageInner = stage ? stage.querySelector('.packs__stage-inner') : null;

const rarityLevels = {};
const rarityJson = document.querySelector('script[data-rarities]');
if (rarityJson) {
    try {
        for (const entry of JSON.parse(rarityJson.textContent)) {
            rarityLevels[entry.code] = entry.level;
        }
    } catch (error) {}
}

let reducedMotion = false;
const mq = window.matchMedia('(prefers-reduced-motion: reduce)');

let activeTrigger = null;
let openings = [];
let settings = { reveal_mode: 'auto', multi_open_mode: 'queue' };
let packIndex = 0;
let skipRequested = false;

let timers = [];
let listeners = [];
let packStates = [];

function trackTimer(t) {
    timers.push(t);
}

function clearTimers() {
    timers.forEach((t) => clearTimeout(t));
    timers = [];
}

function trackListener(el, type, handler) {
    listeners.push({ el, type, handler });
    el.addEventListener(type, handler);
}

function removeListeners() {
    listeners.forEach((l) => l.el.removeEventListener(l.type, l.handler));
    listeners = [];
}

function cleanup() {
    clearTimers();
    removeListeners();
    mq.removeEventListener('change', onReducedMotionChange);
}

window.addEventListener('pagehide', () => {
    cleanup();
});

function onReducedMotionChange() {
    reducedMotion = mq.matches;
}

function cardLevel(card) {
    return rarityLevels[card.rarity_code] ?? 1;
}

function bestDrop() {
    let best = null;
    for (const opening of openings) {
        for (const entry of opening.cards) {
            const level = cardLevel(entry.card);
            if (best === null || level > best.level) {
                best = { level, card: entry.card, isNew: entry.is_new };
            }
        }
    }
    return best;
}

function countNew() {
    let n = 0;
    for (const opening of openings) {
        for (const entry of opening.cards) {
            if (entry.is_new) {
                n++;
            }
        }
    }
    return n;
}

function totalCards() {
    let n = 0;
    for (const opening of openings) {
        n += opening.cards.length;
    }
    return n;
}

function createCard(openingIndex, cardIndex, entry) {
    const card = entry.card;
    const level = cardLevel(card);
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = `pack-card pack-card--level-${level}`;
    const total = openings[openingIndex].cards.length;
    btn.setAttribute('aria-label', `Reveal card ${cardIndex + 1} of ${total}`);
    btn.dataset.opening = String(openingIndex);
    btn.dataset.card = String(cardIndex);
    btn.dataset.revealed = 'false';

    const inner = document.createElement('div');
    inner.className = 'pack-card__inner';

    const back = document.createElement('div');
    back.className = 'pack-card__face pack-card__face--back';

    const front = document.createElement('div');
    front.className = 'pack-card__face pack-card__face--front';

    const icon = document.createElement('span');
    icon.className = 'pack-card__icon icon';
    icon.setAttribute('aria-hidden', 'true');
    icon.textContent = card.icon;

    const title = document.createElement('h3');
    title.className = 'pack-card__title';
    title.textContent = card.name;

    const rarity = document.createElement('span');
    rarity.className = 'pack-card__rarity';
    rarity.textContent = card.rarity_name;

    const desc = document.createElement('p');
    desc.className = 'pack-card__desc';
    desc.textContent = card.description;

    front.appendChild(icon);
    front.appendChild(title);
    front.appendChild(rarity);
    front.appendChild(desc);

    if (entry.is_new) {
        const badge = document.createElement('span');
        badge.className = 'pack-card__new';
        badge.setAttribute('aria-hidden', 'true');
        badge.textContent = 'NEW';
        front.appendChild(badge);
    }

    inner.appendChild(back);
    inner.appendChild(front);
    btn.appendChild(inner);

    return btn;
}

function revealCardUI(btn) {
    btn.dataset.revealed = 'true';
    btn.classList.add('pack-card--revealed');
    btn.disabled = true;

    const openingIndex = parseInt(btn.dataset.opening, 10);
    const cardIndex = parseInt(btn.dataset.card, 10);
    const entry = openings[openingIndex] ? openings[openingIndex].cards[cardIndex] : null;

    if (entry) {
        btn.setAttribute(
            'aria-label',
            `${entry.card.name}, ${entry.card.rarity_name}` + (entry.is_new ? ', new' : '')
        );
    }
}

function announceCard(btn) {
    const openingIndex = parseInt(btn.dataset.opening, 10);
    const cardIndex = parseInt(btn.dataset.card, 10);
    const entry = openings[openingIndex].cards[cardIndex];
    const card = entry.card;
    const total = openings[openingIndex].cards.length;
    const text = `Card ${cardIndex + 1} of ${total}: ${card.name}, ${card.rarity_name}` + (entry.is_new ? ', new' : '');
    announce(text);
}

function flipCard(btn, silent) {
    if (btn.dataset.revealed === 'true') {
        return;
    }
    revealCardUI(btn);
    if (!silent) {
        announceCard(btn);
        play('flip');
        const openingIndex = parseInt(btn.dataset.opening, 10);
        const cardIndex = parseInt(btn.dataset.card, 10);
        const entry = openings[openingIndex].cards[cardIndex];
        const level = cardLevel(entry.card);
        if (level >= 3) {
            play('reveal', { level });
        }
        if (entry.is_new) {
            play('new');
        }
        if (level >= 4 && stageInner) {
            flashStage();
        }
    }
}

function flashStage() {
    const flash = stageInner.querySelector('.packs__stage-flash');
    if (!flash) {
        return;
    }
    flash.classList.add('packs__stage-flash--active');
    const t = setTimeout(() => {
        flash.classList.remove('packs__stage-flash--active');
    }, 200);
    trackTimer(t);
}

function flipAllRemaining(cardBtns, fromIdx) {
    let bestLevel = 0;
    let hasNew = false;
    let revealed = 0;
    let newCards = 0;
    for (let i = fromIdx; i < cardBtns.length; i++) {
        const btn = cardBtns[i];
        if (btn.dataset.revealed === 'true') {
            continue;
        }
        revealCardUI(btn);
        revealed++;
        const openingIndex = parseInt(btn.dataset.opening, 10);
        const cardIndex = parseInt(btn.dataset.card, 10);
        const entry = openings[openingIndex].cards[cardIndex];
        if (entry.is_new) {
            hasNew = true;
            newCards++;
        }
        const level = cardLevel(entry.card);
        if (level > bestLevel) {
            bestLevel = level;
        }
    }
    announce(`${revealed} cards revealed, ${newCards} new.`);
    play('flip');
    if (bestLevel >= 3) {
        play('reveal', { level: bestLevel });
    }
    if (hasNew) {
        play('new');
    }
}

function autoFlip(cardBtns, fromIdx, onComplete) {
    let i = fromIdx;
    function step() {
        if (skipRequested) {
            flipAllRemaining(cardBtns, i);
            onComplete();
            return;
        }
        if (i >= cardBtns.length) {
            onComplete();
            return;
        }
        flipCard(cardBtns[i]);
        i++;
        const interval = reducedMotion ? 0 : TIME.flip;
        const t = setTimeout(step, interval);
        trackTimer(t);
    }
    step();
}

function createSeal(openingIndex) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'pack-seal';
    btn.setAttribute('aria-label', `Open pack ${openingIndex + 1}`);
    const front = document.createElement('div');
    front.className = 'pack-seal__front';
    btn.appendChild(front);
    return btn;
}

function tearSeal(sealBtn, onTorn) {
    play('tear');
    sealBtn.classList.add('pack-seal--tearing');
    const duration = reducedMotion ? 0 : TIME.tear;
    const t = setTimeout(() => {
        sealBtn.hidden = true;
        onTorn();
    }, duration);
    trackTimer(t);
}

function buildPack(openingIndex, container) {
    const opening = openings[openingIndex];
    const group = document.createElement('div');
    group.className = 'packs__pack-group';

    const head = document.createElement('div');
    head.className = 'packs__pack-header';
    head.textContent = `Pack ${openingIndex + 1} of ${openings.length}`;
    group.appendChild(head);

    const seal = createSeal(openingIndex);
    group.appendChild(seal);

    const cardsWrap = document.createElement('div');
    cardsWrap.className = 'packs__cards';
    cardsWrap.hidden = true;
    const cardBtns = [];
    for (let j = 0; j < opening.cards.length; j++) {
        const btn = createCard(openingIndex, j, opening.cards[j]);
        cardsWrap.appendChild(btn);
        cardBtns.push(btn);
    }
    group.appendChild(cardsWrap);

    container.appendChild(group);

    packStates[openingIndex] = {
        index: openingIndex,
        seal,
        cardsWrap,
        cardBtns,
        torn: false,
    };

    return packStates[openingIndex];
}

function packComplete(state) {
    return state.cardBtns.every((b) => b.dataset.revealed === 'true');
}

function allComplete() {
    return packStates.every((s) => s && packComplete(s));
}

function focusFirstAction() {
    const state = packStates[packIndex];
    if (!state) {
        return;
    }
    if (!state.torn) {
        state.seal.focus();
    } else {
        const first = state.cardBtns.find((b) => b.dataset.revealed !== 'true');
        if (first) {
            first.focus();
        }
    }
}

function sceneOpen() {
    document.dispatchEvent(new CustomEvent('packs:sceneopen'));
}

function sceneClose() {
    document.dispatchEvent(new CustomEvent('packs:sceneclose'));
}

export function close() {
    cleanup();
    while (reveal.firstChild) {
        reveal.removeChild(reveal.firstChild);
    }
    if (stage) {
        stage.setAttribute('hidden', '');
    }
    sceneClose();
    if (activeTrigger && typeof activeTrigger.focus === 'function') {
        activeTrigger.focus();
    }
    activeTrigger = null;
}

function showSummary() {
    clearTimers();
    while (reveal.firstChild) {
        reveal.removeChild(reveal.firstChild);
    }

    const panel = document.createElement('div');
    panel.className = 'packs__summary';

    const title = document.createElement('h2');
    title.className = 'packs__summary-title';
    title.textContent = 'Opening complete';
    panel.appendChild(title);

    const stats = document.createElement('p');
    stats.className = 'packs__summary-stats';
    stats.textContent = `${totalCards()} cards, ${countNew()} new`;
    panel.appendChild(stats);

    const best = bestDrop();
    if (best) {
        const bestLine = document.createElement('p');
        bestLine.className = 'packs__summary-best';
        bestLine.textContent = `Best drop: ${best.card.name} (${best.card.rarity_name})` + (best.isNew ? ' - new' : '');
        panel.appendChild(bestLine);
    }

    play('summary', { bestLevel: best ? best.level : 0 });

    const buttons = document.createElement('div');
    buttons.className = 'packs__summary-buttons';

    const summaryClose = document.createElement('button');
    summaryClose.type = 'button';
    summaryClose.className = 'button button--primary';
    summaryClose.textContent = 'Close';
    trackListener(summaryClose, 'click', close);
    buttons.appendChild(summaryClose);

    const stock = root ? (parseInt(root.dataset.stock, 10) || 0) : 0;
    const openAnother = document.createElement('button');
    openAnother.type = 'button';
    openAnother.className = 'button button--secondary';
    openAnother.textContent = 'Open another';
    openAnother.disabled = stock <= 0;
    trackListener(openAnother, 'click', () => {
        document.dispatchEvent(new CustomEvent('packs:openanother', { detail: { count: 1 } }));
    });
    buttons.appendChild(openAnother);

    panel.appendChild(buttons);
    reveal.appendChild(panel);
    summaryClose.focus();
}

export function show(openingsIn, settingsIn) {
    cleanup();
    openings = openingsIn;
    settings = settingsIn || { reveal_mode: 'auto', multi_open_mode: 'queue' };
    packIndex = 0;
    skipRequested = false;
    packStates = [];
    activeTrigger = document.activeElement;

    trackListener(mq, 'change', onReducedMotionChange);
    reducedMotion = mq.matches;

    if (stage) {
        stage.setAttribute('role', 'region');
        stage.setAttribute('aria-label', 'Pack opening');
    }

    sceneOpen();

    while (reveal.firstChild) {
        reveal.removeChild(reveal.firstChild);
    }

    const wrapper = document.createElement('div');
    wrapper.className = 'packs__scene';

    const queue = settings.multi_open_mode === 'queue';
    const auto = settings.reveal_mode === 'auto';

    if (queue) {
        buildPack(0, wrapper);
    } else {
        const grid = document.createElement('div');
        grid.className = 'packs__packs-grid';
        for (let i = 0; i < openings.length; i++) {
            buildPack(i, grid);
        }
        wrapper.appendChild(grid);
    }

    reveal.appendChild(wrapper);
    stage.removeAttribute('hidden');

    if (stageInner && !stageInner.querySelector('.packs__stage-flash')) {
        const flash = document.createElement('div');
        flash.className = 'packs__stage-flash';
        stageInner.insertBefore(flash, reveal);
    }

    if (closeBtn) {
        trackListener(closeBtn, 'click', close);
    }

    if (queue) {
        wireQueuePack(packStates[0], wrapper);
    } else {
        wireAllPacks(auto, wrapper);
    }
}

function wireQueuePack(state, wrapper) {
    const auto = settings.reveal_mode === 'auto';
    let torn = false;

    const controls = document.createElement('div');
    controls.className = 'packs__controls';

    const skipBtn = document.createElement('button');
    skipBtn.type = 'button';
    skipBtn.className = 'button button--secondary packs__skip';
    skipBtn.textContent = auto ? 'Skip animation' : 'Reveal all';
    controls.appendChild(skipBtn);

    const nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'button button--primary packs__next-pack';
    nextBtn.textContent = 'Next pack';
    nextBtn.hidden = true;
    controls.appendChild(nextBtn);

    wrapper.appendChild(controls);

    function doTear() {
        if (torn) {
            return;
        }
        torn = true;
        tearSeal(state.seal, () => {
            state.cardsWrap.hidden = false;
            if (auto) {
                if (reducedMotion) {
                    flipAllRemaining(state.cardBtns, 0);
                    onPackDone();
                } else {
                    autoFlip(state.cardBtns, 0, () => onPackDone());
                }
            } else {
                focusFirstAction();
            }
        });
    }

    if (auto) {
        const t = setTimeout(() => doTear(), reducedMotion ? 0 : TIME.tear);
        trackTimer(t);
    } else {
        trackListener(state.seal, 'click', doTear);
        trackListener(state.seal, 'keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                doTear();
            }
        });
    }

    trackListener(skipBtn, 'click', () => {
        skipRequested = true;
        clearTimers();
        state.torn = true;
        state.seal.hidden = true;
        state.cardsWrap.hidden = false;
        flipAllRemaining(state.cardBtns, 0);
        const t = setTimeout(() => onPackDone(), reducedMotion ? 0 : 100);
        trackTimer(t);
    });

    for (const btn of state.cardBtns) {
        trackListener(btn, 'click', () => {
            if (auto) {
                return;
            }
            flipCard(btn);
            if (packComplete(state)) {
                onPackDone();
            }
        });
        trackListener(btn, 'keydown', (event) => {
            if (auto) {
                return;
            }
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                flipCard(btn);
                if (packComplete(state)) {
                    onPackDone();
                }
            }
        });
    }

    trackListener(nextBtn, 'click', () => {
        skipRequested = false;
        packIndex++;
        if (packIndex >= openings.length) {
            showSummary();
        } else {
            while (wrapper.firstChild) {
                wrapper.removeChild(wrapper.firstChild);
            }
            buildPack(packIndex, wrapper);
            wireQueuePack(packStates[packIndex], wrapper);
            focusFirstAction();
        }
    });

    function onPackDone() {
        state.complete = true;
        skipBtn.hidden = true;
        if (packIndex === openings.length - 1) {
            showSummary();
        } else {
            nextBtn.hidden = false;
            nextBtn.focus();
        }
    }

    focusFirstAction();
}

function wireAllPacks(auto, wrapper) {
    let active = 0;

    const controls = document.createElement('div');
    controls.className = 'packs__controls';
    const skipBtn = document.createElement('button');
    skipBtn.type = 'button';
    skipBtn.className = 'button button--secondary packs__skip';
    skipBtn.textContent = auto ? 'Skip animation' : 'Reveal all';
    controls.appendChild(skipBtn);
    wrapper.appendChild(controls);

    function tearNext() {
        while (active < openings.length && packStates[active] && packStates[active].torn) {
            active++;
        }
        if (active >= openings.length) {
            if (allComplete()) {
                showSummary();
            }
            return;
        }
        const state = packStates[active];
        state.torn = true;
        tearSeal(state.seal, () => {
            state.cardsWrap.hidden = false;
            if (auto) {
                if (reducedMotion) {
                    flipAllRemaining(state.cardBtns, 0);
                    active++;
                    tearNext();
                } else {
                    autoFlip(state.cardBtns, 0, () => {
                        active++;
                        tearNext();
                    });
                }
            }
        });
    }

    trackListener(skipBtn, 'click', () => {
        skipRequested = true;
        clearTimers();
        const allBtns = [];
        for (const state of packStates) {
            if (!state) {
                continue;
            }
            state.torn = true;
            state.seal.hidden = true;
            state.cardsWrap.hidden = false;
            allBtns.push(...state.cardBtns);
        }
        flipAllRemaining(allBtns, 0);
        const t = setTimeout(() => {
            if (allComplete()) {
                showSummary();
            }
        }, reducedMotion ? 0 : 100);
        trackTimer(t);
    });

    if (auto) {
        tearNext();
    } else {
        for (const state of packStates) {
            if (!state) {
                continue;
            }
            trackListener(state.seal, 'click', () => {
                if (state.torn) {
                    return;
                }
                state.torn = true;
                tearSeal(state.seal, () => {
                    state.cardsWrap.hidden = false;
                });
            });
            trackListener(state.seal, 'keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    if (state.torn) {
                        return;
                    }
                    state.torn = true;
                    tearSeal(state.seal, () => {
                        state.cardsWrap.hidden = false;
                    });
                }
            });
        }
        for (const state of packStates) {
            if (!state) {
                continue;
            }
            for (const btn of state.cardBtns) {
                trackListener(btn, 'click', () => {
                    flipCard(btn);
                    if (allComplete()) {
                        showSummary();
                    }
                });
                trackListener(btn, 'keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        flipCard(btn);
                        if (allComplete()) {
                            showSummary();
                        }
                    }
                });
            }
        }
    }

    focusFirstAction();
}
