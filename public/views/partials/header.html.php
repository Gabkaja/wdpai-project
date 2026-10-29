<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Outfit:wght@600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <a href="/">StreamRip</a>

        <nav aria-label="Main navigation">
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/cards">All cards</a></li>
                <li><a href="/leaderboard">Leaderboard</a></li>
                <li><a href="/profile">Profile</a></li>
                <li><a href="/admin">Admin</a></li>
                <li><a href="/login">Log in</a></li>
                <li><a href="/register">Register</a></li>
            </ul>
        </nav>

        <form role="search" action="/" method="get">
            <label for="card-search">Search cards by name</label>
            <input type="search" id="card-search" name="q">
            <button type="submit">Search</button>
        </form>
    </header>