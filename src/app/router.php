<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\Api\V1\QuestionController;
use App\Controllers\Api\V1\ProgressController;

$app->get('/', function (Request $request, Response $response): Response {
    $response->getBody()->write(file_get_contents(ROOT_PATH . '/views/index.html'));
    return $response;
});

$app->group('/api/v1', function (RouteCollectorProxy $group) {
    $group->group('/questions', function (RouteCollectorProxy $group) {
        $group->get('/{id}', [QuestionController::class, 'find']);
        $group->get('', [QuestionController::class, 'getAnswers']);
    });

    $group->group('/saved', function (RouteCollectorProxy $group) {
        $group->get('', [ProgressController::class, 'get']);
        $group->post('', [ProgressController::class, 'save']);
        $group->delete('', [ProgressController::class, 'delete']);
    });
});
