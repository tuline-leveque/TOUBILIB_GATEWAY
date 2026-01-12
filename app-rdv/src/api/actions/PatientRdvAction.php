<?php

namespace toubilib\api\actions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\application\usecases\interfaces\ServiceRendezVousInterface;

class PatientRdvAction {
    private ServiceRendezVousInterface $serviceRdv;

    public function __construct(ServiceRendezVousInterface $serviceRdv) {
        $this->serviceRdv = $serviceRdv;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id_pat = $args['id_pat'] ?? null;
        if(empty($id_pat)) {
            throw new \Exception("Saisissez un id de patient");
        }

        $queryParams = $request->getQueryParams();
        $date_debut = $queryParams['date_debut'] ?? null;
        $date_fin = $queryParams['date_fin'] ?? null;

        try {
            $res = $this->serviceRdv->listerRDV(1,$id_pat, $date_debut, $date_fin);
            foreach ($res as $rdvs) {
                $rdvs->links = [
                    'detail' => [
                        "href" => "/patients/" . $rdvs->patient_id . "/rdvs/" . $rdvs->id
                    ]
                ];
            }
            $response->getBody()->write(json_encode($res));
            return $response->withHeader("Content-Type", "application/json");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}