<?php

namespace rdvs\api\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

class CorsMiddleware {
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : Response {
        if (! $request->hasHeader('Origin'))
            New HttpUnauthorizedException ($request, "missing Origin Header (cors)");
        $response = $next->handle($request);
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', 'http://myapp.net')
            ->withHeader('Access-Control-Allow-Methods', 'POST, PUT, GET' )
            ->withHeader('Access-Control-Allow-Headers','Authorization' )
            ->withHeader('Access-Control-Max-Age', 3600)
            ->withHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}