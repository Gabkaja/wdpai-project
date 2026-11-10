<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$navItems = [
    ['href' => '/', 'label' => 'Catalog'],
    ['href' => '/profile', 'label' => 'Profile'],
    ['href' => '/leaderboard', 'label' => 'Leaderboard'],
];

if (isset($currentUser) && $currentUser !== null && $currentUser->getRole() === 'admin') {
    $navItems[] = ['href' => '/admin', 'label' => 'Admin'];
}

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

                <?php if (isset($currentUser) && $currentUser !== null): ?>
                    <span class="header-tools__user" aria-hidden="true"><?= htmlspecialchars($currentUser->getUsername()) ?></span>
                    <a class="button button--primary header-tools__profile" href="/profile">
                        <span class="icon" aria-hidden="true">person</span>
                        Profile
                    </a>
                    <form class="header-tools__logout" method="post" action="/logout" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        <button class="button button--primary header-tools__logout-button" type="submit">
                            <span class="icon" aria-hidden="true">logout</span>
                            Logout
                        </button>
                    </form>
                <?php else: ?>
                    <a class="button button--primary header-tools__login" href="/login">
                        <span class="icon" aria-hidden="true">login</span>
                        Log in
                    </a>
                    <a class="button button--primary header-tools__register" href="/register">
                        <span class="icon" aria-hidden="true">person_add</span>
                        Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
