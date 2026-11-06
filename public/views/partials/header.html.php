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
    <script>
        (function () {
            try {
                var stored = window.localStorage.getItem('streamrip-theme');
                var theme = stored === 'light' || stored === 'dark'
                    ? stored
                    : (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/theme.js" defer></script>
</head>
<body>
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