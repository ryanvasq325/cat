<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Carrega as variáveis do .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = AppFactory::create();

$app->addRoutingMiddleware();

$debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

// Inicia a sessão uma única vez para toda a aplicação
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$app->addErrorMiddleware($debug, $debug, $debug);

require __DIR__ . '/helpers/settings.php';
require __DIR__ . '/routes/routes.php';

return $app;
