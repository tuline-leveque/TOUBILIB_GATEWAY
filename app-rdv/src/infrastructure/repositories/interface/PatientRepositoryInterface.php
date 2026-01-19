<?php

namespace rdvs\infra\repositories\interface;

use rdvs\core\domain\entities\patient\Patient;
// TODO : REMOVE IT
interface PatientRepositoryInterface {
    public function getPatient(string $id) : Patient;
}