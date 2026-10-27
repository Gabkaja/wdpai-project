<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main>
        <h1>Card catalog</h1>

        <section aria-labelledby="live-heading">
            <h2 id="live-heading">Stream status</h2>
            <p>
                <?php if ($isLive): ?>
                    The stream is <strong>live</strong> right now.
                <?php else: ?>
                    The stream is currently offline.
                <?php endif; ?>
                <a href="<?= htmlspecialchars($channelUrl) ?>">Open the Kick channel</a>
            </p>
        </section>

        <section aria-labelledby="catalog-heading">
            <h2 id="catalog-heading">All cards</h2>

            <form action="/" method="get">
                <label for="rarity-filter">Filter by rarity</label>
                <select id="rarity-filter" name="rarity">
                    <option value="">All</option>
                    <option value="common">Common</option>
                    <option value="rare">Rare</option>
                    <option value="epic">Epic</option>
                    <option value="legendary">Legendary</option>
                </select>
                <button type="submit">Filter</button>
            </form>

            <?php $this->partial('card-list', ['cards' => $cards]); ?>
        </section>
    </main>

<?php $this->partial('footer'); ?>