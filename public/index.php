<?php

declare(strict_types=1);

use App\Controller\CardController;
use App\Controller\DefaultController;
use App\Routing;
use App\Controller\ProfileController;
use App\Controller\LeaderboardController;
use App\Controller\SecurityController;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routing = new Routing();

$routing->get('/', DefaultController::class, 'index');
$routing->get('/cards', CardController::class, 'index');
$routing->get('/cards/{id}', CardController::class, 'show');
$routing->get('/profile', ProfileController::class, 'index');
$routing->get('/leaderboard', LeaderboardController::class, 'index');
$routing->get('/login', SecurityController::class, 'login');
$routing->get('/register', SecurityController::class, 'register');

$routeFound = $routing->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);

if (!$routeFound) {
    http_response_code(404);
    (new DefaultController())->notFound();
}