<?php
$maxUniqueCards = max(array_column($players, 'uniqueCards'));

$formatAgo = static function (int $seconds): string {
    if ($seconds < 60) {
        return $seconds . 's ago';
    }

    return intdiv($seconds, 60) . 'm ago';
};
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Outfit:wght@600;700;800&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&amp;display=block" rel="stylesheet">
    <link rel="stylesheet" href="/css/overlay.css">
</head>
<body class="overlay-page">
    <main class="overlay">
        <h1 class="visually-hidden">StreamRip live stream overlay</h1>

        <header class="overlay__header">
            <div class="overlay__brand">
                <span class="overlay__logo">StreamRip</span>
                <span class="overlay__tagline">Live card drops</span>
            </div>

            <div class="overlay__status">
                <span class="overlay__badge overlay__badge--live">
                    <span class="overlay__pulse" aria-hidden="true"></span>
                    Live
                </span>
                <span class="overlay__badge">
                    <span class="icon" aria-hidden="true">visibility</span>
                    <?= number_format($stats['viewers']) ?> viewers
                </span>
                <span class="overlay__badge overlay__badge--channel">
                    <span class="icon" aria-hidden="true">sensors</span>
                    <?= htmlspecialchars($channel) ?>
                </span>
            </div>
        </header>

        <div class="overlay__content">
            <section class="panel drops" aria-labelledby="drops-title">
                <div class="drops__header">
                    <div>
                        <h2 class="drops__title" id="drops-title">Recent drops</h2>
                        <p class="drops__subtitle">Opened by chat during the live broadcast</p>
                    </div>
                    <span class="drops__pool">
                        <span class="icon" aria-hidden="true">inventory_2</span>
                        <?= (int) $stats['activeCards'] ?> cards in pool
                    </span>
                </div>

                <ol class="drops__list" role="list">
                    <?php foreach ($recentDrops as $index => $drop): ?>
                        <li class="drop rarity-<?= htmlspecialchars($drop['card']['rarity']) ?>">
                            <span class="drop__rank" aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>

                            <span class="drop__icon" aria-hidden="true">
                                <span class="icon"><?= htmlspecialchars($drop['card']['icon']) ?></span>
                            </span>

                            <div class="drop__body">
                                <div class="drop__title-row">
                                    <h3 class="drop__name"><?= htmlspecialchars($drop['card']['name']) ?></h3>
                                    <span class="rarity-badge"><?= htmlspecialchars(ucfirst($drop['card']['rarity'])) ?></span>
                                </div>
                                <p class="drop__meta">
                                    <span class="drop__code">#<?= htmlspecialchars($drop['card']['code']) ?></span>
                                    <span class="drop__divider" aria-hidden="true">/</span>
                                    dropped by <strong>@<?= htmlspecialchars($drop['username']) ?></strong>
                                </p>
                            </div>

                            <time class="drop__time"><?= htmlspecialchars($formatAgo($drop['secondsAgo'])) ?></time>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </section>

            <aside class="overlay__side" aria-label="Live statistics">
                <section class="panel board" aria-labelledby="board-title">
                    <div class="board__header">
                        <h2 class="board__title" id="board-title">Top collectors</h2>
                        <span class="board__hint">unique cards</span>
                    </div>

                    <ol class="board__list" role="list">
                        <?php foreach ($players as $player): ?>
                            <?php $value = (int) round(($player['uniqueCards'] / $maxUniqueCards) * 100); ?>
                            <li class="board__row">
                                <span class="board__rank board__rank--<?= (int) $player['rank'] ?>"><?= (int) $player['rank'] ?></span>
                                <span class="board__name"><?= htmlspecialchars($player['username']) ?></span>
                                <span class="board__score"><?= (int) $player['uniqueCards'] ?></span>
                                <span class="board__bar" aria-hidden="true">
                                    <span class="board__bar-fill" style="--value: <?= $value ?>%"></span>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </section>

                <section class="panel stats" aria-labelledby="stats-title">
                    <h2 class="stats__title" id="stats-title">Session stats</h2>

                    <dl class="stats__grid">
                        <div class="stat">
                            <dt class="stat__label">Packs opened</dt>
                            <dd class="stat__value"><?= number_format($stats['packsOpened']) ?></dd>
                        </div>
                        <div class="stat">
                            <dt class="stat__label">Legendaries</dt>
                            <dd class="stat__value stat__value--gold"><?= (int) $stats['legendaries'] ?></dd>
                        </div>
                        <div class="stat">
                            <dt class="stat__label">Active cards</dt>
                            <dd class="stat__value"><?= (int) $stats['activeCards'] ?></dd>
                        </div>
                        <div class="stat">
                            <dt class="stat__label">Viewers</dt>
                            <dd class="stat__value"><?= number_format($stats['viewers']) ?></dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>

        <footer class="overlay__footer">
            <p class="overlay__ticker">
                <span class="icon" aria-hidden="true">bolt</span>
                Cards drop automatically during the live broadcast — keep watching!
            </p>
            <p class="overlay__channel"><?= htmlspecialchars($channel) ?></p>
        </footer>
    </main>
</body>
</html>
