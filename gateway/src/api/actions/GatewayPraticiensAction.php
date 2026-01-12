<?php

namespace gateway\api\actions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class GatewayPraticiensAction {
    private ClientInterface $remote_praticien_service;

    public function __construct(ClientInterface $client) {
        $this->remote_praticien_service = $client;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $path = ltrim($request->getUri()->getPath(), '/');
        try {
            return $this->remote_praticien_service->request(
                $request->getMethod(),
                $path
            );
        } catch (ClientException $e) {
            throw new HttpNotFoundException($request, $e);
        }
    }
}