<main class="packs" data-packs-root
    data-cost="<?= (int) $packSettings['cost_points'] ?>"
    data-max-bulk="<?= (int) $packSettings['max_bulk_packs'] ?>"
    data-max-purchase="<?= (int) $packSettings['max_purchase_packs'] ?>"
    data-cards-per-pack="<?= (int) $packSettings['cards_per_pack'] ?>"
    data-points="<?= (int) $points ?>"
    data-stock="<?= (int) $unopenedPacks ?>"
    data-reveal-mode="<?= htmlspecialchars($userSettings->getRevealMode(), ENT_QUOTES, 'UTF-8') ?>"
    data-multi-open-mode="<?= htmlspecialchars($userSettings->getMultiOpenMode(), ENT_QUOTES, 'UTF-8') ?>"
    data-sound-enabled="<?= $userSettings->isSoundEnabled() ? 'true' : 'false' ?>"
    data-sound-volume="<?= (int) $userSettings->getSoundVolume() ?>">

    <?php
    $rarityJson = [];

    foreach ($rarities as $rarity) {
        $rarityJson[] = [
            'code' => $rarity->getCode(),
            'name' => $rarity->getName(),
            'level' => $rarity->getLevel(),
        ];
    }
    ?>
    <script type="application/json" data-rarities><?= json_encode($rarityJson, JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR) ?></script>

    <?php $flash = $flash ?? null; ?>
    <?php if ($flash !== null): ?>
        <div class="auth-alert<?= $flash['type'] === 'error' ? ' auth-alert--error' : '' ?>" role="alert">
            <span class="icon" aria-hidden="true"><?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?></span>
            <p class="auth-alert__text"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    <?php endif; ?>

    <section class="packs__hero" aria-labelledby="packs-title">
        <div class="container">
            <h1 class="packs__title" id="packs-title">Packs</h1>
            <p class="packs__eyebrow">Collector foil pack &mdash; buy, open, reveal.</p>

            <div class="packs__stat-group">
                <div class="packs__stat">
                    <span class="packs__stat-label">Points</span>
                    <span class="packs__stat-value" data-stat="points"><?= (int) $points ?></span>
                </div>
                <div class="packs__stat">
                    <span class="packs__stat-label">Unopened packs</span>
                    <span class="packs__stat-value" data-stat="stock"><?= (int) $unopenedPacks ?></span>
                </div>
            </div>

            <div class="packs__pack-wrap">
                <div class="packs__pack" aria-label="Foil pack">
                    <div class="packs__pack-flap"></div>
                    <div class="packs__pack-body"></div>
                    <div class="packs__pack-seal"></div>
                    <div class="packs__pack-shine" aria-hidden="true"></div>
                </div>
                <p class="packs__pack-note"><?= (int) $packSettings['cards_per_pack'] ?> cards per pack</p>
            </div>
        </div>
    </section>    <div class="packs__grid">
        <div class="packs__main">
            <section id="buy" class="packs__buy" aria-labelledby="buy-heading">
            <div class="container">
                <div class="packs__panel-header">
                    <h2 class="packs__heading" id="buy-heading">
                        <span class="icon" aria-hidden="true">add_shopping_cart</span>
                        Buy packs
                    </h2>
                    <span class="packs__panel-note"><?= (int) $packSettings['cost_points'] ?> points per pack</span>
                </div>

                <form class="packs__form" data-form="buy" method="post" action="/api/packs/buy">
                    <div class="packs__field packs__field--slider">
                        <div class="packs__field-head">
                            <label class="packs__label" for="packs-buy-quantity">Quantity</label>
                            <output class="packs__slider-value" for="packs-buy-quantity" data-output="buy-quantity">1</output>
                        </div>
                        <div class="packs__slider">
                            <input class="packs__range-input" id="packs-buy-quantity" name="quantity" type="range" min="1" max="<?= (int) $packSettings['max_purchase_packs'] ?>" step="1" value="1" data-slider="buy-quantity">
                            <div class="packs__slider-ticks" data-ticks="buy-quantity" aria-hidden="true"></div>
                        </div>
                        <span class="packs__unit">packs</span>
                    </div>
                    <div class="packs__field packs__field--total">
                        <label class="packs__label" for="packs-buy-total">Total cost</label>
                        <output class="packs__output" id="packs-buy-total" data-total-cost><?= (int) $packSettings['cost_points'] ?></output>
                        <span class="packs__unit">points</span>
                    </div>
                    <button class="button button--primary packs__submit" type="submit">Buy packs</button>
                    <p class="packs__message" data-message="buy" role="status"></p>
                </form>
            </div>
        </section>

        <section class="packs__open" aria-labelledby="open-heading">
            <div class="container">
                <div class="packs__panel-header">
                    <h2 class="packs__heading" id="open-heading">
                        <span class="icon" aria-hidden="true">playing_cards</span>
                        Open packs
                    </h2>
                    <span class="packs__panel-note"><?= (int) $unopenedPacks ?> in stock</span>
                </div>

                <form class="packs__form" data-form="open" method="post" action="/api/packs/open">
                    <div class="packs__field packs__field--slider">
                        <div class="packs__field-head">
                            <label class="packs__label" for="packs-open-count">Count</label>
                            <output class="packs__slider-value" for="packs-open-count" data-output="open-count">1</output>
                        </div>
                        <div class="packs__slider">
                            <input class="packs__range-input" id="packs-open-count" name="count" type="range" min="1" max="<?= (int) max(1, min($unopenedPacks, $packSettings['max_bulk_packs'])) ?>" step="1" value="1" data-slider="open-count"<?= $unopenedPacks <= 0 ? ' disabled' : '' ?>>
                            <div class="packs__slider-ticks" data-ticks="open-count" aria-hidden="true"></div>
                        </div>
                        <span class="packs__unit">packs</span>
                    </div>
                    <button class="button button--primary packs__submit" type="submit"<?= $unopenedPacks <= 0 ? ' disabled' : '' ?>>Open</button>
                    <button class="button button--secondary packs__open-all" type="button" data-action="open-all"<?= $unopenedPacks <= 0 ? ' disabled' : '' ?>>Open all</button>
                    <p class="packs__message" data-message="open" role="status"></p>
                    <?php if ($unopenedPacks <= 0): ?>
                        <p class="packs__hint">No unopened packs in stock. <a href="#buy">Buy packs first</a>.</p>
                    <?php endif; ?>
                </form>
            </section>

            <section class="collection" aria-labelledby="collection-heading">
                <div class="container">
                    <div class="collection__header">
                        <h2 class="collection__heading" id="collection-heading">
                            <span class="icon" aria-hidden="true">inventory</span>
                            Your collection
                        </h2>
                        <p class="collection__summary">
                            <span class="collection__summary-value" data-summary="unique">–</span> /
                            <span class="collection__summary-value" data-summary="total">–</span> unique cards,
                            <span class="collection__summary-value" data-summary="quantity">–</span> total
                        </p>
                    </div>

                    <div class="collection__toolbar">
                        <label class="collection__label" for="collection-rarity">Rarity</label>
                        <select class="collection__select" id="collection-rarity" name="rarity">
                            <option value="">All rarities</option>
                            <?php foreach ($rarities as $rarity): ?>
                                <option value="<?= htmlspecialchars($rarity->getCode()) ?>"><?= htmlspecialchars($rarity->getName()) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label class="collection__label" for="collection-sort">Sort</label>
                        <select class="collection__select" id="collection-sort" name="sort">
                            <option value="rarity">Rarity</option>
                            <option value="name">Name</option>
                            <option value="quantity">Quantity</option>
                            <option value="recent">Recently obtained</option>
                        </select>

                        <label class="collection__label" for="collection-search">Search</label>
                        <input class="collection__input" id="collection-search" name="q" type="search" placeholder="Search cards…">
                    </div>

                    <p class="collection__loading" hidden>Loading…</p>

                    <ol class="collection__grid" role="list" data-collection-list></ol>

                    <div class="collection__empty" hidden>
                        <p class="collection__empty-text" id="collection-empty-text">You don't have any cards yet.</p>
                    </div>

                    <button class="button button--secondary collection__load-more" type="button" data-action="load-more" hidden>Load more</button>

                    <div class="collection__error" hidden role="alert">
                        <p class="collection__error-message"></p>
                        <button class="button button--primary collection__retry" type="button">Retry</button>
                    </div>

                    <template data-template="collection-item">
                        <li class="collection__card">
                            <span class="collection__chip rarity-chip" aria-hidden="true"></span>
                            <span class="collection__name"></span>
                            <span class="collection__quantity">×1</span>
                        </li>
                    </template>
                </div>
            </section>
        </div>
        <aside class="packs__sidebar">
            <section class="packs__settings" aria-labelledby="settings-heading">
        <div class="container">
            <div class="packs__panel-header">
                <h2 class="packs__heading" id="settings-heading">
                    <span class="icon" aria-hidden="true">settings</span>
                    Settings
                </h2>
                <span class="packs__panel-note">Remembered for your account</span>
            </div>

            <form class="packs__form" data-form="settings" method="post" action="/api/settings/packs">
                <div class="packs__field packs__field--radios">
                    <span class="packs__label">Reveal mode</span>
                    <p class="packs__field-description">How cards are revealed after opening.</p>
                    <div class="packs__radios" role="radiogroup" aria-label="Reveal mode">
                        <label class="packs__radio">
                            <input type="radio" name="reveal_mode" value="auto"<?= $userSettings->getRevealMode() === 'auto' ? ' checked' : '' ?>>
                            <span class="packs__radio-title">Auto</span>
                            <span class="packs__radio-desc">Cards reveal one by one</span>
                        </label>
                        <label class="packs__radio">
                            <input type="radio" name="reveal_mode" value="manual"<?= $userSettings->getRevealMode() === 'manual' ? ' checked' : '' ?>>
                            <span class="packs__radio-title">Manual</span>
                            <span class="packs__radio-desc">You click each card, in any order</span>
                        </label>
                    </div>
                </div>

                <div class="packs__field packs__field--radios">
                    <span class="packs__label">Multi-open mode</span>
                    <p class="packs__field-description">What happens when you open several packs.</p>
                    <div class="packs__radios" role="radiogroup" aria-label="Multi-open mode">
                        <label class="packs__radio">
                            <input type="radio" name="multi_open_mode" value="queue"<?= $userSettings->getMultiOpenMode() === 'queue' ? ' checked' : '' ?>>
                            <span class="packs__radio-title">Queue</span>
                            <span class="packs__radio-desc">Openings reveal one after another</span>
                        </label>
                        <label class="packs__radio">
                            <input type="radio" name="multi_open_mode" value="all_at_once"<?= $userSettings->getMultiOpenMode() === 'all_at_once' ? ' checked' : '' ?>>
                            <span class="packs__radio-title">All at once</span>
                            <span class="packs__radio-desc">All cards reveal at once</span>
                        </label>
                    </div>
                </div>

                <div class="packs__field">
                    <label class="packs__label" for="packs-settings-sound">Sound</label>
                    <p class="packs__field-description">Play sounds while revealing cards.</p>
                    <input class="packs__switch" id="packs-settings-sound" name="sound_enabled" type="checkbox"<?= $userSettings->isSoundEnabled() ? ' checked' : '' ?>>
                </div>

                <div class="packs__field">
                    <label class="packs__label" for="packs-settings-volume">Volume</label>
                    <p class="packs__field-description">Reveal sound volume.</p>
                    <div class="packs__range">
                        <input class="packs__range-input" id="packs-settings-volume" name="sound_volume" type="range" min="0" max="100" value="<?= (int) $userSettings->getSoundVolume() ?>">
                        <output class="packs__range-output" for="packs-settings-volume"><?= (int) $userSettings->getSoundVolume() ?></output>
                    </div>
                </div>

                <button class="button button--primary packs__submit" type="submit">Save settings</button>
                <p class="packs__message" data-status="settings" role="status"></p>
            </form>
        </div>
    </section>

    <section class="packs__odds" aria-labelledby="odds-heading">
        <div class="container">
            <div class="packs__panel-header">
                <h2 class="packs__heading" id="odds-heading">
                    <span class="icon" aria-hidden="true">auto_awesome</span>
                    Drop chances
                </h2>
                <span class="packs__panel-note">Per pack card, rounded to one decimal</span>
            </div>

            <div class="packs__table-wrap">
                <table class="packs__table">
                    <caption class="visually-hidden">Drop chances by rarity</caption>
                    <thead>
                        <tr>
                            <th scope="col">Rarity</th>
                            <th scope="col">Cards</th>
                            <th scope="col">Chance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rarities as $rarity): ?>
                            <tr>
                                <td class="packs__table-cell--rarity">
                                    <span class="rarity-chip rarity-chip--level-<?= (int) $rarity->getLevel() ?>" aria-hidden="true"><?= htmlspecialchars(strtoupper($rarity->getCode())) ?></span>
                                    <span class="packs__table-rarity"><?= htmlspecialchars($rarity->getName()) ?></span>
                                </td>
                                <td class="packs__table-cell--cards"><?= (int) $rarity->getCardCount() ?></td>
                                <td class="packs__table-cell--chance">
                                    <?php if ($rarity->getPercent() > 0.0): ?>
                                        <?= number_format($rarity->getPercent(), 1) ?>%
                                    <?php else: ?>
                                        <span class="packs__chance-none">not in packs</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="packs__history" aria-labelledby="history-heading">
        <div class="container">
            <div class="packs__panel-header">
                <h2 class="packs__heading" id="history-heading">
                    <span class="icon" aria-hidden="true">history</span>
                    History
                </h2>
                <span class="packs__panel-note">Your latest pack openings</span>
            </div>

            <p class="packs__message" data-message="history" role="status"></p>

            <ol class="packs__history-list" data-history-list role="list">
                <?php foreach ($history as $opening): ?>
                    <?php $openedAt = $opening->getOpenedAt(); ?>
                    <li class="pack-history__item" data-opening-id="<?= (int) $opening->getId() ?>">
                        <a class="pack-history__link" href="/packs/<?= (int) $opening->getId() ?>">
                            <time class="pack-history__time" datetime="<?= htmlspecialchars($openedAt->format(DATE_ATOM), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($openedAt->format('Y-m-d H:i')) ?></time>
                            <span class="pack-history__title">Opening #<?= (int) $opening->getId() ?></span>
                        </a>
                        <ul class="pack-history__cards" role="list">
                            <?php foreach ($opening->getCards() as $openingCard): ?>
                                <?php $card = $openingCard->getCard(); ?>
                                <li class="pack-history__card">
                                    <span class="rarity-chip rarity-chip--level-<?= (int) ($rarityLevels[$card->getRarityCode()] ?? 1) ?>" aria-hidden="true"><?= htmlspecialchars(strtoupper($card->getRarityCode())) ?></span>
                                    <span class="pack-history__card-name"><?= htmlspecialchars($card->getName()) ?></span>
                                    <?php if ($openingCard->isNew()): ?>
                                        <span class="pack-history__new" aria-label="New card">NEW</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ol>

            <button class="button button--secondary packs__load-more" type="button" data-action="load-more" data-next-offset="<?= (int) $historyNextOffset ?>"<?= !$historyHasMore ? ' hidden' : '' ?>>Load more</button>

            <template data-template="history-item">
                <li class="pack-history__item">
                    <a class="pack-history__link" href="">
                        <time class="pack-history__time"></time>
                        <span class="pack-history__title"></span>
                    </a>
                    <ul class="pack-history__cards" role="list"></ul>
                </li>
            </template>
        </div>
    </section>
        </aside>
    </div>

    <noscript class="packs__noscript">
        <p>This page requires JavaScript. Enable JavaScript to buy and open packs.</p>
    </noscript>

    <section class="packs__stage" data-stage hidden>
        <div data-live class="visually-hidden" aria-live="polite"></div>
        <div class="packs__stage-backdrop" aria-hidden="true"></div>
        <div class="packs__stage-inner">
            <button class="packs__stage-close" type="button" aria-label="Close opening"></button>
            <div class="packs__reveal"></div>
        </div>
    </section>
</main>
<script type="module" src="/js/packs/index.js"></script>
