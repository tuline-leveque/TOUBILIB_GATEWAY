<?php

namespace auth\api\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Routing\RouteContext;
use auth\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use auth\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use toubilib\core\application\usecases\interfaces\ServiceRendezVousInterface;
use auth\core\domain\entities\user\User;


class AuthzAccessRdvDetailMiddleware {
    private ServiceAuthzPatientInterface $authz_service_patient;
    private ServiceAuthzPraticienInterface $authz_service_praticien;
    private ServiceRendezVousInterface $service_rendez_vous;

    public function __construct(
        ServiceAuthzPatientInterface $authz_service_patient,
        ServiceAuthzPraticienInterface $authz_service_praticien,
        ServiceRendezVousInterface $service_rendez_vous,
    ) {
        $this->authz_service_patient = $authz_service_patient;
        $this->authz_service_praticien = $authz_service_praticien;
        $this->service_rendez_vous = $service_rendez_vous;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {

        // 1. On récupère le payload que le middleware 'JwtAuthMiddleware' a préparé
        $payload = $request->getAttribute('user_payload');

        if (!$payload) {
            throw new HttpInternalServerErrorException($request, 'Le middleware JwtAuthMiddleware doit être exécuté avant.');
        }

        // 2. On lit le payload en tant qu'OBJET
        $user_id = $payload->sub;
        $role = intval($payload->data->role);

        // 3. On récupère les arguments de la route (plus sécurisé)
        $route = RouteContext::fromRequest($request)->getRoute();
        $id_prat = $route->getArgument('id_prat');
        $id_rdv = $route->getArgument('id_rdv');

        if (!$id_prat || !$id_rdv) {
            throw new HttpInternalServerErrorException($request, 'Paramètres de route manquants (id_prat, id_rdv)');
        }

        // 4. Ta logique de vérification (qui est parfaite)
        switch($role) {
            case User::PATIENT :
                $rdv = $this->service_rendez_vous->getRDV($id_prat, $id_rdv);
                $ressource_id = $rdv->patient_id;

                if(!$this->authz_service_patient->isGranted($user_id, $role, $ressource_id)) {
                    throw new HttpForbiddenException($request, 'Vous n\'avez pas accès à ce rendez-vous.');
                }
                break;

            case User::PRATICIEN :
                $ressource_id = $id_prat;

                if(!$this->authz_service_praticien->isGranted($user_id, $role, $ressource_id)) {
                    throw new HttpForbiddenException($request, 'Vous n\'avez pas accès aux ressources de ce praticien.');
                }
                break;

            default :
                throw new HttpForbiddenException($request, 'Rôle inconnu ou non autorisé.');
        }

        // 5. Si tout est bon, on continue
        return $next->handle($request);
    }
}