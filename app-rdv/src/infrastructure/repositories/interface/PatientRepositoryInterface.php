<?php

namespace toubilib\infra\repositories\interface;

use toubilib\core\domain\entities\patient\Patient;

interface PatientRepositoryInterface {
    public function getPatient(string $id) : Patient;
}