<?php

namespace App\Controllers\Api\V1;

use App\Repositories\ProgressRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class ProgressController
{
    function __construct(private ProgressRepository $progressRepository)
    {
    }

    public function get(Request $request, Response $response, array $args): Response 
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'];

        try {
            $payload = $this->progressRepository->get($ip);
        } catch (\RuntimeException $e) {
            $this->progressRepository->create($ip);
            $payload = $this->progressRepository->get($ip);
        }
        $payload = $payload['progress'];
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function save(Request $request, Response $response, array $args): Response 
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'];
        $progress = json_encode($request->getParsedBody());

        if(!$this->progressRepository->update($ip, $progress)){
            throw new \RuntimeException('No progress found', 400);
        }

        $payload = $this->progressRepository->get($ip);
        $payload = $payload['progress'];
        $response->getBody()->write(json_encode($payload));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function delete(Request $request, Response $response, array $args): Response 
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'];

        try {
            $this->progressRepository->delete($ip);
        } catch (\RuntimeException $th) {
            return $response->withStatus(400);
        }

        return $response->withStatus(200);
    }
}