    <main class="stack">
        <section class="stream-banner" aria-labelledby="about-title">
            <div class="stream-banner__inner">
                <div class="stream-banner__info">
                    <div class="stream-banner__tags">
                        <span class="badge badge--live">
                            <span class="pulse" aria-hidden="true"></span>
                            About
                        </span>
                        <span class="badge badge--category">Season 1</span>
                    </div>

                    <h1 class="stream-banner__title" id="about-title">Cards that drop while you watch</h1>

                    <ul class="stream-banner__status" role="list">
                        <li class="is-accent">
                            <span class="icon" aria-hidden="true">sensors</span>
                            <span>Built for the Kick chat: collect, trade and show off your pulls.</span>
                        </li>
                        <li>
                            <span class="icon" aria-hidden="true">bolt</span>
                            <span>Drops are synced with the live broadcast, not with your wallet.</span>
                        </li>
                        <li>
                            <span class="icon" aria-hidden="true">groups</span>
                            <span>One shared catalog, one leaderboard, every viewer on the same pool.</span>
                        </li>
                    </ul>
                </div>

                <div class="stream-banner__stats">
                    <div class="viewer-count">
                        <span class="viewer-count__icon">
                            <span class="icon" aria-hidden="true">inventory_2</span>
                        </span>
                        <div>
                            <strong class="viewer-count__value"><?= (int) $cardCount ?></strong>
                            <span class="viewer-count__label">Cards in catalog</span>
                        </div>
                    </div>

                    <p class="stream-banner__server">
                        <span class="dot dot--sync" aria-hidden="true"></span>
                        <?= htmlspecialchars($edition) ?>
                    </p>
                </div>
            </div>
        </section>

        <section class="catalog" aria-labelledby="how-heading">
            <h2 id="how-heading">How StreamRip works</h2>

            <ul class="card-grid" role="list">
                <?php foreach ($features as $feature): ?>
                    <li>
                        <article class="card rarity-<?= htmlspecialchars($feature['rarity']) ?>">
                            <div class="card__media">
                                <span class="card__icon">
                                    <span class="icon" aria-hidden="true"><?= htmlspecialchars($feature['icon']) ?></span>
                                </span>
                            </div>

                            <div class="card__body">
                                <div class="card__content">
                                    <div class="card__heading">
                                        <h3 class="card__title"><?= htmlspecialchars($feature['title']) ?></h3>
                                    </div>
                                    <p class="card__description"><?= htmlspecialchars($feature['description']) ?></p>
                                </div>
                            </div>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p>
                <a class="button button--primary" href="/">Explore the catalog</a>
            </p>
        </section>
    </main>
