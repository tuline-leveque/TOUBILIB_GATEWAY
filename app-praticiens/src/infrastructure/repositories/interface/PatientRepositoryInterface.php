<?php

namespace praticiens\infra\repositories\interface;

use praticiens\core\domain\entities\patient\Patient;

interface PatientRepositoryInterface {
    public function getPatient(string $id) : Patient;
}