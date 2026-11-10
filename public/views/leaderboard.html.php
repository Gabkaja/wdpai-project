    <main class="leaderboard">
        <section class="stream-banner leaderboard-hero" aria-labelledby="leaderboard-title">
            <div class="stream-banner__inner">
                <div class="stream-banner__info">
                    <div class="stream-banner__tags">
                        <span class="badge badge--live">
                            <span class="pulse" aria-hidden="true"></span>
                            Season 1 &middot; Genesis
                        </span>
                        <span class="badge badge--category">Stream Rip Kick Drops</span>
                    </div>

                    <h1 class="stream-banner__title" id="leaderboard-title">Leaderboard</h1>
                    <p class="leaderboard-hero__lead">Official ranking of top StreamRip live unboxing collectors and card masters.</p>
                </div>

                <div class="countdown">
                    <span class="countdown__label">
                        <span class="icon" aria-hidden="true">schedule</span>
                        Season 1 ends in:
                    </span>
                    <div class="countdown__timer" aria-label="14 days, 6 hours, 42 minutes, 5 seconds remaining">
                        <span class="countdown__unit"><strong>14</strong><span>d</span></span>
                        <span class="countdown__sep" aria-hidden="true">:</span>
                        <span class="countdown__unit"><strong>06</strong><span>h</span></span>
                        <span class="countdown__sep" aria-hidden="true">:</span>
                        <span class="countdown__unit"><strong>42</strong><span>m</span></span>
                        <span class="countdown__sep" aria-hidden="true">:</span>
                        <span class="countdown__unit"><strong>05</strong><span>s</span></span>
                    </div>
                </div>
            </div>
        </section>

        <?php
        $maxUniqueCards = max(1, max(array_map(fn ($entry) => $entry->getUniqueCards(), $players)));
        $podiumClasses = ['gold', 'silver', 'bronze'];
        $medalLabels = ['Gold', 'Silver', 'Bronze'];
        ?>

        <section aria-labelledby="podium-heading">
            <h2 class="visually-hidden" id="podium-heading">Top 3 collectors</h2>

            <ol class="podium" role="list">
                <?php foreach ($podium as $index => $player): ?>
                    <?php $completion = (int) round($player->getUniqueCards() / $maxUniqueCards * 100); ?>
                    <li class="podium__item podium__item--<?= $index + 1 ?>">
                        <article class="podium__card podium__card--<?= $podiumClasses[$index] ?>">
                            <span class="podium__badge">
                                <span class="icon" aria-hidden="true">emoji_events</span>
                                <?= htmlspecialchars($medalLabels[$index]) ?>: <?= htmlspecialchars($player->getUsername()) ?>
                            </span>

                            <div class="podium__header">
                                <span class="podium__avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($player->getUsername(), 0, 1))) ?></span>
                                <div>
                                    <h3 class="podium__name">
                                        <?= htmlspecialchars($player->getUsername()) ?>
                                        <span class="icon" aria-hidden="true">verified</span>
                                    </h3>
                                    <p class="podium__meta">Unique cards: <?= (int) $player->getUniqueCards() ?></p>
                                </div>
                            </div>

                            <div class="progress">
                                <div class="progress__label-row">
                                    <span>Genesis deck completion</span>
                                    <span class="progress__value"><?= $completion ?>%</span>
                                </div>
                                <span class="progress__track">
                                    <span class="progress__bar" style="width: <?= $completion ?>%"></span>
                                </span>
                            </div>

                            <p class="podium__rarest">
                                <span class="icon" aria-hidden="true">target</span>
                                Rarest card: <strong>--</strong>
                            </p>

                            <p class="podium__packs">
                                <span>Packs opened</span>
                                <strong>--</strong>
                            </p>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>

        <section class="leaderboard-table" aria-labelledby="table-heading">
            <h2 class="visually-hidden" id="table-heading">Full ranking</h2>

            <div class="leaderboard-controls">
                <div class="search">
                    <span class="icon search__icon" aria-hidden="true"></span>
                    <label class="visually-hidden" for="leaderboard-search">Search collector</label>
                    <input class="search__input" type="search" id="leaderboard-search" name="q" placeholder="Search collector...">
                </div>

                <div class="filters" role="group" aria-label="Leaderboard filters">
                    <button class="pill" type="button" aria-pressed="true">Top 50</button>
                    <button class="pill" type="button" aria-pressed="false">My friends</button>
                    <button class="pill" type="button" aria-pressed="false">Legendary tier</button>
                    <button class="pill" type="button" aria-pressed="false">Kick partners</button>
                </div>
            </div>

            <div class="leaderboard-table__scroll">
                <table class="ranking">
                    <caption class="visually-hidden">All collectors ranked by number of unique cards</caption>
                    <thead>
                        <tr>
                            <th scope="col">Rank</th>
                            <th scope="col">Username</th>
                            <th scope="col">Unique cards</th>
                            <th scope="col">Packs opened</th>
                            <th scope="col">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $player): ?>
                            <?php $completion = (int) round($player->getUniqueCards() / $maxUniqueCards * 100); ?>
                            <tr>
                                <th scope="row">
                                    <span class="ranking__rank ranking__rank--<?= (int) $player->getRankPosition() ?>"><?= (int) $player->getRankPosition() ?></span>
                                </th>
                                <td>
                                    <div class="ranking__player">
                                        <span class="ranking__avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($player->getUsername(), 0, 1))) ?></span>
                                        <div>
                                            <span class="ranking__name">
                                                <?= htmlspecialchars($player->getUsername()) ?>
                                                <span class="icon" aria-hidden="true">verified</span>
                                            </span>
                                            <span class="ranking__sub">Rarest card: --</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="ranking__cards">
                                        <span class="ranking__value"><?= (int) $player->getUniqueCards() ?></span>
                                        <span class="progress__track">
                                            <span class="progress__bar" style="width: <?= $completion ?>%"></span>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="ranking__packs">
                                        <span class="ranking__value">--</span>
                                        <?php if ($player->getRankPosition() === 1): ?>
                                            <span class="ranking__tag">Top</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <time class="ranking__joined" datetime="--">--</time>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="leaderboard-footer">
                <p>Showing 1&ndash;<?= count($players) ?> of <?= count($players) ?> collectors</p>
                <nav class="pagination" aria-label="Leaderboard pagination">
                    <a class="pagination__link pagination__link--arrow" href="/leaderboard?page=<?= max(1, 1 - 1) ?>" aria-label="Previous page">
                        <span class="icon" aria-hidden="true">chevron_left</span>
                    </a>
                    <?php for ($page = 1; $page <= 1; $page++): ?>
                        <a class="pagination__link" href="/leaderboard?page=<?= $page ?>"<?= $page === 1 ? ' aria-current="page"' : '' ?>>Page <?= $page ?></a>
                    <?php endfor; ?>
                    <a class="pagination__link pagination__link--arrow" href="/leaderboard?page=<?= min(1, 1 + 1) ?>" aria-label="Next page">
                        <span class="icon" aria-hidden="true">chevron_right</span>
                    </a>
                </nav>
            </div>
        </section>
    </main>
