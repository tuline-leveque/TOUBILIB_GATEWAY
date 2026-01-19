<?php

namespace rdvs\api\dtos;


use Exception;

class PraticienDTO {
    public function __construct(
        public readonly string $id,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $ville,
        public readonly string $email,
        public readonly string $telephone,
        public readonly string $specialite,
        public readonly ?array $moyens_paiement = null,
        public readonly ?array $motifs_visite = null,
        public ?array $links = null
    ) {}

    /**
     * @throws Exception
     */
    public function __get(string $property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new Exception("La propriété '$property' n'existe pas.");
    }
}