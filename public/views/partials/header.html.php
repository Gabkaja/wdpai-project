<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <header>
        <a href="/">StreamRip</a>

        <nav aria-label="Main navigation">
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/cards">All cards</a></li>
            </ul>
        </nav>

        <form role="search" action="/" method="get">
            <label for="card-search">Search cards by name</label>
            <input type="search" id="card-search" name="q">
            <button type="submit">Search</button>
        </form>
    </header>