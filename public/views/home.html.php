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

        <section aria-labelledby="catalog-heading">
            <h2 id="catalog-heading" class="visually-hidden">Browse all cards</h2>
            <?php $this->partial('card-list', ['cards' => $cards]); ?>
        </section>
    </main>

<?php $this->partial('footer'); ?>