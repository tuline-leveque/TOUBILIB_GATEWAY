<?php

namespace auth\api\dtos;

class RendezVousDTO {
    public function __construct(
        public readonly string $id,
        public readonly string $praticien_id,
        public readonly string $patient_id,
        public readonly int    $status,
        public readonly int    $duree,
        public readonly string $date_heure_fin,
        public readonly string $motif_visite,
        public readonly ?string $date_heure_debut = null,
        public readonly ?string $patient_email = null,
        public readonly ?string $date_creation = null,
        public ?array $links = null,

    ) {}
}