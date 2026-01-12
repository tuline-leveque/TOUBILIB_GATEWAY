<?php

namespace toubilib\infra\repositories\interface;

use toubilib\core\domain\entities\rdv\RendezVous;

interface RendezVousRepositoryInterface {
    public function getCreneauxOccupes(int $role, string $debut, string $fin, string $id) : array;
    //role 0 = praticien
    //role 1 = patient
    public function getRDV(int $role, string $id, string $id_rdv) : RendezVous;
    //role 0 = praticien
    //role 1 = patient
    public function createRdv($dto) : void;
    public function annulerRdv($id_rdv);

    public function honorerRDV(string $id_prat, string $id_rdv, bool $statut);
}