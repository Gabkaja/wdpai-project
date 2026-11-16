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
use App\Controller\PackApiController;
use App\Repository\LogRepository;
use App\Session;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routing = new Routing();

Session::start();

$sendJson = static function (int $status, array $payload): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_THROW_ON_ERROR);
    exit;
};

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isApiRequest = str_starts_with($path, '/api/');

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
$routing->post('/api/packs/buy', PackApiController::class, 'buy');
$routing->post('/api/packs/open', PackApiController::class, 'open');
$routing->post('/api/settings/packs', PackApiController::class, 'saveSettings');
$routing->get('/api/packs/history', PackApiController::class, 'history');

try {
    $routeFound = $routing->dispatch(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI'],
    );

    if (!$routeFound) {
        if ($isApiRequest) {
            $sendJson(404, [
                'ok' => false,
                'error' => [
                    'code' => 'not_found',
                    'message' => 'Resource not found.',
                ],
            ]);
        }

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

    if ($isApiRequest) {
        $sendJson(500, [
            'ok' => false,
            'error' => [
                'code' => 'internal_error',
                'message' => 'An internal error occurred.',
            ],
        ]);
    }

    http_response_code(500);
    (new DefaultController())->serverError();
}