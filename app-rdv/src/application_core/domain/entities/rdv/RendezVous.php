<?php

namespace rdvs\core\domain\entities\rdv;

use Exception;

class RendezVous {
    private string $id;
    private string $praticien_id;
    private string $patient_id;
    private ?string $patient_email;
    private string $date_heure_debut;
    private ?string $date_heure_fin;
    private string $motif_visite;
    private ?string $date_creation;
    private int $duree;
    private int $status;

    public function  __construct(
        string $id,
        string $praticien_id,
        string $patient_id,
        int $status,
        int $duree,
        string $date_heure_fin,
        string $motif_visite,
        ?string $date_heure_debut = null,
        ?string $patient_email = null,
        ?string $date_creation = null
    ){
        $this->id = $id;
        $this->praticien_id = $praticien_id;
        $this->patient_email = $patient_email;
        $this->patient_id = $patient_id;
        $this->date_heure_debut = $date_heure_debut;
        $this->status = $status;
        $this->duree = $duree;
        $this->date_heure_fin = $date_heure_fin;
        $this->date_creation = $date_creation;
        $this->motif_visite = $motif_visite;
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

    /**
     * @throws Exception
     */
    public function annuler(){
        $dateFin = \Datetime::createFromFormat('Y-m-d H:i:s', $this->date_heure_debut);
        if($dateFin < new \DateTime()) {
            throw new Exception("Impossible d'annuler un rendez-vous deje passe.");
        }

        $this->status = 2;
    }
}