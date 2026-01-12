<?php

namespace toubilib\api\actions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\application\usecases\interfaces\ServicePraticienInterface;

class PraticiensAction {
    private ServicePraticienInterface $servicePraticien;

    public function __construct(ServicePraticienInterface $servicePraticien) {
        $this->servicePraticien = $servicePraticien;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        try {
            $query = $request->getQueryParams();
            $specialite = $query['specialite'] ?? null;
            $ville = $query['ville'] ?? null;

            $res = $this->servicePraticien->listerPraticiens($specialite, $ville);
            foreach ($res as $praticien) {
                $praticien->links = [
                    'detail' => [
                        "href" => "/praticiens/" . $praticien->id
                    ]
                ];
            }
            $response->getBody()->write(json_encode($res));
            return $response->withHeader("Content-Type", "application/json");
        } catch (\Exception) {
            throw new \Exception("Erreur lors de la récupération de la liste des praticiens.");
        }
    }
}