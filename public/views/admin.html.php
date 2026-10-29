<?php $this->partial('header', ['pageTitle' => $pageTitle]); ?>

    <aside>
        <nav aria-label="Admin sections">
            <ul>
                <li><a href="#cards">Cards</a></li>
                <li><a href="#users">Users and roles</a></li>
                <li><a href="#logs">System logs</a></li>
            </ul>
        </nav>
    </aside>

    <main>
        <h1>Admin panel</h1>

        <section id="cards" aria-labelledby="cards-heading">
            <h2 id="cards-heading">Cards</h2>

            <table>
                <caption>All cards in the game</caption>
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
                        <tr>
                            <th scope="row"><?= (int) $card['id'] ?></th>
                            <td><?= htmlspecialchars($card['name']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($card['rarity'])) ?></td>
                            <td><?= (int) $card['dropChance'] ?>%</td>
                            <td>
                                <button type="button">Edit</button>
                                <form method="post" action="/admin/cards/<?= (int) $card['id'] ?>/delete">
                                    <button type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form method="post" action="/admin/cards" enctype="multipart/form-data">
                <fieldset>
                    <legend>Add new card</legend>

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
                        <input type="number" id="card-drop" name="drop_chance" min="0" max="100" required>
                    </div>

                    <div>
                        <label for="card-lore">Lore</label>
                        <textarea id="card-lore" name="lore" rows="4"></textarea>
                    </div>

                    <div>
                        <label for="card-image">Card artwork</label>
                        <input type="file" id="card-image" name="image" accept="image/*">
                    </div>

                    <button type="submit">Add card</button>
                </fieldset>
            </form>
        </section>

        <section id="users" aria-labelledby="users-heading">
            <h2 id="users-heading">Users and roles</h2>

            <table>
                <caption>Registered users</caption>
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
                            <th scope="row"><?= (int) $user['id'] ?></th>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="post" action="/admin/users/<?= (int) $user['id'] ?>/role">
                                    <select name="role" aria-label="Role for <?= htmlspecialchars($user['username']) ?>">
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= htmlspecialchars($role) ?>"<?= $role === $user['role'] ? ' selected' : '' ?>>
                                                <?= htmlspecialchars(ucfirst($role)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section id="logs" aria-labelledby="logs-heading">
            <h2 id="logs-heading">System logs</h2>

            <table>
                <caption>Recent system events</caption>
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
                            <td><time datetime="<?= htmlspecialchars($log['time']) ?>"><?= htmlspecialchars($log['time']) ?></time></td>
                            <td><?= htmlspecialchars($log['level']) ?></td>
                            <td><?= htmlspecialchars($log['message']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Scheduled cleanup</h3>
            <dl>
                <dt>Last run</dt>
                <dd><?= htmlspecialchars($cleanup['lastRun']) ?></dd>
                <dt>Expired records removed</dt>
                <dd><?= (int) $cleanup['removed'] ?></dd>
                <dt>Next run</dt>
                <dd><?= htmlspecialchars($cleanup['nextRun']) ?></dd>
            </dl>
        </section>
    </main>

<?php $this->partial('footer'); ?>