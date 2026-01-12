<?php

namespace rdvs\core\application\usecases\interfaces;


use rdvs\api\dtos\InputRendezVousDTO;
use rdvs\api\dtos\RendezVousDTO;

interface ServiceRendezVousInterface {
    public function listerRDV(int $role, string $id, ?string $debut = null, ?string $fin = null): array;
    //role 0 = praticien
    //role 1 = patient
    public function getRDV(string $id_prat,string $id_rdv): RendezVousDTO;
    public function creerRendezVous(InputRendezVousDTO $dto): array;
    public function annulerRendezVous($id_prat, $id_rdv): array;

    public function honorerRDV(string $id_prat, string $id_rdv, bool $statut);
}