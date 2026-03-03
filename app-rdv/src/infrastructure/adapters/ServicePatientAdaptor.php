<?php

namespace rdvs\infra\adapters;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Client\ClientInterface;
use rdvs\core\application\usecases\interfaces\ServicePatientInterface;
use rdvs\core\exceptions\EntityNotFoundException;

class ServicePatientAdaptor implements ServicePatientInterface {
    private ClientInterface $remote_patient_service;

    public function __construct(ClientInterface $client)
    {
        $this->remote_patient_service = $client;
    }

    /**
     * @throws Exception
     */
    public function getPatient(string $id): string
    {
        $path = 'patients/'.$id;
        $method = 'GET';
        try {
            $response = $this->remote_patient_service->request(
                $method,
                $path
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new EntityNotFoundException("Patient $id introuvable", "Patient");
            }
            throw new Exception("Erreur client lors de la récupération du patient", $e->getCode());
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), 500);
        }

        return $response->getBody()->getContents();
    }
}