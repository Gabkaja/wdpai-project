    <main class="profile">
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
                            <li><span class="icon" aria-hidden="true">calendar_month</span> Collector since: September 2024</li>
                            <li><span class="icon" aria-hidden="true">tag</span> ID: #9482-TCG</li>
                            <li><span class="icon" aria-hidden="true">bolt</span> Season 3 Active</li>
                            <li><span class="icon" aria-hidden="true">auto_awesome</span> Booster x2</li>
                        </ul>
                    </div>
                </div>

                <div class="profile-hero__valuation">
                    <span class="profile-hero__valuation-label">Inventory valuation</span>
                    <span class="profile-hero__valuation-value">4,820 <span>PLN</span></span>
                    <span class="profile-hero__valuation-delta">
                        <span class="icon" aria-hidden="true">trending_up</span>
                        +340 PLN this week
                    </span>
                </div>
            </div>
        </section>

        <?php
        $ownedCount = count($ownedIds);
        $lockedCount = count($cards) - $ownedCount;
        $mintGrades = ['common' => '8.8', 'rare' => '8.9', 'epic' => '9.5', 'legendary' => '10'];
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
                    <p class="stat-card__value">Cards collected: <strong><?= (int) $stats['totalCards'] ?></strong></p>
                    <div class="progress">
                        <span class="progress__track">
                            <span class="progress__bar" style="width: <?= (int) $stats['completion'] ?>%"></span>
                        </span>
                    </div>
                </article>

                <article class="stat-card stat-card--completion">
                    <div class="stat-card__head">
                        <span>Completion</span>
                        <span class="icon" aria-hidden="true">donut_large</span>
                    </div>
                    <p class="stat-card__value">Collection completion: <?= (int) $stats['completion'] ?>%</p>
                    <div class="progress">
                        <span class="progress__track">
                            <span class="progress__bar" style="width: <?= (int) $stats['completion'] ?>%"></span>
                        </span>
                    </div>
                </article>

                <article class="stat-card stat-card--standing">
                    <div class="stat-card__head">
                        <span>Standing</span>
                        <span class="icon" aria-hidden="true">leaderboard</span>
                    </div>
                    <p class="stat-card__value">Community rank: #<?= (int) $stats['rank'] ?></p>
                    <p class="stat-card__note">Top 2% of players</p>
                </article>

                <article class="stat-card stat-card--signature">
                    <div class="stat-card__head">
                        <span>Signature</span>
                        <span class="icon" aria-hidden="true">favorite</span>
                    </div>
                    <p class="stat-card__value">Favorite card: <?= htmlspecialchars($stats['favoriteCard']) ?></p>
                    <p class="stat-card__note">
                        <span class="icon" aria-hidden="true">military_tech</span>
                        Legendary mint 10
                    </p>
                </article>
            </div>
        </section>

        <div class="profile-layout">
            <div class="profile-sidebar">
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
                    <button class="pill" type="button" aria-pressed="true">All <?= count($cards) ?></button>
                    <button class="pill" type="button" aria-pressed="false">Owned <?= $ownedCount ?></button>
                    <button class="pill" type="button" aria-pressed="false">Locked <?= $lockedCount ?></button>
                    <button class="pill rarity-legendary" type="button" aria-pressed="false"><span class="dot" aria-hidden="true"></span> Legendary</button>
                    <button class="pill rarity-epic" type="button" aria-pressed="false"><span class="dot" aria-hidden="true"></span> Epic</button>
                    <button class="pill rarity-rare" type="button" aria-pressed="false"><span class="dot" aria-hidden="true"></span> Rare</button>
                </div>

                <ul class="collection-grid" role="list">
                    <?php foreach ($cards as $card): ?>
                        <?php $isOwned = in_array($card->getId(), $ownedIds, true); ?>
                        <li>
                            <?php if ($isOwned): ?>
                                    <article class="collection-card rarity-<?= htmlspecialchars($card->getRarityCode()) ?>">
                                    <div class="collection-card__media">
                                        <span class="collection-card__code">#<?= htmlspecialchars($card->getCode()) ?></span>
                                        <span class="collection-card__owned">1x owned</span>
                                        <span class="collection-card__icon">
                                            <span class="icon" aria-hidden="true"><?= htmlspecialchars($card->getIcon()) ?></span>
                                        </span>
                                        <span class="collection-card__mint">Mint <?= htmlspecialchars($mintGrades[$card->getRarityCode()] ?? '—') ?></span>
                                    </div>
                                    <div class="collection-card__body">
                                        <span class="collection-card__rarity"><?= htmlspecialchars(ucfirst($card->getRarityCode())) ?></span>
                                            <h3 class="collection-card__name">
                                                <a href="/cards/<?= (int) $card->getId() ?>"><?= htmlspecialchars($card->getName()) ?></a>
                                        </h3>
                                        <div class="collection-card__footer">
                                            <span>Rarity: <?= htmlspecialchars(ucfirst($card->getRarityCode())) ?></span>
                                            <a class="collection-card__view" href="/cards/<?= (int) $card->getId() ?>">
                                                <span class="icon" aria-hidden="true">visibility</span>
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            <?php else: ?>
                                <article class="collection-card collection-card--locked" aria-label="Locked card">
                                    <div class="collection-card__media">
                                        <span class="collection-card__code">Locked (#<?= htmlspecialchars($card->getCode()) ?>)</span>
                                        <span class="icon" aria-hidden="true">lock</span>
                                    </div>
                                    <div class="collection-card__body">
                                        <span class="collection-card__rarity">Tier ???</span>
                                        <h3 class="collection-card__name">???</h3>
                                        <p class="collection-card__locked">Not collected yet.</p>
                                    </div>
                                </article>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="collection-footer">
                    <p>Showing <?= count($cards) ?> of <?= count($cards) ?> cards in collection</p>
                    <nav class="pagination" aria-label="Collection pagination">
                        <span class="pagination__link pagination__link--arrow pagination__link--disabled" aria-disabled="true">
                            <span class="icon" aria-hidden="true">chevron_left</span>
                        </span>
                        <span class="pagination__link" aria-current="page">1/1</span>
                        <span class="pagination__link pagination__link--arrow pagination__link--disabled" aria-disabled="true">
                            <span class="icon" aria-hidden="true">chevron_right</span>
                        </span>
                    </nav>
                </div>
            </section>
        </div>
    </main>
