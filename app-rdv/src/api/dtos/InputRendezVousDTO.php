<?php

namespace toubilib\api\dtos;


use Exception;

class InputRendezVousDTO {
    private string $patient_id;
    private string $praticien_id;
    private string $date_heure_debut;
    private string $date_heure_fin;
    private int $duree;
    private string $motif_visite;

    public function __construct($data) {
        $this->duree = $data['duree'];
        $this->motif_visite = $data['motif'];
        $this->patient_id = $data['id_pat'];
        $this->praticien_id = $data['id_prat'];
        $this->date_heure_debut = $data['date_heure_debut'];
        $this->date_heure_fin = $data['date_heure_fin'];
    }

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