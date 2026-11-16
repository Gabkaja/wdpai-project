const stage = document.querySelector('[data-stage]');
const reveal = document.querySelector('.packs__reveal');
const closeBtn = document.querySelector('.packs__stage-close');

export function show(openings) {
    while (reveal.firstChild) {
        reveal.removeChild(reveal.firstChild);
    }

    const list = document.createElement('ul');
    list.className = 'packs__reveal-list';

    for (const opening of openings) {
        const item = document.createElement('li');
        item.className = 'packs__reveal-item';

        const title = document.createElement('h3');
        title.textContent = `Opening #${opening.id}`;
        item.appendChild(title);

        const cardsList = document.createElement('ul');
        cardsList.className = 'packs__reveal-cards';

        for (const entry of opening.cards) {
            const card = entry.card;
            const li = document.createElement('li');
            li.className = 'packs__reveal-card';

            const chip = document.createElement('span');
            chip.className = 'rarity-chip';
            chip.textContent = card.rarity_code.toUpperCase();
            li.appendChild(chip);

            const name = document.createElement('span');
            name.className = 'packs__reveal-card-name';
            name.textContent = card.name;
            li.appendChild(name);

            if (entry.is_new) {
                const badge = document.createElement('span');
                badge.className = 'packs__reveal-new';
                badge.setAttribute('aria-label', 'New card');
                badge.textContent = 'NEW';
                li.appendChild(badge);
            }

            cardsList.appendChild(li);
        }

        item.appendChild(cardsList);
        list.appendChild(item);
    }

    reveal.appendChild(list);
    stage.removeAttribute('hidden');
}

export function close() {
    while (reveal.firstChild) {
        reveal.removeChild(reveal.firstChild);
    }
    stage.setAttribute('hidden', '');
}

if (closeBtn) {
    closeBtn.addEventListener('click', close);
}
