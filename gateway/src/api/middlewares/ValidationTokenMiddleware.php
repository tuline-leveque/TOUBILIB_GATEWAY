<?php

namespace gateway\api\middlewares;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;

class ValidationTokenMiddleware
{
    private ClientInterface $serviceAuthn;

    public function __construct(ClientInterface $client)
    {
        $this->serviceAuthn = $client;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            throw new HttpUnauthorizedException($request, "Missing Authorization header");
        }
        if (!preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
            throw new HttpUnauthorizedException($request, "Invalid Authorization format");
        }

        $token = $matches[1];
        try {
            $response = $this->serviceAuthn->request(
                'POST',
                '/tokens/validate',
                [
                    'json' => ['token' => $token]
                ]
            );

        } catch (ClientException $e) {
            if ($e->getCode() === 401) {
                throw new HttpUnauthorizedException(
                    $request,
                    "Unauthorized ({$e->getMessage()})"
                );
            }
            throw $e;

        } catch (ConnectException | ServerException $e) {
            throw new \RuntimeException("Auth service unavailable", 503, $e);
        }

        return $handler->handle($request);
    }
}
