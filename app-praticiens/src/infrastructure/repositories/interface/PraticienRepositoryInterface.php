<?php

namespace toubilib\infra\repositories\interface;

use toubilib\core\domain\entities\praticien\Praticien;

interface PraticienRepositoryInterface {
    public function getPraticiens(?string $specialite = null, ?string $ville = null) : array;
    public function getPraticien($id): Praticien;
    public function addIndisponibilite(string $id_prat, string $date_debut, string $date_fin);
    public function getIndisponibilite(string $id_prat) : array;
}