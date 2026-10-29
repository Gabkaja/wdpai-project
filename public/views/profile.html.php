<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main>
        <h1>Profile of <?= htmlspecialchars($username) ?></h1>

        <section aria-labelledby="stats-heading">
            <h2 id="stats-heading">Player statistics</h2>
            <dl>
                <dt>Cards collected</dt>
                <dd><?= (int) $stats['totalCards'] ?></dd>
                <dt>Collection completion</dt>
                <dd><?= (int) $stats['completion'] ?>%</dd>
                <dt>Community rank</dt>
                <dd>#<?= (int) $stats['rank'] ?></dd>
                <dt>Favorite card</dt>
                <dd><?= htmlspecialchars($stats['favoriteCard']) ?></dd>
            </dl>
        </section>

        <section aria-labelledby="avatar-heading">
            <h2 id="avatar-heading">Avatar</h2>
            <p>No avatar uploaded yet.</p>
            <form method="post" action="/profile/avatar" enctype="multipart/form-data">
                <label for="avatar-file">Choose a profile picture</label>
                <input type="file" id="avatar-file" name="avatar" accept="image/*" required>
                <button type="submit">Upload avatar</button>
            </form>
        </section>

        <section aria-labelledby="collection-heading">
            <h2 id="collection-heading">My collection</h2>
            <ul>
                <?php foreach ($cards as $card): ?>
                    <li>
                        <?php if (in_array($card['id'], $ownedIds, true)): ?>
                            <article data-rarity="<?= htmlspecialchars($card['rarity']) ?>">
                                <h3><?= htmlspecialchars($card['name']) ?></h3>
                                <p>Rarity: <?= htmlspecialchars(ucfirst($card['rarity'])) ?></p>
                                <a href="/cards/<?= (int) $card['id'] ?>">View card</a>
                            </article>
                        <?php else: ?>
                            <article data-locked="true" aria-label="Locked card">
                                <h3>???</h3>
                                <p>Not collected yet.</p>
                            </article>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section aria-labelledby="redeem-heading">
            <h2 id="redeem-heading">Redeem a promo code</h2>
            <form method="post" action="/profile/redeem">
                <label for="promo-code">Promo code</label>
                <input type="text" id="promo-code" name="code" required autocomplete="off">
                <button type="submit">Redeem</button>
            </form>
            <p role="status" aria-live="polite"></p>
        </section>
    </main>

<?php $this->partial('footer'); ?>