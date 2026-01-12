<?php

namespace praticiens\core\application\usecases\interfaces;

use praticiens\api\dtos\PatientDTO;

interface ServicePatientInterface {
    public function getPatient(string $id): PatientDTO;
}