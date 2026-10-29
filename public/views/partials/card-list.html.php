<ul class="card-grid" role="list">
    <?php foreach ($cards as $card): ?>
        <li>
            <article class="card rarity-<?= htmlspecialchars($card['rarity']) ?>" data-rarity="<?= htmlspecialchars($card['rarity']) ?>">
                <div class="card__media">
                    <span class="card__code">#<?= htmlspecialchars($card['code']) ?></span>
                    <span class="card__icon">
                        <span class="icon" aria-hidden="true"><?= htmlspecialchars($card['icon']) ?></span>
                    </span>
                </div>

                <div class="card__body">
                    <div class="card__content">
                        <div class="card__heading">
                            <h3 class="card__title">
                                <a class="card__link" href="/cards/<?= (int) $card['id'] ?>"><?= htmlspecialchars($card['name']) ?></a>
                            </h3>
                            <span class="rarity-badge"><?= htmlspecialchars(ucfirst($card['rarity'])) ?></span>
                        </div>
                        <p class="card__description"><?= htmlspecialchars($card['description']) ?></p>
                    </div>

                    <p class="card__drop">
                        <span class="card__drop-label">Drop chance</span>
                        <span class="card__drop-value"><?= number_format($card['dropChance'], 1) ?>%</span>
                    </p>
                </div>
            </article>
        </li>
    <?php endforeach; ?>
</ul>