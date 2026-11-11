<?php

declare(strict_types=1);

use App\Controller\AboutController;
use App\Controller\CardController;
use App\Controller\DefaultController;
use App\Routing;
use App\Controller\ProfileController;
use App\Controller\LeaderboardController;
use App\Controller\SecurityController;
use App\Controller\AdminController;
use App\Controller\OverlayController;
use App\Controller\KickController;
use App\Controller\PackController;
use App\Repository\LogRepository;
use App\Session;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routing = new Routing();

Session::start();

$routing->get('/', DefaultController::class, 'index');
$routing->get('/about', AboutController::class, 'index');
$routing->get('/cards', CardController::class, 'index');
$routing->get('/cards/{id}', CardController::class, 'show');
$routing->get('/profile', ProfileController::class, 'index');
$routing->get('/leaderboard', LeaderboardController::class, 'index');
$routing->get('/login', SecurityController::class, 'login');
$routing->post('/login', SecurityController::class, 'loginSubmit');
$routing->get('/register', SecurityController::class, 'register');
$routing->post('/register', SecurityController::class, 'registerSubmit');
$routing->get('/admin', AdminController::class, 'index');
$routing->get('/overlay', OverlayController::class, 'index');
$routing->get('/auth/kick/login', KickController::class, 'login');
$routing->get('/profile/kick/connect', KickController::class, 'connect');
$routing->get('/auth/kick/callback', KickController::class, 'callback');
$routing->post('/profile/kick/unlink', KickController::class, 'unlink');
$routing->post('/logout', SecurityController::class, 'logout');
$routing->post('/admin/users/{id}/points', AdminController::class, 'grantPoints');
$routing->post('/packs/open', PackController::class, 'open');
$routing->get('/packs/{id}', PackController::class, 'show');

try {
    $routeFound = $routing->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI'],
    );

    if (!$routeFound) {
        http_response_code(404);
        (new DefaultController())->notFound();
    }
} catch (\Throwable $exception) {
    try {
        $pdo = (new App\Database())->connect();
        $logRepository = new LogRepository($pdo);

        $logRepository->add(
            'error',
            $exception->getMessage(),
            [
                'class' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'uri' => $_SERVER['REQUEST_URI'] ?? '',
            ]
        );
    } catch (\Throwable $loggingException) {
        error_log($loggingException->getMessage());
    }

    http_response_code(500);
    (new DefaultController())->serverError();
}