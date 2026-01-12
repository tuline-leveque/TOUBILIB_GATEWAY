<?php

namespace toubilib\api\actions;

use _PHPStan_2d0955352\Nette\Neon\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\application\usecases\interfaces\ServiceRendezVousInterface;

class ValiderRdvAction
{
    private ServiceRendezVousInterface $serviceRdv;

    public function __construct(ServiceRendezVousInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    /**
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id_prat = $args['id_prat'] ?? null;
        $id_rdv = $args['id_rdv'] ?? null;

        $body = $request->getParsedBody();
        $statut = $body['statut'] ?? null;

        try {
            $this->serviceRdv->honorerRDV($id_prat, $id_rdv, $statut);
            $response->getBody()->write(json_encode([
                "success" => true,
                "message" => "statut du RDV mis a jour avec succes"
                ]));
            return $response->withHeader("Content-Type", "application/json");
        } catch (\Throwable $t) {
            throw new Exception($t->getMessage());
        }
    }
}