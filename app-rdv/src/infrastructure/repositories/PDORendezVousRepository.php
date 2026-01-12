<?php

namespace rdvs\infra\repositories;


use DateTime;
use PDO;
use Slim\Exception\HttpInternalServerErrorException;
use rdvs\core\domain\entities\rdv\RendezVous;
use rdvs\core\exceptions\CreneauException;
use rdvs\core\exceptions\EntityNotFoundException;
use rdvs\core\exceptions\InternalErrorException;
use rdvs\infra\repositories\interface\RendezVousRepositoryInterface;

class PDORendezVousRepository implements RendezVousRepositoryInterface {


    private PDO $rdv_pdo;

    public function __construct(PDO $rdv_pdo) {
        $this->rdv_pdo = $rdv_pdo;
    }

    public function getCreneauxOccupes(int $role, string $debut, string $fin, string $id): array {
        // FORMAT DATE : YYYY-MM-DD
        if (!$this->estDateValide($debut)) {
            throw new CreneauException("Le format de la date de debut est invalide.");
        }

        if ($this->estDateValide($fin)) {
            if (strlen($fin) <= 10) {
                $fin = $fin . " 23:59:59";
            }
        } else {
            throw new CreneauException("Le format de la date de fin est invalide.");
        }

        try {
            $col = ($role === 0) ? 'praticien_id' : 'patient_id';

            $stmt = $this->rdv_pdo->prepare("SELECT * FROM rdv WHERE date_heure_debut < :fin AND date_heure_fin > :debut AND $col = :id");

            $stmt->execute([
                ':debut' => $debut,
                ':fin'   => $fin,
                ':id'    => $id
            ]);

            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(\Throwable) {
            throw new InternalErrorException("Erreur lors de la reception des rendez-vous.");
        }
        $res = [];
        foreach ($array as $rdv) {
            $res[] = new RendezVous(
                id: $rdv['id'],
                praticien_id: $rdv['praticien_id'],
                patient_id: $rdv['patient_id'],
                status: (int)$rdv['status'],
                duree: (int)$rdv['duree'],
                date_heure_fin: $rdv['date_heure_fin'],
                motif_visite: $rdv['motif_visite'],
                date_heure_debut: $rdv['date_heure_debut'],
                patient_email: $rdv['patient_email'],
                date_creation: $rdv['date_creation']
            );
        }
        return $res;
    }

    /**
     * @throws EntityNotFoundException
     * @throws \Exception
     */
    public function getRDV(int $role, string $id, string $id_rdv): RendezVous {
        try {
            $col = ($role === 0) ? 'praticien_id' : 'patient_id';

            // Requête préparée sécurisée
            $stmt = $this->rdv_pdo->prepare("SELECT * FROM rdv WHERE id = :id_rdv AND $col = :id_user");

            $stmt->execute([
                ':id_rdv' => $id_rdv,
                ':id_user' => $id
            ]);

            $rdv = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (HttpInternalServerErrorException) {
            //500
            throw new \Exception("Erreur lors de l'execution de la requete SQL.");
        } catch(\Throwable) {
            throw new \Exception("Erreur lors de la reception du rendez-vous.");
        }

        if (!$rdv) {
            throw new EntityNotFoundException("Le rendez-vous ayant pour id ".$id_rdv." avec le praticien ayant pour id ".$id." n'existe pas.", "RDV");
        } else {
            return new RendezVous(
                id: $rdv['id'],
                praticien_id: $rdv['praticien_id'],
                patient_id: $rdv['patient_id'],
                status: (int) $rdv['status'],
                duree: (int) $rdv['duree'],
                date_heure_fin: $rdv['date_heure_fin'],
                motif_visite: $rdv['motif_visite'],
                date_heure_debut: $rdv['date_heure_debut'],
                patient_email: $rdv['patient_email'],
                date_creation: $rdv['date_creation']
            );
        }
    }

    private function estDateValide($date) {
        // Essayer le format complet avec heure
        $formats = ['Y-m-d H:i:s', 'Y-m-d'];

        foreach ($formats as $format) {
            $d = DateTime::createFromFormat($format, $date);

            // Vérifie que la date a bien été créée, et qu'elle correspond exactement au format
            if ($d && $d->format($format) === $date) {
                return true;
            }
        }
        return false;
    }

    /**
     * @throws InternalErrorException
     */
    public function createRdv($dto) : void
    {
        try {
            $id = \Ramsey\Uuid\Uuid::uuid4();
            $this->rdv_pdo->query("INSERT INTO rdv (id, patient_id, praticien_id, date_heure_debut, date_heure_fin, duree, motif_visite)
                      VALUES ('$id', '$dto->patient_id', '$dto->praticien_id', '$dto->date_heure_debut', '$dto->date_heure_fin',
                      '$dto->duree', '$dto->motif_visite')");
        } catch(\Throwable $e) {
            throw new InternalErrorException("Erreur lors de la création du rendez-vous.");
        }
        
    }

    public function annulerRdv($id_rdv) {
        try {
            $this->rdv_pdo->query("UPDATE rdv SET status = 1 WHERE id = '$id_rdv'");
        } catch (HttpInternalServerErrorException) {
            //500
            throw new HttpInternalServerErrorException("Erreur lors de l'execution de la requete SQL.");
        } catch(\Throwable) {
            throw new Exception("Erreur lors de l'annulation du rendez-vous.");
        }

    }

    /**
     * @throws InternalErrorException
     */
    public function honorerRDV(string $id_prat, string $id_rdv, bool $statut): void {
        try {
            if($statut) {
                $this->rdv_pdo->query("UPDATE rdv SET status = 2 WHERE id = '$id_rdv' AND praticien_id = '$id_prat'");
            } else {
                $this->rdv_pdo->query("UPDATE rdv SET status = -1 WHERE id = '$id_rdv' AND praticien_id = '$id_prat'");
            }
        } catch (\Throwable) {
            throw new InternalErrorException("Erreur lors de l'execution de la requete SQL. STatut : " . $statut);
        }
    }
}