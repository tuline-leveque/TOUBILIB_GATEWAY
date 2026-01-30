<?php

namespace praticiens\api\actions;
use praticiens\core\application\usecases\interfaces\ServicePatientInterface;
use praticiens\core\exceptions\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PatientAction {
    private ServicePatientInterface $servicePatient;

    public function __construct(ServicePatientInterface $servicePatient) {
        $this->servicePatient = $servicePatient;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $id_pat = $args['id_pat'] ?? null;
        if(empty($id_pat)) {
            throw new \Exception("Saisissez un id de patient");
        }

        try {
            $patient = $this->servicePatient->getPatient($id_pat);
            $response->getBody()->write(json_encode($patient));

            return $response->withHeader("Content-Type", "application/json");
        } catch (EntityNotFoundException $e) {
            $response->getBody()->write(json_encode([
                "message" => $e->getMessage(),
                "exception" => [["type" => "EntityNotFoundException", "code" => 404]]
            ]));
            return $response
                ->withStatus(404) //
                ->withHeader("Content-Type", "application/json");

        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(["message" => $e->getMessage()]));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    }
}