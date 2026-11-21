export function readRarityLevels(root = document) {
    const levels = new Map();
    const json = root.querySelector('script[data-rarities]');

    if (json) {
        try {
            for (const entry of JSON.parse(json.textContent)) {
                levels.set(entry.code, entry.level);
            }
        } catch (error) {}
    }

    return levels;
}

export function createCollectionItem(template, item, rarityLevels) {
    const clone = document.importNode(template.content, true);
    const card = clone.querySelector('.collection__card');
    const chip = clone.querySelector('.collection__chip');
    const name = clone.querySelector('.collection__name');
    const quantity = clone.querySelector('.collection__quantity');

    chip.className = `collection__chip rarity-chip rarity-chip--level-${rarityLevels.get(item.rarity_code) ?? 1}`;
    chip.textContent = item.rarity_code.toUpperCase();
    name.textContent = item.name;
    quantity.textContent = `×${item.quantity}`;

    return card;
}
