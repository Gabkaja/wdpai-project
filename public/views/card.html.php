    <main class="card-detail">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
                <li><a href="/">Catalog</a></li>
                <li><a href="/cards">All cards</a></li>
                <li aria-current="page"><?= htmlspecialchars($card->getName()) ?></li>
            </ol>
        </nav>

        <article class="card-detail__panel rarity-<?= htmlspecialchars($card->getRarityCode()) ?>">
            <div class="card-detail__media">
                <span class="card-detail__code">#<?= htmlspecialchars($card->getCode()) ?></span>
                <span class="card-detail__rarity"><?= htmlspecialchars($card->getRarityName()) ?></span>
                <span class="card-detail__icon">
                    <span class="icon" aria-hidden="true"><?= htmlspecialchars($card->getIcon()) ?></span>
                </span>
            </div>

            <div class="card-detail__body">
                <div class="card-detail__tags">
                    <span class="badge badge--live">
                        <span class="pulse" aria-hidden="true"></span>
                        Live drop
                    </span>
                    <span class="badge badge--category">Season 1 &middot; Genesis</span>
                </div>

                <h1 class="card-detail__title"><?= htmlspecialchars($card->getName()) ?></h1>
                <p class="card-detail__description"><?= htmlspecialchars($card->getDescription()) ?></p>

                <dl class="card-detail__stats">
                    <div class="card-detail__stat">
                        <dt>Drop chance</dt>
                        <dd><?= number_format($card->getDropChance(), 1) ?>%</dd>
                        <span class="progress__track">
                            <span class="progress__bar" style="width: <?= (float) $card->getDropChance() ?>%"></span>
                        </span>
                    </div>
                    <div class="card-detail__stat">
                        <dt>Rarity</dt>
                        <dd><?= htmlspecialchars($card->getRarityName()) ?></dd>
                    </div>
                    <div class="card-detail__stat">
                        <dt>Card code</dt>
                        <dd>#<?= htmlspecialchars($card->getCode()) ?></dd>
                    </div>
                </dl>

                <div class="card-detail__actions">
                    <a class="button button--primary" href="/cards">
                        <span class="icon" aria-hidden="true">grid_view</span>
                        Browse catalog
                    </a>
                    <a class="button button--secondary" href="/profile">
                        <span class="icon" aria-hidden="true">style</span>
                        View in collection
                    </a>
                </div>

                <p class="card-detail__note">
                    <span class="icon" aria-hidden="true">info</span>
                    Cards drop while you watch the live Kick stream.
                </p>
            </div>
        </article>

        <section class="card-detail__facts" aria-labelledby="drop-details-heading">
            <h2 class="visually-hidden" id="drop-details-heading">Drop details</h2>

            <ul class="card-detail__fact-list" role="list">
                <li class="card-detail__fact">
                    <span class="icon" aria-hidden="true">sensors</span>
                    <div>
                        <strong>Live sync</strong>
                        <span>Drops are tied to the Kick broadcast, not to your wallet.</span>
                    </div>
                </li>
                <li class="card-detail__fact">
                    <span class="icon" aria-hidden="true">verified</span>
                    <div>
                        <strong>Verified mint</strong>
                        <span>Every pull is logged with a unique mint grade.</span>
                    </div>
                </li>
                <li class="card-detail__fact">
                    <span class="icon" aria-hidden="true">swap_horiz</span>
                    <div>
                        <strong>Trade ready</strong>
                        <span>Inspect and trade your cards with other collectors.</span>
                    </div>
                </li>
            </ul>
        </section>
    </main>
