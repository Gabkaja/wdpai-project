<ul class="card-grid" role="list">
    <?php foreach ($cards as $card): ?>
        <li>
            <article class="card rarity-<?= htmlspecialchars($card->getRarityCode()) ?>" data-rarity="<?= htmlspecialchars($card->getRarityCode()) ?>">
                <div class="card__media">
                    <span class="card__code">#<?= htmlspecialchars($card->getCode()) ?></span>
                    <span class="card__icon">
                        <span class="icon" aria-hidden="true"><?= htmlspecialchars($card->getIcon()) ?></span>
                    </span>
                </div>

                <div class="card__body">
                    <div class="card__content">
                        <div class="card__heading">
                            <h3 class="card__title">
                                <a class="card__link" href="/cards/<?= (int) $card->getId() ?>"><?= htmlspecialchars($card->getName()) ?></a>
                            </h3>
                            <span class="rarity-badge"><?= htmlspecialchars(ucfirst($card->getRarityCode())) ?></span>
                        </div>
                        <p class="card__description"><?= htmlspecialchars($card->getDescription()) ?></p>
                    </div>

                    <p class="card__drop">
                        <span class="card__drop-label">Drop chance</span>
                        <span class="card__drop-value"><?= number_format($card->getDropChance(), 1) ?>%</span>
                    </p>
                </div>
            </article>
        </li>
    <?php endforeach; ?>
</ul>
