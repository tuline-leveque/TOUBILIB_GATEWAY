<?php

namespace toubilib\api\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;
use toubilib\api\dtos\InputUserDTO;

class EnregistrerUtilisateurMiddleware {
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {

        $data = $request->getParsedBody();

        try {
            v::key('email', v::stringType()->notEmpty())
                ->key('password', v::stringType()->notEmpty())
                ->assert($data);

        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($request, "Invalid data: " . $e->getFullMessage(), $e);
        }

        $userDTO = new InputUserDTO($data);
        $request = $request->withAttribute('user_dto', $userDTO);

        return $next->handle($request);
    }
}