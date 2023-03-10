<?php

namespace App\Controllers\Api\V1;

use App\Repositories\QuestionRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class QuestionController
{
    function __construct(private QuestionRepository $questionRepository)
    {
    }

    public function find(Request $request, Response $response, array $args): Response 
    {
        $id = $args['id'] ?? NULL;

        if(!$id){
            throw new RuntimeException('No ID Provided', 400);
        }

        $data = $this->questionRepository->find($id);

        $data['choices'] = json_decode($data['choices']);

        $payload = $data;

        $response->getBody()->write(json_encode($payload));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getAnswers(Request $request, Response $response, array $args): Response
    {
        $payload = $this->questionRepository->getAnswers();

        $response->getBody()->write(json_encode($payload));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}