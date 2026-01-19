<?php

namespace rdvs\core\application\usecases\interfaces;

use rdvs\api\dtos\PraticienDTO;

interface ServicePraticienInterface
{
    public function listerPraticiens(?string $specialite = null, ?string $ville = null): array;
    public function getPraticien(string $id);
    public function addIndisponibilite(string $id_prat, string $date_debut, string $date_fin);
    public function getIndisponibilite(string $id_prat);
}