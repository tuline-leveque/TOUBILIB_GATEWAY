<?php

namespace auth\api\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Routing\RouteContext;
use auth\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use auth\core\domain\entities\user\User;


class AuthzAccessRdvsMiddleware {
    private ServiceAuthzPraticienInterface $authz_service_praticien;

    public function __construct(
        ServiceAuthzPraticienInterface $authz_service_praticien
    ) {
        $this->authz_service_praticien = $authz_service_praticien;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {

        // 1. On récupère le payload
        $payload = $request->getAttribute('user_payload');

        if (!$payload) {
            throw new HttpInternalServerErrorException($request, 'Le middleware JwtAuthMiddleware doit être exécuté avant.');
        }

        // 2. On lit le payload
        $user_id = $payload->sub;
        $role = intval($payload->data->role); // On caste en entier

        // 3. On récupère les arguments de la route
        $route = RouteContext::fromRequest($request)->getRoute();
        $id_prat = $route->getArgument('id_prat');

        if (!$id_prat) {
            throw new HttpInternalServerErrorException($request, 'Paramètre de route manquant (id_prat)');
        }

        // Seul un praticien peut voir une liste de RDV
        switch($role) {
            case User::PRATICIEN :
                // La ressource est l'ID du praticien dans l'URL
                $ressource_id = $id_prat;

                // On vérifie que le praticien connecté est bien celui de la route
                if(!$this->authz_service_praticien->isGranted($user_id, $role, $ressource_id)) {
                    throw new HttpForbiddenException($request, 'Vous n\'avez pas accès aux ressources de ce praticien.');
                }
                break;

            case User::PATIENT :
                throw new HttpForbiddenException($request, 'Vous n\'êtes pas autorisé à voir cette ressource.');

            default :
                throw new HttpForbiddenException($request, 'Rôle inconnu ou non autorisé.');
        }

        return $next->handle($request);
    }
}