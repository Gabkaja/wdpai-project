    <main class="profile">
        <section class="player-card" aria-labelledby="player-title">
            <header class="player-card__header">
                <?php $this->partial('avatar', [
                    'username' => $username,
                    'avatarPath' => $avatarPath,
                    'avatarUrl' => $avatarUrl,
                    'size' => 'md',
                ]); ?>
                <div class="player-card__identity">
                    <h1 class="player-card__name" id="player-title"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h1>
                    <span class="player-card__role"><?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?></span>
                    <p class="player-card__joined">Joined <?= htmlspecialchars($joinedAt->format('F j, Y'), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </header>

            <dl class="player-card__stats">
                <div class="player-card__stat player-card__stat--rank">
                    <dt>Rank</dt>
                    <dd>#<?= (int) $rank ?></dd>
                </div>
                <div class="player-card__stat">
                    <dt>Total cards</dt>
                    <dd><?= (int) $totalCards ?></dd>
                </div>
                <div class="player-card__stat">
                    <dt>Unique cards</dt>
                    <dd><?= (int) $uniqueCards ?></dd>
                </div>
                <div class="player-card__stat">
                    <dt>Packs opened</dt>
                    <dd><?= (int) $packsOpened ?></dd>
                </div>
                <div class="player-card__stat player-card__stat--rarest">
                    <dt>Rarest card</dt>
                    <dd><?= $rarestCard !== null && $rarestCard !== '' ? htmlspecialchars($rarestCard, ENT_QUOTES, 'UTF-8') : '&mdash;' ?></dd>
                </div>
            </dl>

            <?php if ($isOwner): ?>
                <div class="player-card__actions">
                    <a class="button button--secondary" href="/settings/account">
                        <span class="icon" aria-hidden="true">settings</span>
                        Edit in settings
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <?php if ($canViewCollection): ?>
            <section class="profile-panel user-collection" aria-labelledby="user-collection-heading" data-user-collection data-user-id="<?= (int) $userId ?>">
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

                <div class="profile-panel__head">
                    <div>
                        <h2 class="profile-panel__title" id="user-collection-heading">
                            <span class="icon" aria-hidden="true">inventory</span>
                            Collection
                        </h2>
                        <?php if ($collectionNotice !== null): ?>
                            <p class="user-collection__notice"><?= htmlspecialchars($collectionNotice, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="collection__toolbar">
                    <label class="collection__label" for="user-collection-rarity">Rarity</label>
                    <select class="collection__select" id="user-collection-rarity" name="rarity">
                        <option value="">All rarities</option>
                        <?php foreach ($rarities as $rarity): ?>
                            <option value="<?= htmlspecialchars($rarity->getCode(), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($rarity->getName(), ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label class="collection__label" for="user-collection-sort">Sort</label>
                    <select class="collection__select" id="user-collection-sort" name="sort">
                        <option value="rarity">Rarity</option>
                        <option value="name">Name</option>
                        <option value="quantity">Quantity</option>
                        <option value="recent">Recently obtained</option>
                    </select>

                    <label class="collection__label" for="user-collection-search">Search</label>
                    <input class="collection__input" id="user-collection-search" name="q" type="search" placeholder="Search cards&hellip;">
                </div>

                <p class="collection__loading" hidden>Loading&hellip;</p>
                <p class="visually-hidden" data-user-collection-live role="status" aria-live="polite"></p>

                <ol class="collection__grid" role="list" data-collection-list></ol>

                <div class="collection__empty" hidden>
                    <p class="collection__empty-text">This collection is empty.</p>
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
                        <span class="collection__quantity">&times;1</span>
                    </li>
                </template>
            </section>

            <script type="module" src="/js/users/collection.js"></script>
        <?php else: ?>
            <section class="profile-panel user-collection" aria-labelledby="user-collection-heading">
                <div class="profile-panel__head">
                    <h2 class="profile-panel__title" id="user-collection-heading">
                        <span class="icon" aria-hidden="true">inventory</span>
                        Collection
                    </h2>
                </div>
                <p class="user-collection__private">This collection is private.</p>
            </section>
        <?php endif; ?>
    </main>
