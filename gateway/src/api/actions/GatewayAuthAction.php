<?php

namespace gateway\api\actions;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use auth\core\exceptions\ConnexionException;

class GatewayAuthAction {
    private ClientInterface $remote_auth_service;

    public function __construct(ClientInterface $client) {
        $this->remote_auth_service = $client;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $path = ltrim($request->getUri()->getPath(), '/');

        try {
            $body = $request->getBody()->getContents();
            $options = $request->getHeaders();
            $headers = [];
            if ($request->hasHeader('Content-Type')) {
                $headers['Content-Type'] = $request->getHeaderLine('Content-Type');
            }
            if ($request->hasHeader('Authorization')) {
                $headers['Authorization'] = $request->getHeaderLine('Authorization');
            }

            if (!empty($headers)) {
                $options['headers'] = $headers;
            }

            if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && !empty($body)) {
                $options['body'] = $body;
            }

            $guzzleResponse = $this->remote_auth_service->request(
                $request->getMethod(),
                $path,
                $options
            );

            $response->getBody()->write($guzzleResponse->getBody()->getContents());
            $response = $response->withStatus($guzzleResponse->getStatusCode());

            foreach ($guzzleResponse->getHeaders() as $name => $values) {
                $response = $response->withHeader($name, $values);
            }

            return $response;

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