<?php

namespace toubilib\api\actions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use toubilib\core\application\usecases\interfaces\ServicePraticienInterface;
use toubilib\core\application\usecases\ServicePraticien;

class PraticienAction {
    private ServicePraticienInterface $servicePraticien;

    public function __construct(ServicePraticienInterface $servicePraticien) {
        $this->servicePraticien = $servicePraticien;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id_prat = $args['id_prat'] ?? null;
        if(empty($id_prat)) {
            throw new \Exception("Saisissez un id de praticien");
        }

        try {
            $praticien = $this->servicePraticien->getPraticien($id_prat);
            $praticien->links = [
                "self" => [
                    "href" => "/praticiens/" . $praticien->id
                ],
                "rdvs" => [
                    "href" => "/praticiens/" . $praticien->id . "/rdvs"
                ]
            ];
            $response->getBody()->write(json_encode($praticien));

            return $response->withHeader("Content-Type", "application/json");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}