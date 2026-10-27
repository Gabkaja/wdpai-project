<ul>
    <?php foreach ($cards as $card): ?>
        <li>
            <article data-rarity="<?= htmlspecialchars($card['rarity']) ?>">
                <h3><?= htmlspecialchars($card['name']) ?></h3>
                <p><?= htmlspecialchars($card['description']) ?></p>
                <dl>
                    <dt>Rarity</dt>
                    <dd><?= htmlspecialchars(ucfirst($card['rarity'])) ?></dd>
                    <dt>Drop chance</dt>
                    <dd><?= (int) $card['dropChance'] ?>%</dd>
                </dl>
                <a href="/cards/<?= (int) $card['id'] ?>">View card</a>
            </article>
        </li>
    <?php endforeach; ?>
</ul>