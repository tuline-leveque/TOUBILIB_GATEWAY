<?php

namespace rdvs\core\application\usecases\interfaces;

interface ServicePatientInterface {
    public function getPatient(string $id): string;
}