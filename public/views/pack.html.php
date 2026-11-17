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
                <a href="/packs" class="button button--primary pack-header__back">
                    <span class="icon" aria-hidden="true">arrow_back</span>
                    Back to packs
                </a>
                <h1 class="pack-header__title" id="pack-title">Pack opened</h1>
                <div class="pack-header__meta">
                    <span class="pack-header__meta-item">
                        <span class="icon" aria-hidden="true">schedule</span>
                        <time datetime="<?= htmlspecialchars($opening->getOpenedAt()->format(DATE_ATOM), ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($opening->getOpenedAt()->format('Y-m-d H:i'), ENT_QUOTES, 'UTF-8') ?>
                        </time>
                    </span>
                </div>
            </div>
        </section>

        <section class="pack-cards" aria-labelledby="cards-heading">
            <div class="container">
                <h2 class="pack-cards__title" id="cards-heading">
                    <span class="icon" aria-hidden="true">playing_cards</span>
                    Cards (<?= count($openingCards) ?>)
                </h2>
                <ul class="pack-result__cards" role="list">
                    <?php foreach ($openingCards as $openingCard): ?>
                        <?php
                        $card = $openingCard->getCard();
                        $level = (int) ($rarityLevels[$card->getRarityCode()] ?? 1);
                        ?>
                        <li class="pack-result__item">
                            <article class="pack-card pack-card--static pack-card--revealed pack-card--level-<?= $level ?>">
                                <div class="pack-card__inner">
                                    <div class="pack-card__face pack-card__face--back" aria-hidden="true"></div>
                                    <div class="pack-card__face pack-card__face--front">
                                        <span class="pack-card__icon icon" aria-hidden="true"><?= htmlspecialchars($card->getIcon(), ENT_QUOTES, 'UTF-8') ?></span>
                                        <h3 class="pack-card__title"><?= htmlspecialchars($card->getName(), ENT_QUOTES, 'UTF-8') ?></h3>
                                        <span class="pack-card__rarity"><?= htmlspecialchars($card->getRarityName(), ENT_QUOTES, 'UTF-8') ?></span>
                                        <p class="pack-card__desc"><?= htmlspecialchars($card->getDescription(), ENT_QUOTES, 'UTF-8') ?></p>
                                        <?php if ($openingCard->isNew()): ?>
                                            <span class="pack-card__new">NEW<span class="visually-hidden"> card</span></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    </main>
