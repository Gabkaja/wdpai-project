    <main class="admin">
        <section class="admin-hero" aria-labelledby="admin-title">
            <div class="admin-hero__inner">
                <div class="admin-hero__intro">
                    <p class="admin-hero__eyebrow">
                        <span class="dot dot--sync" aria-hidden="true"></span>
                        Operational console // V2.4.9
                        <span class="admin-hero__brand">StreamRip core services</span>
                    </p>

                    <h1 class="admin-hero__title" id="admin-title">Admin panel</h1>

                    <nav class="admin-tabs" aria-label="Admin sections">
                        <a class="admin-tab admin-tab--active" href="#cards">
                            <span class="icon" aria-hidden="true">style</span>
                            Cards
                        </a>
                        <a class="admin-tab" href="#users">
                            <span class="icon" aria-hidden="true">group</span>
                            Users and roles
                        </a>
                        <a class="admin-tab" href="#logs">
                            <span class="icon" aria-hidden="true">receipt_long</span>
                            System logs
                        </a>
                    </nav>
                </div>

                <ul class="admin-status" role="list">
                    <li>
                        <span class="pulse" aria-hidden="true"></span>
                        <div>
                            <span class="admin-status__label">Kick API</span>
                            <span class="admin-status__value">Connected</span>
                        </div>
                    </li>
                    <li>
                        <span class="icon" aria-hidden="true">settings_suggest</span>
                        <div>
                            <span class="admin-status__label">System status</span>
                            <span class="admin-status__value">Operational</span>
                        </div>
                    </li>
                    <li>
                        <span class="icon" aria-hidden="true">style</span>
                        <div>
                            <span class="admin-status__label">Active cards</span>
                            <span class="admin-status__value"><?= count($cards) ?> units</span>
                        </div>
                    </li>
                    <li>
                        <span class="icon" aria-hidden="true">admin_panel_settings</span>
                        <div>
                            <span class="admin-status__label">Session root</span>
                            <span class="admin-status__value">Admin [Root]</span>
                        </div>
                    </li>
                </ul>
            </div>
        </section>

        <section class="admin-section" id="cards" aria-labelledby="cards-heading">
            <div class="admin-section__head">
                <h2 class="admin-section__title" id="cards-heading">All cards in the game</h2>
                <a class="button button--primary" href="#add-card">
                    <span class="icon" aria-hidden="true">add_circle</span>
                    Add card
                </a>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="visually-hidden">All cards in the game</caption>
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Rarity</th>
                            <th scope="col">Drop chance</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cards as $card): ?>
                            <tr class="rarity-<?= htmlspecialchars($card['rarity']) ?>">
                                <th scope="row"><span class="admin-table__id">#<?= htmlspecialchars($card['code']) ?></span></th>
                                <td>
                                    <div class="admin-table__card">
                                        <span class="admin-table__icon">
                                            <span class="icon" aria-hidden="true"><?= htmlspecialchars($card['icon']) ?></span>
                                        </span>
                                        <span class="admin-table__name">
                                            <?= htmlspecialchars($card['name']) ?>
                                            <?php if ($card['rarity'] === 'legendary'): ?>
                                                <span class="icon admin-table__star" aria-hidden="true">star</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </td>
                                <td><span class="rarity-badge"><?= htmlspecialchars(ucfirst($card['rarity'])) ?></span></td>
                                <td><span class="admin-table__value"><?= number_format($card['dropChance'], 1) ?>%</span></td>
                                <td>
                                    <div class="admin-table__actions">
                                        <button class="admin-table__action admin-table__action--edit" type="button">Edit</button>
                                        <form method="post" action="/admin/cards/<?= (int) $card['id'] ?>/delete">
                                            <button class="admin-table__action admin-table__action--delete" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <form class="admin-form" id="add-card" method="post" action="/admin/cards" enctype="multipart/form-data">
                <fieldset class="admin-form__fieldset">
                    <legend class="admin-form__legend">Add new card</legend>

                    <div class="admin-form__grid">
                        <div>
                            <label for="card-name">Name</label>
                            <input type="text" id="card-name" name="name" required>
                        </div>

                        <div>
                            <label for="card-rarity">Rarity</label>
                            <select id="card-rarity" name="rarity" required>
                                <?php foreach ($rarities as $rarity): ?>
                                    <option value="<?= htmlspecialchars($rarity) ?>"><?= htmlspecialchars(ucfirst($rarity)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="card-drop">Drop chance (%)</label>
                            <input type="number" id="card-drop" name="drop_chance" min="0" max="100" step="0.1" required>
                        </div>

                        <div>
                            <label for="card-image">Card artwork</label>
                            <input type="file" id="card-image" name="image" accept="image/*">
                        </div>
                    </div>

                    <div>
                        <label for="card-lore">Lore</label>
                        <textarea id="card-lore" name="lore" rows="4"></textarea>
                    </div>

                    <button class="button button--primary" type="submit">
                        <span class="icon" aria-hidden="true">add_circle</span>
                        Add card
                    </button>
                </fieldset>
            </form>
        </section>

        <section class="admin-section" id="users" aria-labelledby="users-heading">
            <div class="admin-section__head">
                <h2 class="admin-section__title" id="users-heading">Users and roles</h2>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="visually-hidden">Registered users</caption>
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Username</th>
                            <th scope="col">E-mail</th>
                            <th scope="col">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <th scope="row"><span class="admin-table__id">#<?= (int) $user['id'] ?></span></th>
                                <td>
                                    <div class="admin-table__user">
                                        <span class="admin-table__avatar" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?></span>
                                        <span class="admin-table__name"><?= htmlspecialchars($user['username']) ?></span>
                                    </div>
                                </td>
                                <td><span class="admin-table__muted"><?= htmlspecialchars($user['email']) ?></span></td>
                                <td>
                                    <form class="admin-table__role" method="post" action="/admin/users/<?= (int) $user['id'] ?>/role">
                                        <select name="role" aria-label="Role for <?= htmlspecialchars($user['username']) ?>">
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?= htmlspecialchars($role) ?>"<?= $role === $user['role'] ? ' selected' : '' ?>>
                                                    <?= htmlspecialchars(ucfirst($role)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="button button--secondary admin-table__save" type="submit">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-section" id="logs" aria-labelledby="logs-heading">
            <div class="admin-section__head">
                <h2 class="admin-section__title" id="logs-heading">System logs</h2>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <caption class="visually-hidden">Recent system events</caption>
                    <thead>
                        <tr>
                            <th scope="col">Time</th>
                            <th scope="col">Level</th>
                            <th scope="col">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><time class="admin-table__time" datetime="<?= htmlspecialchars($log['time']) ?>"><?= htmlspecialchars($log['time']) ?></time></td>
                                <td><span class="log-badge log-badge--<?= htmlspecialchars($log['level']) ?>"><?= htmlspecialchars($log['level']) ?></span></td>
                                <td><span class="admin-table__message"><?= htmlspecialchars($log['message']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-cleanup">
                <article class="admin-cleanup__item">
                    <span class="admin-cleanup__label">Last run</span>
                    <strong class="admin-cleanup__value"><?= htmlspecialchars($cleanup['lastRun']) ?></strong>
                </article>
                <article class="admin-cleanup__item">
                    <span class="admin-cleanup__label">Expired records removed</span>
                    <strong class="admin-cleanup__value"><?= (int) $cleanup['removed'] ?></strong>
                </article>
                <article class="admin-cleanup__item">
                    <span class="admin-cleanup__label">Next run</span>
                    <strong class="admin-cleanup__value"><?= htmlspecialchars($cleanup['nextRun']) ?></strong>
                </article>
            </div>
        </section>
    </main>
