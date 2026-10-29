<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main>
        <article data-rarity="<?= htmlspecialchars($card['rarity']) ?>">
            <h1><?= htmlspecialchars($card['name']) ?></h1>
            <p><?= htmlspecialchars($card['description']) ?></p>
            <dl>
                <dt>Card ID (from URL)</dt>
                <dd><?= (int) $card['id'] ?></dd>
                <dt>Rarity</dt>
                <dd><?= htmlspecialchars(ucfirst($card['rarity'])) ?></dd>
                <dt>Drop chance</dt>
                <dd><?= number_format($card['dropChance'], 1) ?>%</dd>
            </dl>
            <a href="/cards">Back to all cards</a>
        </article>
    </main>

<?php $this->partial('footer'); ?>