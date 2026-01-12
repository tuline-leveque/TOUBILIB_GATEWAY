<?php

namespace toubilib\infra\adapters;

use Exception;
use Psr\Http\Client\ClientInterface;
use Slim\Exception\HttpNotFoundException;
use toubilib\api\dtos\IndisponibiliteDTO;
use toubilib\api\dtos\PraticienDTO;
use toubilib\core\application\usecases\interfaces\ServicePraticienInterface;
use toubilib\core\exceptions\EntityNotFoundException;

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
            $praticiens = $this->remote_praticien_service->request(
                $method,
                $path
            );
        } catch (ClientException $e) {
            throw new Exception();
        }

        $res = [];
        foreach ($praticiens as $praticien) {
            $res[] = new PraticienDTO(
                id: $praticien->id,
                nom: $praticien->nom,
                prenom: $praticien->prenom,
                ville: $praticien->ville,
                email: $praticien->email,
                telephone: $praticien->telephone,
                specialite: $praticien->specialite
            );
        }
        return $res;
    }

    /**
     * @throws \Exception
     */
    public function getPraticien(string $id): PraticienDTO {
        try {
            $praticien = $this->praticienRepository->getPraticien($id);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getMessage(), $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception("probleme lors de la reception du praticien.", $e->getCode());
        }
        return new PraticienDTO(
            id: $praticien->id,
            nom: $praticien->nom,
            prenom: $praticien->prenom,
            ville: $praticien->ville,
            email: $praticien->email,
            telephone: $praticien->telephone,
            specialite: $praticien->specialite,
            moyens_paiement: $praticien->moyens_paiement,
            motifs_visite: $praticien->motifs_visite,
        );
    }

    public function addIndisponibilite(string $id_prat, string $date_debut, string $date_fin) {
        try {
            $this->praticienRepository->addIndisponibilite($id_prat, $date_debut, $date_fin);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getIndisponibilite(string $id_prat) : array {
        try {
            $indisponibilites = $this->praticienRepository->getIndisponibilite($id_prat);
            $res = [];

            foreach ($indisponibilites as $indisponibilite) {
                $res[] = new IndisponibiliteDTO($indisponibilite["date_heure_debut"], $indisponibilite["date_heure_fin"]);
            }
            return $res;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}