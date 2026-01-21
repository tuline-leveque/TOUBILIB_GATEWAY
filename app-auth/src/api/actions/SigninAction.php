<?php

namespace auth\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use auth\core\application\usecases\interfaces\ServiceAuthnInterface;
use auth\core\exceptions\ConnexionException;
use Slim\Exception\HttpInternalServerErrorException;

class SigninAction {

    private ServiceAuthnInterface $authnService;

    public function __construct(ServiceAuthnInterface $authnService) {
        $this->authnService = $authnService;
    }

    public function __invoke(Request $request, Response $response): Response {

        // 1. On récupère le DTO que le middleware a validé et créé
        $user_dto = $request->getAttribute('auth_dto');

        // Sécurité : si le DTO est absent, c'est une erreur de configuration
        if ($user_dto === null) {
            throw new HttpInternalServerErrorException($request, "Erreur de configuration du middleware.");
        }

        // 2. On récupère le host
        $host = $request->getUri()->getHost();

        // 3. On appelle le service
        try {
            $token = $this->authnService->login($user_dto, $host);

            $responseData = ['token' => $token];

            $response->getBody()->write(json_encode($responseData));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(200);

        } catch (ConnexionException $e) {
            // 4. On gère l'échec de la connexion
            $errorData = ['error' => $e->getMessage()];

            $response->getBody()->write(json_encode($errorData));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(401); // 401 Unauthorized
        }
    }
}