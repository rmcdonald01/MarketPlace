<?php

use Respect\Validation\Validator as v;

//allow script to run has long has needed
ini_set('MAX_EXECUTION_TIME', -1);

//Turn Warnings and Error reporting
ini_set('display_errors', 0);
//error_reporting(E_ERROR | E_WARNING);


session_start();

require __DIR__ . '/../vendor/autoload.php';

$dotenv = (new Dotenv\Dotenv(__DIR__ . '/..'))->load();

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]
    ],
]);

date_default_timezone_set('America/Jamaica');

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['auth'] = function ($container) {
    return new \App\Auth\Auth;
};

$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
        'isAdmin' => $container->auth->isAdmin(),
        'isSuperAdmin' => $container->auth->isSupperAdmin(),

    ]);

    $view->getEnvironment()->addGlobal('flash', $container->flash);

    return $view;
};


$container['Logger'] = function($container) {
    $logger = new \Monolog\Logger('logger');
    $filename = __DIR__ . '/../storage/logs/error.log';


    $stream = new \Monolog\Handler\StreamHandler($filename, \Monolog\Logger::DEBUG);
    $fingersCrossed = new \Monolog\Handler\FingersCrossedHandler($stream, \Monolog\Logger::DEBUG);
    $logger->pushHandler($fingersCrossed);

    $slackHandler = new \Monolog\Handler\SlackHandler(getenv('SLACK_ACCESS_TOKEN'), "#" . getenv('SLACK_CHANNEL'), getenv('SLACK_CHANNEL_BOT'));
    $slackHandler->setLevel(\Monolog\Logger::ERROR);
    $logger->pushHandler($slackHandler);

    $errorHandler = new \Monolog\ErrorHandler($logger);
    $errorHandler->registerExceptionHandler();
    $errorHandler->registerErrorHandler([], false);
    $errorHandler->registerFatalHandler();

    return $logger;
};

$container['errorHandler'] = function ($container) {
    return new App\Handlers\Error(true, $container['Logger']);
};

$container['phpErrorHandler'] = function ($container) {
    return new App\Handlers\PhpError(true, $container['Logger']);
};

$container['validator'] = function ($container) {
    return new App\Validation\Validator;
};

$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController($container);
};

$container['AuthController'] = function ($container) {
    return new \App\Controllers\Auth\AuthController($container);
};

$container['PasswordController'] = function ($container) {
    return new \App\Controllers\Auth\PasswordController($container);
};

$container['csrf'] = function ($container) {
    return new \Slim\Csrf\Guard;
};

// GuzzleHttp
$container['httpClient'] = function ($container) {
    return new \GuzzleHttp\Client;
};

$container['CheckOutController'] = function ($container) {
    return new \App\Controllers\CheckOutController($container);
};

//------------------------------------------------------------------------------------------------------------------------

// Application Logic - Inhjecting Controller into the container

//Example
// $container['ListController'] = function ($container) {
//     return new \App\Controllers\ListController($container);
// };



// Middleware
$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\MonoLoggerMiddleware($container));
//$app->add(new \App\Middleware\CsrfViewMiddleware($container));

//$app->add($container->csrf);

v::with('App\\Validation\\Rules\\');

require __DIR__ . '/../app/routes.php';
