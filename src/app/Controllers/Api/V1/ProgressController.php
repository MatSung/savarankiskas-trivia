<?php

namespace App\Controllers\Api\V1;

use App\Repositories\ProgressRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;
use App\Helpers\IpGetter;
use App\Repositories\QuestionRepository;

class ProgressController
{

    function __construct(
        private ProgressRepository $progressRepository,
        private QuestionRepository $questionRepository
        )
    {
    }


    public function get(Request $request, Response $response, array $args): Response
    {
        $ip = IpGetter::getIpAddress($request);

        $data = $this->progressRepository->get($ip) ?? $this->progressRepository->create($ip);

        if($data['progress'] != null){
            $decodedProgress = json_decode($data['progress'], true);
            $payload = [
                'progress' => $decodedProgress,
                'lastPage' => $decodedProgress['questionId'] == $this->questionRepository->countEntries() ? true : false
            ];
        } else {
            $payload = null;
        }

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function save(Request $request, Response $response, array $args): Response
    {
        
        $ip = IpGetter::getIpAddress($request);

        $progress = $request->getParsedBody();
        $restoredProgress = $this->progressRepository->get($ip);
        $correctAnswers = json_decode($restoredProgress['correct_answers']);

        if($restoredProgress == null){
            throw new \RuntimeException('Server error', 500);
        }

        array_push(
            $correctAnswers,
            $progress['answer'] == $this->questionRepository->getAnswer($progress['questionId'])
        );

        $this->progressRepository->update($ip, json_encode($progress), json_encode($correctAnswers));

        $payload = $this->progressRepository->get($ip);
        $payload = $payload['progress'];
        $response->getBody()->write(json_encode($payload));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $ip = IpGetter::getIpAddress($request);

        try {
            $this->progressRepository->delete($ip);
        } catch (\RuntimeException $th) {
            return $response->withStatus(400);
        }

        return $response->withStatus(200);
    }

    public function getResults(Request $request, Response $response, array $args): Response
    {
        $ip = IpGetter::getIpAddress($request);

        try {
            $payload = $this->progressRepository->getResults($ip);
        } catch (\RuntimeException $th) {
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode($payload));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}
