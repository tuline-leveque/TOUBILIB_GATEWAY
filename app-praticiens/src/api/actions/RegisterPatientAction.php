<?php

namespace toubilib\api\actions;

use toubilib\core\application\usecases\interfaces\ServiceAuthnInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RegisterPatientAction {
    private ServiceAuthnInterface $serviceAuthn;
    public function __construct(ServiceAuthnInterface $serviceAuthn){
        $this->serviceAuthn = $serviceAuthn;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            $user_dto = $request->getAttribute('user_dto') ?? null;

            if(is_null($user_dto)) {
                throw new \Exception("Erreur récupération DTO de création d'un utilisateur");
            }

            $user_dto->email = filter_var(trim($user_dto->email), FILTER_SANITIZE_EMAIL);

            if (!filter_var($user_dto->email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Email invalide");
            }

            $password = trim($user_dto->password ?? '');
            $minLength = 8;
            $maxLength = 64;

            if (strlen($password) < $minLength) {
                throw new \Exception("Le mot de passe doit contenir au moins $minLength caractères");
            }

            if (strlen($password) > $maxLength) {
                throw new \Exception("Le mot de passe ne doit pas dépasser $maxLength caractères");
            }

            $res = $this->serviceAuthn->register($user_dto, 1);
            $response->getBody()->write(json_encode($res));
            return $response->withHeader("Content-Type", "application/json");

        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la création du compte." . $e->getMessage());
        } catch(\Throwable $e){
            throw new \Exception($e->getMessage());
        }
    }
}
