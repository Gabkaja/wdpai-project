    <main class="profile">
        <?php $flash = $flash ?? null; ?>
        <?php $avatarPath = $avatarPath ?? null; ?>
        <?php $avatarUrl = $avatarUrl ?? null; ?>
        <?php if ($flash !== null): ?>
            <div class="auth-alert<?= $flash['type'] === 'error' ? ' auth-alert--error' : '' ?>" role="alert">
                <span class="icon" aria-hidden="true"><?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?></span>
                <p class="auth-alert__text"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <section class="player-card" aria-labelledby="player-title">
            <header class="player-card__header">
                <?php $this->partial('avatar', [
                    'username' => $username,
                    'avatarPath' => $avatarPath,
                    'avatarUrl' => $avatarUrl,
                    'size' => 'md',
                ]); ?>
                <div class="player-card__identity">
                    <h1 class="player-card__name" id="player-title"><?= htmlspecialchars($username) ?></h1>
                    <span class="player-card__role"><?= htmlspecialchars(ucfirst($role)) ?></span>
                    <p class="player-card__joined">Joined <?= htmlspecialchars($joinedAt->format('F j, Y')) ?></p>
                </div>
            </header>

            <dl class="player-card__stats">
                <div class="player-card__stat player-card__stat--rank">
                    <dt>Rank</dt>
                    <dd>#<?= (int) $rank ?></dd>
                </div>
                <div class="player-card__stat">
                    <dt>Points</dt>
                    <dd><?= (int) $points ?></dd>
                </div>
                <div class="player-card__stat">
                    <dt>Unopened packs</dt>
                    <dd><?= (int) $unopenedPacks ?></dd>
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
                    <dd><?= $rarestCard !== null && $rarestCard !== '' ? htmlspecialchars($rarestCard) : '&mdash;' ?></dd>
                </div>
            </dl>

            <div class="player-card__actions">
                <a class="button button--primary" href="/packs">
                    <span class="icon" aria-hidden="true">playing_cards</span>
                    Open packs
                </a>
                <a class="button button--secondary" href="/settings/account">
                    <span class="icon" aria-hidden="true">settings</span>
                    Settings
                </a>
            </div>
        </section>

        <div class="profile-layout">
            <div class="profile-sidebar">
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

        </div>
    </main>
