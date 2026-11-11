    <main class="profile">
        <?php $flash = $flash ?? null; ?>
        <?php if ($flash !== null): ?>
            <div class="auth-alert<?= $flash['type'] === 'error' ? ' auth-alert--error' : '' ?>" role="alert">
                <span class="icon" aria-hidden="true"><?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?></span>
                <p class="auth-alert__text"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>
        <section class="profile-hero" aria-labelledby="profile-title">
            <div class="profile-hero__inner">
                <div class="profile-hero__identity">
                    <span class="profile-hero__avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($username, 0, 1))) ?></span>
                    <div>
                        <h1 class="profile-hero__name" id="profile-title">Profile of <?= htmlspecialchars($username) ?></h1>
                        <span class="badge badge--live profile-hero__badge">
                            <span class="icon" aria-hidden="true">verified</span>
                            Connected with Kick
                        </span>
                        <ul class="profile-hero__meta" role="list">
                            <li><span class="icon" aria-hidden="true">calendar_month</span> Collector since: <?= htmlspecialchars($memberSince) ?></li>
                            <li><span class="icon" aria-hidden="true">tag</span> ID: #<?= str_pad((string) (int) ($stats['userId'] ?? 0), 4, '0', STR_PAD_LEFT) ?>-TCG</li>
                            <li><span class="icon" aria-hidden="true">bolt</span> Season 3 Active</li>
                            <li><span class="icon" aria-hidden="true">auto_awesome</span> Booster x2</li>
                        </ul>
                    </div>
                </div>

                <div class="profile-hero__valuation">
                    <span class="profile-hero__valuation-label">Points balance</span>
                    <span class="profile-hero__valuation-value"><?= (int) $points ?></span>
                    <span class="profile-hero__valuation-delta">
                        <span class="icon" aria-hidden="true">trending_up</span>
                        StreamRip points
                    </span>
                </div>
            </div>
        </section>

        <?php
        $totalCards = $stats['totalCards'] ?? 0;
        $uniqueCards = $stats['uniqueCards'] ?? 0;
        $packsOpened = $stats['packsOpened'] ?? 0;
        $completion = $stats['completion'] ?? 0;
        $cardsPerPack = $packSettings['cards_per_pack'] ?? 4;
        $costPoints = $packSettings['cost_points'] ?? 100;
        $canAfford = (int) $points >= $costPoints;
        ?>

        <section class="profile-stats" aria-labelledby="stats-heading">
            <div class="profile-stats__head">
                <h2 class="profile-stats__title" id="stats-heading">
                    <span class="icon" aria-hidden="true">query_stats</span>
                    Player statistics
                </h2>
                <span class="profile-stats__eyebrow">Overview</span>
            </div>

            <div class="profile-stats__grid">
                <article class="stat-card stat-card--collection">
                    <div class="stat-card__head">
                        <span>Collection</span>
                        <span class="icon" aria-hidden="true">inventory_2</span>
                    </div>
                    <p class="stat-card__value">Cards collected: <strong><?= (int) $totalCards ?></strong></p>
                    <div class="progress">
                        <span class="progress__track">
                            <span class="progress__bar" style="width: <?= (int) $completion ?>%"></span>
                        </span>
                    </div>
                </article>

                <article class="stat-card stat-card--completion">
                    <div class="stat-card__head">
                        <span>Completion</span>
                        <span class="icon" aria-hidden="true">donut_large</span>
                    </div>
                    <p class="stat-card__value">Collection completion: <?= (int) $completion ?>%</p>
                    <div class="progress">
                        <span class="progress__track">
                            <span class="progress__bar" style="width: <?= (int) $completion ?>%"></span>
                        </span>
                    </div>
                </article>

                <article class="stat-card stat-card--standing">
                    <div class="stat-card__head">
                        <span>Packs opened</span>
                        <span class="icon" aria-hidden="true">card_today</span>
                    </div>
                    <p class="stat-card__value"><?= (int) $packsOpened ?></p>
                    <p class="stat-card__note">Total pack openings</p>
                </article>

                <article class="stat-card stat-card--signature">
                    <div class="stat-card__head">
                        <span>Signature</span>
                        <span class="icon" aria-hidden="true">favorite</span>
                    </div>
                    <p class="stat-card__value">Favorite card: <?= htmlspecialchars($stats['favoriteCard'] ?? '—') ?></p>
                    <p class="stat-card__note">
                        <span class="icon" aria-hidden="true">military_tech</span>
                        Legendary mint 10
                    </p>
                </article>
            </div>
        </section>

        <div class="profile-layout">
            <div class="profile-sidebar">
                <section class="profile-panel" aria-labelledby="open-pack-heading">
                    <div class="profile-panel__head">
                        <h2 class="profile-panel__title" id="open-pack-heading">
                            <span class="icon" aria-hidden="true">card_today</span>
                            Open pack
                        </h2>
                    </div>

                    <div class="profile-panel__text">
                        <span class="pack-cost">
                            <span class="icon" aria-hidden="true">attach_money</span>
                            Cost: <strong><?= (int) $costPoints ?></span> points
                        </span>
                        <span class="pack-balance">
                            <span class="icon" aria-hidden="true">account_balance_wallet</span>
                            Your balance: <strong><?= (int) $points ?></span> points
                        </span>
                    </div>

                    <form class="profile-panel__form" method="post" action="/packs/open">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <button class="button button--primary" type="submit"<?= !$canAfford ? ' disabled aria-disabled="true"' : '' ?>>
                            <span class="icon" aria-hidden="true">bolt</span>
                            Open pack (<?= (int) $cardsPerPack ?> cards)
                        </button>
                        <?php if (!$canAfford): ?>
                            <p class="profile-panel__text profile-panel__status" role="status" aria-live="polite">
                                Not enough points to open a pack.
                            </p>
                        <?php endif; ?>
                    </form>
                </section>

                <section class="profile-panel" aria-labelledby="avatar-heading">
                    <div class="profile-panel__head">
                        <h2 class="profile-panel__title" id="avatar-heading">
                            <span class="icon" aria-hidden="true">account_box</span>
                            Avatar
                        </h2>
                    </div>

                    <div class="avatar-drop">
                        <span class="avatar-drop__icon">
                            <span class="icon" aria-hidden="true">person</span>
                        </span>
                        <p class="profile-panel__text">No avatar uploaded yet.<br>Supported format: PNG, JPG</p>
                    </div>

                    <form class="profile-panel__form" method="post" action="/profile/avatar" enctype="multipart/form-data">
                        <label for="avatar-file">Choose a profile picture</label>
                        <input type="file" id="avatar-file" name="avatar" accept="image/*" required>
                        <button class="button button--primary" type="submit">
                            <span class="icon" aria-hidden="true">upload</span>
                            Upload avatar
                        </button>
                    </form>
                </section>

                <section class="profile-panel" aria-labelledby="redeem-heading">
                    <div class="profile-panel__head">
                        <h2 class="profile-panel__title" id="redeem-heading">
                            <span class="icon" aria-hidden="true">redeem</span>
                            Redeem a promo code
                        </h2>
                    </div>

                    <form class="profile-panel__form" method="post" action="/profile/redeem">
                        <label for="promo-code">Promo code</label>
                        <input type="text" id="promo-code" name="code" placeholder="Enter promo code..." required autocomplete="off">
                        <button class="button button--primary" type="submit">
                            <span class="icon" aria-hidden="true">redeem</span>
                            Redeem
                        </button>
                    </form>

                    <p class="profile-panel__text profile-panel__status" role="status" aria-live="polite">
                        Last redeemed: <strong>STREAM-HYPER-24</strong>
                        <span class="profile-panel__success">Success (+1 pack)</span>
                    </p>
                </section>

                <section class="profile-panel" aria-labelledby="security-heading">
                    <div class="profile-panel__head">
                        <h2 class="profile-panel__title" id="security-heading">
                            <span class="icon" aria-hidden="true">shield</span>
                            Status &amp; security
                        </h2>
                    </div>

                    <ul class="status-list" role="list">
                        <li>
                            <span class="icon" aria-hidden="true">verified_user</span>
                            <span class="status-list__label">2FA Verification</span>
                            <span class="status-list__badge">Active</span>
                        </li>
                        <li>
                            <span class="icon" aria-hidden="true">workspace_premium</span>
                            <span class="status-list__label">VIP Stream Supporter</span>
                            <span class="status-list__badge">Tier 3</span>
                        </li>
                        <li>
                            <span class="icon" aria-hidden="true">swap_horiz</span>
                            <span class="status-list__label">P2P Trading</span>
                            <span class="status-list__badge">Unlocked</span>
                        </li>
                    </ul>
                </section>

                <?php if ($kickAccount !== null): ?>
                    <section class="profile-panel" aria-labelledby="kick-heading">
                        <div class="profile-panel__head">
                            <h2 class="profile-panel__title" id="kick-heading">
                                <span class="icon" aria-hidden="true">verified</span>
                                Kick account
                            </h2>
                        </div>
                        <div class="profile-panel__text">
                            Connected with Kick as <strong><?= htmlspecialchars($kickAccount['kick_username']) ?></strong>.
                        </div>
                        <form class="profile-panel__form" method="post" action="/profile/kick/unlink">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                            <button class="button button--primary" type="submit">
                                <span class="icon" aria-hidden="true">unlink</span>
                                Disconnect
                            </button>
                        </form>
                    </section>
                <?php else: ?>
                    <section class="profile-panel" aria-labelledby="kick-heading">
                        <div class="profile-panel__head">
                            <h2 class="profile-panel__title" id="kick-heading">
                                <span class="icon" aria-hidden="true">verified</span>
                                Kick account
                            </h2>
                        </div>
                        <div class="profile-panel__text">
                            Not connected. Link your Kick account to log in with Kick.
                        </div>
                        <a class="button button--primary" href="/profile/kick/connect">
                            <span class="icon" aria-hidden="true">link</span>
                            Connect Kick
                        </a>
                    </section>
                <?php endif; ?>
            </div>

            <section class="profile-panel profile-collection" aria-labelledby="collection-heading">
                <div class="profile-panel__head">
                    <div>
                        <h2 class="profile-panel__title" id="collection-heading">
                            <span class="icon" aria-hidden="true">style</span>
                            My collection
                        </h2>
                        <p class="profile-panel__text">Browse, inspect and verify cards unlocked from live broadcast drops.</p>
                    </div>

                    <div class="collection-sort-label">
                        <label for="collection-sort">Sort by</label>
                        <select class="collection-sort" id="collection-sort" name="sort">
                            <option value="rarity">Rarity: Highest first</option>
                            <option value="name">Name: A&ndash;Z</option>
                            <option value="newest">Newest first</option>
                        </select>
                    </div>
                </div>

                <div class="collection-filters" role="group" aria-label="Collection filters">
                    <button class="pill" type="button" aria-pressed="true">All <?= $uniqueCards ?></button>
                    <button class="pill" type="button" aria-pressed="false">Owned <?= $uniqueCards ?></button>
                </div>

                <ul class="collection-grid" role="list">
                    <?php foreach ($collection as $item): ?>
                        <?php $card = $item->getCard(); ?>
                        <li>
                            <article class="collection-card rarity-<?= htmlspecialchars($card->getRarityCode()) ?>">
                                <div class="collection-card__media">
                                    <span class="collection-card__code">#<?= htmlspecialchars($card->getCode()) ?></span>
                                    <span class="collection-card__owned"><?= (int) $item->getQuantity() ?>x owned</span>
                                    <span class="collection-card__icon">
                                        <span class="icon" aria-hidden="true"><?= htmlspecialchars($card->getIcon()) ?></span>
                                    </span>
                                </div>
                                <div class="collection-card__body">
                                    <span class="collection-card__rarity"><?= htmlspecialchars(ucfirst($card->getRarityCode())) ?></span>
                                    <h3 class="collection-card__name">
                                        <a href="/cards/<?= (int) $card->getId() ?>"><?= htmlspecialchars($card->getName()) ?></a>
                                    </h3>
                                    <div class="collection-card__footer">
                                        <span>Rarity: <?= htmlspecialchars(ucfirst($card->getRarityCode())) ?></span>
                                        <span>Acquired: <?= $item->getAcquiredAt()->format('Y-m-d') ?></span>
                                    </div>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($uniqueCards === 0): ?>
                    <p class="collection-grid__empty">No cards yet. Open a pack to start your collection.</p>
                <?php endif; ?>

                <div class="collection-footer">
                    <p>Showing <?= $uniqueCards ?> unique card<?= $uniqueCards !== 1 ? 's' : '' ?> (<?= (int) $totalCards ?> total)</p>
                </div>
            </section>

            <?php if ($recentOpenings): ?>
                <section class="profile-panel profile-recent" aria-labelledby="recent-heading">
                    <div class="profile-panel__head">
                        <h2 class="profile-panel__title" id="recent-heading">
                            <span class="icon" aria-hidden="true">history</span>
                            Recent pack openings
                        </h2>
                    </div>

                    <ul class="recent-list" role="list">
                        <?php foreach ($recentOpenings as $opening): ?>
                            <li class="recent-list__item">
                                <span class="recent-list__date"><?= htmlspecialchars($opening->getOpenedAt()->format('Y-m-d H:i')) ?></span>
                                <span class="recent-list__cost"><?= (int) $opening->getPointsSpent() ?> pts</span>
                                <a class="recent-list__link" href="/packs/<?= (int) $opening->getId() ?>">
                                    <span class="icon" aria-hidden="true">card_today</span>
                                    View opening #<?= (int) $opening->getId() ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </div>
    </main>
