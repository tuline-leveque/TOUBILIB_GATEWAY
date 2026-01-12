<?php

namespace praticiens\core\application\usecases;
use Exception;
use praticiens\api\dtos\IndisponibiliteDTO;
use praticiens\api\dtos\PraticienDTO;
use praticiens\core\application\usecases\interfaces\ServicePraticienInterface;
use praticiens\core\exceptions\EntityNotFoundException;
use praticiens\infra\repositories\interface\PraticienRepositoryInterface;

class ServicePraticien implements ServicePraticienInterface {
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

    /**
     * @throws Exception
     */
    public function listerPraticiens(?string $specialite = null, ?string $ville = null): array {
        try {
            $praticiens = $this->praticienRepository->getPraticiens($specialite, $ville);
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de l'obtention des praticiens\n Message erreur PDO : " . $e->getMessage());
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