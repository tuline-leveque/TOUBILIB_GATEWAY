<?php

namespace toubilib\api\actions;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\application\usecases\interfaces\ServicePraticienInterface;
use toubilib\core\exceptions\BadRequestException;

class AjouterIndisponibiliteAction {
        public function __construct(
            private ServicePraticienInterface $servicePraticien,
        ){}

    /**
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args) {
            $id_prat = $args['id_prat'] ?? null;

            $body = $request->getParsedBody();
            $date_debut = $body['date_debut'] ?? null;
            $date_fin = $body['date_fin'] ?? null;

            if($id_prat == null || $date_debut == null || $date_fin == null) {
                throw new BadRequestException("Erreur requete");
            }

            try {
                $this->servicePraticien->addIndisponibilite($id_prat, $date_debut, $date_fin);
                $response->getBody()->write(json_encode([
                    "success" => true,
                ]));
                return $response->withHeader("Content-Type", "application/json");
            } catch (\Throwable $th) {
                throw new Exception($th->getMessage());
            }
        }
}