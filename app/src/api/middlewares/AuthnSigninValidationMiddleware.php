<?php

namespace toubilib\api\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;
use toubilib\api\dtos\InputAuthnDTO;

class AuthnSigninValidationMiddleware {

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {

        // 1. On lit le CORPS de la requête
        $data = $request->getParsedBody();

        // 2. On s'assure que $data est un tableau
        if (!is_array($data)) {
            $data = [];
        }
//        var_dump($request->getHeaders());
//        die;
        $data["email"] = $data["email"] ?? "";
        $data["password"] = $data["password"] ?? "";
        // 3. On valide
        try {
            v::key('email', v::stringType()->notEmpty()->email())
            ->key('password', v::stringType()->notEmpty())
                ->assert($data);

        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($request, "Données invalides : " . $e->getFullMessage(), $e);
        }

        // 4. On CRÉE le DTO
        $authDto = new InputAuthnDTO($data);

        // 5. On ATTACHE le DTO à la requête pour l'action suivante
        $request = $request->withAttribute('auth_dto', $authDto);

        return $next->handle($request);
    }
}