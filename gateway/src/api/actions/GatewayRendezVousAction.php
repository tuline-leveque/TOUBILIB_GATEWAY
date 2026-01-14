<?php

namespace gateway\api\actions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use toubilib\core\exceptions\ConnexionException;

class GatewayRendezVousAction {
    private ClientInterface $remote_rendezVous_service;

    public function __construct(ClientInterface $client) {
        $this->remote_rendezVous_service = $client;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {

        $path = ltrim($request->getUri()->getPath(), '/');

        try {
            $apiResponse = $this->remote_rendezVous_service->request(
                $request->getMethod(),
                $path
            );

            $response->getBody()->write(
                $apiResponse->getBody()->getContents()
            );

            foreach ($apiResponse->getHeaders() as $name => $values) {
                $response = $response->withHeader($name, $values);
            }

            return $response->withStatus($apiResponse->getStatusCode());

        } catch (ClientException | ServerException $e) {
            $response->getBody()->write(
                $e->getResponse()->getBody()->getContents()
            );
            return $response
                ->withStatus($e->getResponse()->getStatusCode())
                ->withHeader('Content-Type', 'application/json');

        } catch (ConnectException | ConnexionException) {
            $response->getBody()->write(json_encode([
                'error' => 'AUTH_SERVICE_UNAVAILABLE',
                'message' => 'Authentication service unreachable'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(502);

        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode([
                'error' => 'GATEWAY_INTERNAL_ERROR',
                'message' => 'Unexpected gateway error'
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

}