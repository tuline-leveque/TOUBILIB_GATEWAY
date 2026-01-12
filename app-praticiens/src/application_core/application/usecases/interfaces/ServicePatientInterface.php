<?php

namespace toubilib\core\application\usecases\interfaces;

use toubilib\api\dtos\PatientDTO;

interface ServicePatientInterface {
    public function getPatient(string $id): PatientDTO;
}