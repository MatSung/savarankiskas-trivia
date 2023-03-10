<?php

// use App\Handlers;
use DI\Container;
use App\Handlers;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

$displayErrorDetails = isset($_ENV['APP_DEBUG']) ? strtolower($_ENV['APP_DEBUG']) == 'true' : false;

ini_set('display_errors', ($displayErrorDetails ? 1 : 0));

$container = new Container();

AppFactory::setContainer($container);

$app = AppFactory::create();

// Add JSON parser Middleware
$app->addBodyParsingMiddleware();

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// https://www.slimframework.com/docs/v4/middleware/error-handling.html
$errorHandler = new Handlers\HttpErrorHandler($callableResolver, $responseFactory);
$shutdownHandler = new Handlers\ShutdownHandler($request, $errorHandler, $displayErrorDetails);

register_shutdown_function($shutdownHandler);

// Add Error Handling Middleware
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

require_once ROOT_PATH . '/app/router.php';


$app->run();