<?php

namespace auth\api\actions;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ValidateTokenAction {
    private string $secret;

    public function __construct(string $secret) {
        $this->secret = $secret;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $token = $body['token'] ?? null;

        if (!$token) {
            $response->getBody()->write(json_encode(['error' => 'Missing token']));
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        try {
            JWT::decode($token, new Key($this->secret, 'HS512'));

            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(200);

        } catch (ExpiredException $e) {
            $response->getBody()->write(json_encode(['error' => 'Token expired']));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(401);

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(401);
        }
    }
}
