const root = document.querySelector('[data-packs-root]');

export const config = {
    cost: 0,
    maxBulk: 10,
    maxPurchase: 500,
    cardsPerPack: 4,
    points: 0,
    stock: 0,
};

const SLIDER_STOPS = [1, 2, 3, 5, 8, 10, 15, 20, 25, 30, 40, 50, 75, 100, 150, 200, 300, 400, 500, 750, 1000, 1500, 2000];

let openSliderSync = null;

function sliderStops(max) {
    const stops = [];
    for (const stop of SLIDER_STOPS) {
        if (stop < max) {
            stops.push(stop);
        }
    }
    if (stops.length === 0 || stops[stops.length - 1] !== max) {
        stops.push(max);
    }
    return stops;
}

function nearestStop(stops, value) {
    let best = stops[0];
    for (const stop of stops) {
        if (Math.abs(stop - value) < Math.abs(best - value)) {
            best = stop;
        }
    }
    return best;
}

function renderTicks(container, stops, max) {
    if (!container) {
        return;
    }
    container.replaceChildren();
    if (max <= 1) {
        return;
    }
    for (const stop of stops) {
        const tick = document.createElement('span');
        tick.className = 'packs__slider-tick';
        tick.style.left = `${((stop - 1) / (max - 1)) * 100}%`;
        container.appendChild(tick);
    }
}

function setupSlider(input) {
    if (!input) {
        return null;
    }
    const key = input.dataset.slider;
    const output = document.querySelector(`[data-output="${key}"]`);
    const ticks = document.querySelector(`[data-ticks="${key}"]`);

    const sync = () => {
        const max = Math.max(1, parseInt(input.max, 10) || 1);
        const stops = sliderStops(max);
        const raw = parseInt(input.value, 10) || 1;
        const clamped = Math.min(Math.max(raw, 1), max);
        const value = nearestStop(stops, clamped);

        if (String(value) !== input.value) {
            input.value = String(value);
        }
        if (output) {
            output.textContent = String(value);
        }
        renderTicks(ticks, stops, max);
        input.setAttribute('aria-valuetext', `${value} pack${value === 1 ? '' : 's'}`);
    };

    input.addEventListener('input', sync);
    input.addEventListener('change', sync);
    sync();

    return sync;
}

export function updatePoints(points) {
    config.points = points;
    if (root) {
        root.dataset.points = String(points);
    }
    const el = document.querySelector('[data-stat="points"]');
    if (el) {
        el.textContent = String(points);
    }
}

export function updateStock(stock) {
    config.stock = stock;
    if (root) {
        root.dataset.stock = String(stock);
    }
    const el = document.querySelector('[data-stat="stock"]');
    if (el) {
        el.textContent = String(stock);
    }
    setupOpenControls();
}

export function setupOpenControls() {
    const form = document.querySelector('form[data-form="open"]');
    if (!form || !root) {
        return;
    }

    const countInput = form.querySelector('input[name="count"]');
    const submitBtn = form.querySelector('button[type="submit"]');
    const openAllBtn = form.querySelector('button[data-action="open-all"]');
    const disabled = config.stock <= 0;
    const max = Math.max(1, Math.min(config.stock, config.maxBulk));

    if (countInput) {
        countInput.disabled = disabled;
        countInput.max = max;
        if ((parseInt(countInput.value, 10) || 1) > max) {
            countInput.value = String(max);
        }
    }

    if (openSliderSync) {
        openSliderSync();
    }

    if (submitBtn) {
        submitBtn.disabled = disabled;
    }
    if (openAllBtn) {
        openAllBtn.disabled = disabled;
    }
}

export function setupCostOutput() {
    const form = document.querySelector('form[data-form="buy"]');
    if (!form) {
        return;
    }

    const input = form.querySelector('input[name="quantity"]');
    const output = form.querySelector('output[data-total-cost]');
    if (!input || !output) {
        return;
    }

    function recalc() {
        const max = Math.max(1, parseInt(input.max, 10) || config.maxPurchase);
        const value = Math.max(1, Math.min(parseInt(input.value, 10) || 1, max));
        input.value = String(value);
        output.textContent = String(value * config.cost);
    }

    input.addEventListener('input', recalc);
    input.addEventListener('change', recalc);
    recalc();
}

if (root) {
    config.cost = parseInt(root.dataset.cost, 10) || 0;
    config.maxBulk = parseInt(root.dataset.maxBulk, 10) || 10;
    config.maxPurchase = parseInt(root.dataset.maxPurchase, 10) || 500;
    config.cardsPerPack = parseInt(root.dataset.cardsPerPack, 10) || 4;
    config.points = parseInt(root.dataset.points, 10) || 0;
    config.stock = parseInt(root.dataset.stock, 10) || 0;

    setupSlider(document.querySelector('[data-slider="buy-quantity"]'));
    openSliderSync = setupSlider(document.querySelector('[data-slider="open-count"]'));

    updatePoints(config.points);
    updateStock(config.stock);
    setupCostOutput();
}
