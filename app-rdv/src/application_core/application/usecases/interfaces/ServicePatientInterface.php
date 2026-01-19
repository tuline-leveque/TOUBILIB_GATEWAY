<?php

namespace rdvs\core\application\usecases\interfaces;

use rdvs\api\dtos\PatientDTO;

interface ServicePatientInterface {
    public function getPatient(string $id): PatientDTO;
}