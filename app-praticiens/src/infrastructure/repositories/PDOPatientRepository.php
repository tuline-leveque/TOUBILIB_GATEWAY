<?php

namespace praticiens\infra\repositories;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use PDO;
use Slim\Exception\HttpInternalServerErrorException;
use praticiens\core\domain\entities\patient\Patient;
use praticiens\core\exceptions\EntityNotFoundException;
use praticiens\infra\repositories\interface\PatientRepositoryInterface;

class PDOPatientRepository implements PatientRepositoryInterface {


    private PDO $patient_pdo;

    public function __construct(PDO $patient_pdo) {
        $this->patient_pdo = $patient_pdo;
    }

    /**
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function getPatient(string $id): Patient {
        try {
            $query = $this->patient_pdo->query("SELECT id, nom, prenom, date_naissance, adresse, code_postal, ville, email, telephone FROM patient WHERE id = '$id'");
            $res = $query->fetch(PDO::FETCH_ASSOC);
        } catch (HttpInternalServerErrorException) {
            //500
            throw new Exception("Erreur lors de l'execution de la requete SQL.", StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        } catch(\Throwable) {
            throw new Exception("Erreur lors de la reception du patient.");
        }

        if (!$res) {
            //404
            throw new EntityNotFoundException("Le patient ayant pour id ".$id." n'existe pas.", "patient");
        }

        return new Patient(
            id: $res['id'],
            nom: $res['nom'],
            prenom: $res['prenom'],
            telephone: $res['telephone'],
            date_naissance: $res['date_naissance'],
            adresse: $res['adresse'],
            code_postal: $res['code_postal'],
            ville: $res['ville'],
            email: $res['email']
        );
    }
}