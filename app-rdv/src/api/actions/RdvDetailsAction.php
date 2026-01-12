<?php

namespace toubilib\api\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use toubilib\core\application\usecases\interfaces\ServiceRendezVousInterface;
use toubilib\core\exceptions\EntityNotFoundException;

class RdvDetailsAction {
    private ServiceRendezVousInterface $serviceRdv;

    public function __construct(ServiceRendezVousInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    /**
     * @throws Exception
     * @throws HttpBadRequestException
     * @throws EntityNotFoundException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id_prat = $args['id_prat'] ?? null;
        $id_rdv = $args['id_rdv'] ?? null;

        if(empty($id_prat)) {
            throw new HttpBadRequestException($request,"Saisissez un id de Praticien");
        }

        if(empty($id_rdv)) {
            throw new HttpBadRequestException($request,"Saisissez un id de Rendez vous");
        }

        try {
            $rdv= $this->serviceRdv->getRDV($id_prat, $id_rdv);
            $rdv->links = [
                "self" => [
                    "href" => "/praticiens/" . $rdv->praticien_id . "/rdvs/" . $rdv->id
                ],
                "praticien" => [
                    "href" => "/praticiens/" . $rdv->praticien_id
                ],
            ];
            $response->getBody()->write(json_encode(($this->serviceRdv->getRDV($id_prat, $id_rdv))));
            return $response->withHeader("Content-Type", "application/json");
        }  catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." introuvable", $e->getEntity());
        } catch (Exception) {
            throw new Exception("Erreur lors de l'obtention du RDV");
        }
    }
}