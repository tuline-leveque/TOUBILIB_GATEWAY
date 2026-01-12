<?php

namespace praticiens\api\dtos;

class IndisponibiliteDTO {
    public function __construct(
        public readonly string $date_debut,
        public readonly string $date_fin,
    ) {}
}