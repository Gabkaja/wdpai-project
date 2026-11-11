    <main class="pack-page">
        <?php $flash = $flash ?? null; ?>
        <?php if ($flash !== null): ?>
            <div class="auth-alert<?= $flash['type'] === 'error' ? ' auth-alert--error' : '' ?>" role="alert">
                <span class="icon" aria-hidden="true"><?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?></span>
                <p class="auth-alert__text"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endif; ?>

        <section class="pack-header" aria-labelledby="pack-title">
            <div class="pack-header__inner container">
                <a href="/profile" class="button button--primary pack-header__back">
                    <span class="icon" aria-hidden="true">arrow_back</span>
                    Back to profile
                </a>
                <h1 class="pack-header__title" id="pack-title">Pack opened</h1>
                <div class="pack-header__meta">
                    <span class="pack-header__meta-item">
                        <span class="icon" aria-hidden="true">attach_money</span>
                        <?= (int) $pointsSpent ?> points spent
                    </span>
                    <span class="pack-header__meta-item">
                        <span class="icon" aria-hidden="true">schedule</span>
                        <?= htmlspecialchars($openedAt) ?>
                    </span>
                </div>
            </div>
        </section>

        <section class="pack-cards" aria-labelledby="cards-heading">
            <div class="container">
                <h2 class="pack-cards__title" id="cards-heading">
                    <span class="icon" aria-hidden="true">card_today</span>
                    Cards (<?= count($cards) ?>)
                </h2>
                <?php $this->partial('card-list', ['cards' => $cards]); ?>
            </div>
        </section>
    </main>
