<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main class="stack">
        <h1 class="visually-hidden">StreamRip card catalog</h1>

        <section class="stream-banner" aria-labelledby="stream-title">
            <div class="stream-banner__inner">
                <div class="stream-banner__info">
                    <div class="stream-banner__tags">
                        <?php if ($stream['isLive']): ?>
                            <span class="badge badge--live">
                                <span class="pulse" aria-hidden="true"></span>
                                Live
                            </span>
                        <?php else: ?>
                            <span class="badge badge--offline">Offline</span>
                        <?php endif; ?>

                        <a class="badge badge--channel" href="<?= htmlspecialchars($stream['channelUrl']) ?>" rel="noopener">
                            <span class="icon" aria-hidden="true">sensors</span>
                            <?= htmlspecialchars($stream['channel']) ?>
                        </a>

                        <span class="badge badge--category"><?= htmlspecialchars($stream['category']) ?></span>
                    </div>

                    <h2 class="stream-banner__title" id="stream-title"><?= htmlspecialchars($stream['title']) ?></h2>

                    <ul class="stream-banner__status" role="list">
                        <li class="is-accent">
                            <span class="icon" aria-hidden="true">sync_alt</span>
                            <span>Kick chat connection: <strong><?= htmlspecialchars($stream['chatStatus']) ?></strong></span>
                        </li>
                        <li>
                            <span class="icon" aria-hidden="true">bolt</span>
                            <span>Cards drop automatically during the live broadcast!</span>
                        </li>
                    </ul>
                </div>

                <div class="stream-banner__stats">
                    <div class="viewer-count">
                        <span class="viewer-count__icon">
                            <span class="icon" aria-hidden="true">visibility</span>
                        </span>
                        <div>
                            <strong class="viewer-count__value"><?= number_format($stream['viewers']) ?></strong>
                            <span class="viewer-count__label">Viewers live</span>
                        </div>
                    </div>

                    <p class="stream-banner__server">
                        <span class="dot dot--sync" aria-hidden="true"></span>
                        <?= htmlspecialchars($stream['server']) ?>
                    </p>
                </div>
            </div>
        </section>

                <section class="catalog" aria-labelledby="catalog-heading">
            <h2 id="catalog-heading" class="visually-hidden">Browse all cards</h2>

            <div class="catalog__controls">
                <form class="search" role="search" action="/" method="get">
                    <label class="visually-hidden" for="card-search">Search cards by name or description</label>
                    <span class="icon search__icon" aria-hidden="true">search</span>
                    <input class="search__input" type="search" id="card-search" name="q" placeholder="Search cards by name or description...">
                </form>

                <p class="pool-metric">
                    <span class="icon" aria-hidden="true">inventory_2</span>
                    <span>Active cards in the current drop: <strong><?= count($cards) ?> cards</strong></span>
                </p>
            </div>

            <div class="filters" role="group" aria-label="Filter cards by rarity">
                <button class="pill" type="button" aria-pressed="true">
                    <span class="icon" aria-hidden="true">apps</span>
                    All (<?= count($cards) ?>)
                </button>
                <?php foreach ($rarities as $rarity): ?>
                    <button class="pill" type="button" aria-pressed="false">
                        <span class="dot dot--rarity rarity-<?= htmlspecialchars($rarity) ?>" aria-hidden="true"></span>
                        <?= htmlspecialchars(ucfirst($rarity)) ?> (<?= (int) ($rarityCounts[$rarity] ?? 0) ?>)
                    </button>
                <?php endforeach; ?>
            </div>

            <?php $this->partial('card-list', ['cards' => $cards]); ?>
        </section>
    </main>

<?php $this->partial('footer'); ?>