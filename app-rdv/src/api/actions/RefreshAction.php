<?php

namespace toubilib\api\actions;

use _PHPStan_2d0955352\Nette\Neon\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RefreshAction {
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        // try
            // lancer refresh

            // ok -> retourne token et statut ok

        // catch mauvais refresh token

            // ko -> retourne statut ko

        $response->getBody()->write(json_encode());
        return $response->withHeader("Content-Type", "application/json");
    }
}