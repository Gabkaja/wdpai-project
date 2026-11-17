<aside class="settings__sidebar">
    <nav class="settings__nav" aria-label="Settings">
        <ul class="settings__list" role="list">
            <?php
            $sections = [
                'account' => 'Account',
                'packs' => 'Packs',
                'privacy' => 'Privacy',
                'performance' => 'Performance',
            ];
            foreach ($sections as $key => $label): ?>
                <li class="settings__item">
                    <a class="settings__link<?= $section === $key ? ' settings__link--active' : '' ?>" href="/settings/<?= htmlspecialchars($key) ?>"<?= $section === $key ? ' aria-current="page"' : '' ?> role="menuitem"><?= htmlspecialchars($label) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

<div class="settings__content">
    <?= $content ?>
</div>
