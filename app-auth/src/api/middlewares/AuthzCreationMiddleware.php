<?php

namespace auth\api\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use auth\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use auth\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use auth\core\domain\entities\user\User;

class AuthzCreationMiddleware {
    private ServiceAuthzPatientInterface $authz_service_patient;
    private ServiceAuthzPraticienInterface $authz_service_praticien;

    // Ton constructeur est correct
    public function __construct(
        ServiceAuthzPatientInterface $authz_service_patient,
        ServiceAuthzPraticienInterface $authz_service_praticien
    ) {
        $this->authz_service_patient = $authz_service_patient;
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
        $role = intval($payload->data->role);

        // 3. On récupère la route
        $route = RouteContext::fromRequest($request)->getRoute();

        switch($role) {
            case User::PATIENT : // Si l'utilisateur connecté est un patient

                // 4. On lit le CORPS
                $body = $request->getParsedBody();

                // On s'assure que l'ID patient est bien dans le JSON envoyé
                if (!isset($body['id_pat'])) {
                    throw new HttpBadRequestException($request, 'Le champ \'id_pat\' est requis dans le corps de la requête.');
                }
                $ressource_id = $body['id_pat'];

                // 5. On vérifie la permission
                if(!$this->authz_service_patient->isGranted($user_id, $role, $ressource_id)) {
                    throw new HttpForbiddenException($request, 'Vous ne pouvez pas créer une ressource pour un autre patient.');
                }
                break;

            case User::PRATICIEN : // si l'utilisateur connecté est un praticien

                // On récupère l'ID du praticien depuis l'URL
                $id_prat = $route->getArgument('id_prat');
                if (!$id_prat) {
                    throw new HttpInternalServerErrorException($request, 'Paramètre de route {id_prat} manquant.');
                }
                $ressource_id = $id_prat;

                // 5. On vérifie la permission
                if(!$this->authz_service_praticien->isGranted($user_id, $role, $ressource_id)) {
                    throw new HttpForbiddenException($request, 'Vous n\'avez pas accès aux ressources de ce praticien.');
                }
                break;

            default :
                throw new HttpForbiddenException($request, 'Rôle inconnu ou non autorisé.');
        }

        return $next->handle($request);
    }
}