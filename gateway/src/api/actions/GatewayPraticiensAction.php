<?php

namespace gateway\api\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\exceptions\ConnexionException;

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
            $body = json_decode($request->getBody()->getContents());
            $date_debut = $body->date_debut ?? null;
            $date_fin = $body->date_fin ?? null;
            $options = [];
            if($date_debut && $date_fin){
                $options = [
                    "json" => [
                        "date_debut" => $date_debut,
                        "date_fin" => $date_fin,
                    ]
                ];
            }
            $apiResponse = $this->remote_praticien_service->request(
                $request->getMethod(),
                $path,
                $options
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