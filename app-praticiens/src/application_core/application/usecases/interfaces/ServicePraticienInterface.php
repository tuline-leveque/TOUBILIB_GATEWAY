<?php

namespace praticiens\core\application\usecases\interfaces;

use praticiens\api\dtos\PraticienDTO;

interface ServicePraticienInterface
{
    public function listerPraticiens(?string $specialite = null, ?string $ville = null): array;
    public function getPraticien(string $id): PraticienDTO;
    public function addIndisponibilite(string $id_prat, string $date_debut, string $date_fin);

    public function getIndisponibilite(string $id_prat);
}