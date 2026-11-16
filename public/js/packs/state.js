const root = document.querySelector('[data-packs-root]');

const config = {
    cost: 0,
    maxBulk: 10,
    cardsPerPack: 4,
    points: 0,
    stock: 0,
};

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
    const max = Math.min(config.stock, config.maxBulk);

    if (countInput) {
        countInput.disabled = disabled;
        countInput.max = max;
        if (parseInt(countInput.value, 10) > max) {
            countInput.value = max;
        }
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
        const value = Math.max(1, Math.min(parseInt(input.value, 10) || 1, config.maxBulk));
        input.value = value;
        output.textContent = String(value * config.cost);
    }

    input.addEventListener('input', recalc);
    input.addEventListener('change', recalc);
    recalc();
}

if (root) {
    config.cost = parseInt(root.dataset.cost, 10) || 0;
    config.maxBulk = parseInt(root.dataset.maxBulk, 10) || 10;
    config.cardsPerPack = parseInt(root.dataset.cardsPerPack, 10) || 4;
    config.points = parseInt(root.dataset.points, 10) || 0;
    config.stock = parseInt(root.dataset.stock, 10) || 0;

    updatePoints(config.points);
    updateStock(config.stock);
    setupCostOutput();
}
