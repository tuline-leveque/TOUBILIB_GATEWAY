<?php

namespace toubilib\api\actions;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\application\usecases\interfaces\ServiceRendezVousInterface;
use toubilib\core\exceptions\CreneauException;
use toubilib\core\exceptions\EntityNotFoundException;

class CreateRdvAction {
    private ServiceRendezVousInterface $serviceRdv;

    public function __construct(ServiceRendezVousInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    /**
     * @throws Exception
     * @throws EntityNotFoundException
     * @throws CreneauException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $rdv_dto = $request->getAttribute('rdv_dto') ?? null;

        if(is_null($rdv_dto)) {
            throw new Exception("Erreur récupération DTO de création d'un rendez-vous");
        }

        try {
            $response->getBody()->write(json_encode($this->serviceRdv->creerRendezVous($rdv_dto)));
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity() . " introuvable", $e->getEntity());
        } catch(CreneauException $e) {
            throw new CreneauException($e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Erreur Serveur, réessayer plus tard", 500);
        }

        return $response->withHeader("Content-Type", "application/json");
    }
}