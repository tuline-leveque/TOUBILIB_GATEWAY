<?php

namespace rdvs\infra\adapters;

use Exception;
use Psr\Http\Client\ClientInterface;
use rdvs\api\dtos\PraticienDTO;
use rdvs\core\application\usecases\interfaces\ServicePraticienInterface;

use GuzzleHttp\Exception\ClientException;

class ServicePraticienAdaptor implements ServicePraticienInterface {

    /**
     * @throws Exception
     */
    private ClientInterface $remote_praticien_service;

    public function __construct(ClientInterface $client) {
        $this->remote_praticien_service = $client;
    }
    public function listerPraticiens(?string $specialite = null, ?string $ville = null): array {
        $path = 'praticiens';
        $method = 'GET';
        try {
            $response = $this->remote_praticien_service->request(
                $method,
                $path
            );
        } catch (ClientException $e) {
            throw new Exception();
        }

        return $response->getBody()->getContents();
    }

    /**
     * @throws \Exception
     */
    public function getPraticien(string $id) {
        $path = 'praticiens/'.$id;
        $method = 'GET';
        try {
            $response = $this->remote_praticien_service->request(
                $method,
                $path
            );
        } catch (ClientException $e) {
            throw new Exception();
        }

        return json_decode($response->getBody()->getContents());
    }

    public function addIndisponibilite(string $id_prat, string $date_debut, string $date_fin) {
        $path = 'praticiens/'.$id_prat.'/indisponibilites';
        $method = 'GET';
        try {
            $response = $this->remote_praticien_service->request(
                $method,
                $path,
                [
                    'json' => [
                        'date_debut' => $date_debut,
                        'date_fin' => $date_fin,
                    ]
                ]
            );
        } catch (ClientException $e) {
            throw new Exception("Erreur lors de l'execution de la requete, message : " . $e->getMessage() );
        }

        if($response->getStatusCode() === 200) { return true; } else { return false; }
    }

    public function getIndisponibilite(string $id_prat) : array {
        $path = 'praticiens/'.$id_prat.'/indisponibilites';
        $method = 'GET';
        try {
            $response = $this->remote_praticien_service->request(
                $method,
                $path
            );
        } catch (ClientException $e) {
            throw new Exception();
        }

        return json_decode($response->getBody()->getContents());
    }
}