<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <main>
        <h1>Leaderboard</h1>

        <section aria-labelledby="podium-heading">
            <h2 id="podium-heading">Top 3 collectors</h2>
            <?php $medals = ['Gold', 'Silver', 'Bronze']; ?>
            <ol>
                <?php foreach ($podium as $index => $player): ?>
                    <li>
                        <article>
                            <h3><?= htmlspecialchars($medals[$index]) ?>: <?= htmlspecialchars($player['username']) ?></h3>
                            <dl>
                                <dt>Unique cards</dt>
                                <dd><?= (int) $player['uniqueCards'] ?></dd>
                                <dt>Rarest card</dt>
                                <dd><?= htmlspecialchars($player['rarestCard']) ?></dd>
                            </dl>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>

        <section aria-labelledby="table-heading">
            <h2 id="table-heading">Full ranking</h2>
            <table>
                <caption>All collectors ranked by number of unique cards</caption>
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
                        <tr>
                            <th scope="row"><?= (int) $player['rank'] ?></th>
                            <td><?= htmlspecialchars($player['username']) ?></td>
                            <td><?= (int) $player['uniqueCards'] ?></td>
                            <td><?= (int) $player['packsOpened'] ?></td>
                            <td><time datetime="<?= htmlspecialchars($player['joined']) ?>"><?= htmlspecialchars($player['joined']) ?></time></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <nav aria-label="Leaderboard pagination">
                <ul>
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <li>
                            <?php if ($page === $currentPage): ?>
                                <a href="/leaderboard?page=<?= $page ?>" aria-current="page">Page <?= $page ?></a>
                            <?php else: ?>
                                <a href="/leaderboard?page=<?= $page ?>">Page <?= $page ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </section>
    </main>

<?php $this->partial('footer'); ?>