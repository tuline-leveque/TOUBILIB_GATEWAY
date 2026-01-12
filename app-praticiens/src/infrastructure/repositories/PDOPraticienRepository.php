<?php

namespace praticiens\infra\repositories;

use Exception;
use PDO;
use Slim\Exception\HttpInternalServerErrorException;
use praticiens\core\domain\entities\praticien\Praticien;
use praticiens\core\exceptions\EntityNotFoundException;
use praticiens\infra\repositories\interface\PraticienRepositoryInterface;

class PDOPraticienRepository implements PraticienRepositoryInterface {


    private PDO $prati_pdo;

    public function __construct(PDO $prati_pdo) {
        $this->prati_pdo = $prati_pdo;
    }

    public function getPraticiens(?string $specialite = null, ?string $ville = null) : array {
        try {
            $sql = "SELECT praticien.id, nom, prenom, ville, email, specialite.libelle as specialite, telephone, email FROM praticien
                                          INNER JOIN specialite ON praticien.specialite_id = specialite.id";

            $conditions = [];
            $params = [];

            if ($specialite !== null) {
                $conditions[] = "specialite.libelle = :specialite";
                $params[':specialite'] = $specialite;
            }

            if ($ville !== null) {
                $conditions[] = "praticien.ville = :ville";
                $params[':ville'] = $ville;
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $stmt = $this->prati_pdo->prepare($sql);
            $stmt->execute($params);
            $array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (HttpInternalServerErrorException $e) {
            //500
            throw new Exception("Erreur lors de l'execution de la requete SQL.");
        } catch(\Throwable $e) {
            throw new Exception("Erreur lors de la reception des praticiens.");
        }
        $res = [];
        foreach ($array as $praticien) {
            $res[] = new Praticien(
                id: $praticien['id'],
                nom: $praticien['nom'],
                prenom: $praticien['prenom'],
                ville: $praticien['ville'],
                email: $praticien['email'],
                telephone: $praticien['telephone'],
                specialite: $praticien['specialite']
            );
        }
        return $res;
    }

    /**
     * @throws EntityNotFoundException
     * @throws Exception
     */
    public function getPraticien($id): Praticien {
        try {
            $query = $this->prati_pdo->query("SELECT praticien.id, praticien.nom, praticien.ville, praticien.email, praticien.prenom, specialite.libelle as specialite, praticien.email, praticien.telephone FROM praticien
                                          INNER JOIN specialite ON praticien.specialite_id = specialite.id
                                          WHERE praticien.id = '$id'");
        } catch (HttpInternalServerErrorException $e) {
            //500
            throw new Exception("Erreur lors de l'execution de la requete SQL.");
        } catch(\Throwable $e) {
            throw new Exception("Erreur lors de la reception du praticien.");
        }

        $array = $query->fetch(PDO::FETCH_ASSOC);
        if(!$array) {
            throw new EntityNotFoundException("praticien introuvable.", "praticien");
        }

        try {
            $array['moyens_paiement'] = $this->getMoyenPaiement($id);
        } catch(\Exception $e) {
            throw new Exception("Erreur lors de la reception du moyen de paiement.", 500);
        }

        try {
            $array['motifs_visite'] = $this->getMotifsVisite($id);
        } catch(\Exception $e) {
            throw new Exception("Erreur lors de la reception des motifs de visite.", 500);
        }

        return new Praticien(
            id: $array['id'],
            nom: $array['nom'],
            prenom: $array['prenom'],
            ville: $array['ville'],
            email: $array['email'],
            telephone: $array['telephone'],
            specialite: $array['specialite'],
            moyens_paiement: $array['moyens_paiement'],
            motifs_visite: $array['motifs_visite']
        );
    }

    /**
     * @throws Exception
     */
    private function getMotifsVisite($id) : array {
        try {
            $motifs_visite = $this->prati_pdo->query("SELECT motif_visite.libelle as motif_visite FROM motif_visite
                                           INNER JOIN praticien2motif ON motif_visite.id = praticien2motif.motif_id
                                           WHERE praticien2motif.praticien_id = '$id'");
        } catch(\Throwable $e) {
            throw new Exception("Erreur lors de la reception des motifs de visite.");
        }

        $res = $motifs_visite->fetchAll(PDO::FETCH_ASSOC);
        return array_column($res, "motif_visite");
    }

    private function getMoyenPaiement($id) : array {
        try {
            $moyens_paiement = $this->prati_pdo->query("SELECT moyen_paiement.libelle as moyen_paiement FROM moyen_paiement
                                           INNER JOIN praticien2moyen ON moyen_paiement.id = praticien2moyen.moyen_id
                                           WHERE praticien2moyen.praticien_id = '$id'");
        } catch(\Throwable $e) {
            throw new Exception("Erreur lors de la reception du moyen de paiement.");
        }

        $res = $moyens_paiement->fetchAll(PDO::FETCH_ASSOC);
        return array_column($res, "moyen_paiement");
    }

    public function addIndisponibilite(string $id_prat, string $date_debut, string $date_fin) {
        try {
            $this->prati_pdo->query("INSERT INTO indisponibilite(praticien_id, date_heure_debut, date_heure_fin) VALUES ('$id_prat', '$date_debut', '$date_fin')");
        } catch(\Throwable $e) {
            throw new Exception("Erreur lors de l'ajout d'une nouvelle indisponibilite du praticien. message : " . $e->getMessage());
        }
    }

    public function getIndisponibilite(string $id_prat) : array {
        try {
            $indisponibilites = $this->prati_pdo->query("SELECT * FROM indisponibilite WHERE praticien_id = '$id_prat'");

        } catch (\Throwable $e) {
            throw new Exception("Erreur lors de la recuperation des indisponibilites du praticien.");
        }
        $res = $indisponibilites->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
}