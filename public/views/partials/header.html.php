<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$navItems = [
    ['href' => '/', 'label' => 'Catalog'],
    ['href' => '/profile', 'label' => 'Profile'],
    ['href' => '/leaderboard', 'label' => 'Leaderboard'],
    ['href' => '/admin', 'label' => 'Admin'],
];

$isCurrent = static function (string $href) use ($currentPath): bool {
    if ($href === '/') {
        return $currentPath === '/' || str_starts_with($currentPath, '/cards');
    }

    return str_starts_with($currentPath, $href);
};
?>
    <header class="site-header">
        <div class="site-header__inner container">
            <a class="brand" href="/">StreamRip</a>

            <nav class="main-nav" aria-label="Main navigation">
                <ul class="main-nav__list">
                    <?php foreach ($navItems as $item): ?>
                        <li>
                            <a class="nav__link" href="<?= htmlspecialchars($item['href']) ?>"<?= $isCurrent($item['href']) ? ' aria-current="page"' : '' ?>><?= htmlspecialchars($item['label']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <div class="header-tools">
                <div class="lang-switch" role="group" aria-label="Language">
                    <button class="lang-switch__button" type="button" aria-pressed="false">PL</button>
                    <button class="lang-switch__button" type="button" aria-pressed="true">EN</button>
                </div>

                <button class="icon-button" type="button" aria-label="Switch to light theme" data-theme-toggle>
                    <span class="icon" aria-hidden="true">light_mode</span>
                </button>

                <a class="button button--primary" href="/login">Log in<span class="button__extra"> / Register</span></a>

                <a class="avatar" href="/profile" aria-label="Your profile">
                    <span class="icon" aria-hidden="true">person</span>
                </a>
            </div>
        </div>
    </header>