<?php

namespace rdvs\core\application\usecases;

use DateTime;
use Exception;
use rdvs\api\dtos\RendezVousDTO;
use rdvs\core\application\usecases\interfaces\ServiceMailerInterface;
use rdvs\core\application\usecases\interfaces\ServicePatientInterface;
use rdvs\core\application\usecases\interfaces\ServicePraticienInterface;
use rdvs\core\application\usecases\interfaces\ServiceRendezVousInterface;
use rdvs\api\dtos\InputRendezVousDTO;
use rdvs\core\exceptions\CreneauException;
use rdvs\core\exceptions\EntityNotFoundException;
use rdvs\core\exceptions\MailException;
use rdvs\infra\repositories\interface\RendezVousRepositoryInterface;

class ServiceRendezVous implements ServiceRendezVousInterface
{
    private RendezVousRepositoryInterface $rendezVousRepository;
    private ServicePraticienInterface $servicePraticien;
    private ServicePatientInterface $servicePatient;
    private ServiceMailerInterface $mailer;

    public function __construct(RendezVousRepositoryInterface $rendezVousRepository, ServicePraticienInterface $servicePraticien, ServicePatientInterface $servicePatient, ServiceMailerInterface $mailer)
    {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->servicePraticien = $servicePraticien;
        $this->servicePatient = $servicePatient;
        $this->mailer = $mailer;
    }

    /**
     * @throws Exception
     * @throws CreneauException
     */
    public function listerRDV(int $role, string $id, ?string $debut = null, ?string $fin = null): array {
        if (empty($debut)) {
            $debut = (new \DateTime())->setDate(1900,01,01)->setTime(8, 0)->format('Y-m-d H:i:s');
        }
        if (empty($fin)) {
            $fin = (new \DateTime())->setDate(9999,01,01)->setTime(19,0)->format('Y-m-d H:i:s');
        }

        try {
            $rdvs = $this->rendezVousRepository->getCreneauxOccupes($role, $debut, $fin, $id);
        } catch (CreneauException $e) {
            throw new CreneauException($e->getMessage());
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
        $res = [];
        foreach ($rdvs as $rdv) {
            $res[] = new RendezVousDTO(
                id: $rdv->id,
                praticien_id: $rdv->praticien_id,
                patient_id: $rdv->patient_id,
                status: $rdv->status,
                duree: $rdv->duree,
                date_heure_fin: $rdv->date_heure_fin,
                motif_visite: $rdv->motif_visite,
                date_heure_debut: $rdv->date_heure_debut,
                patient_email: $rdv->patient_email,
                date_creation: $rdv->date_creation
            );
        }
        return $res;
    }

    /**
     * @throws Exception
     * @throws EntityNotFoundException
     */
    public function getRDV(string $id_prat, string $id_rdv): RendezVousDTO {
        try {
            $rdv = $this->rendezVousRepository->getRDV(0, $id_prat, $id_rdv);
            return new RendezVousDTO(
                id: $rdv->id,
                praticien_id: $rdv->praticien_id,
                patient_id: $rdv->patient_id,
                status: $rdv->status,
                duree: $rdv->duree,
                date_heure_fin: $rdv->date_heure_fin,
                motif_visite: $rdv->motif_visite,
                date_heure_debut: $rdv->date_heure_debut,
                patient_email: $rdv->patient_email,
                date_creation: $rdv->date_creation
            );
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getMessage(), $e->getEntity());
        } catch (\Exception $th) {
            throw new Exception("Erreur ".$th->getCode().": probleme lors de la reception du rendez-vous.");
        }
    }

    /**
     * @throws Exception
     * @throws EntityNotFoundException
     */
    public function creerRendezVous(InputRendezVousDTO $dto): array {
        try {
            $patient = $this->servicePatient->getPatient($dto->patient_id);
            $prat = $this->servicePraticien->getPraticien($dto->praticien_id);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." introuvable", $e->getEntity());
        } catch (Exception $e) {
            throw new Exception("Erreur Serveur", $e->getCode());
        }

        //vérification si le motif de visite existe
        if (!(in_array($dto->motif_visite, $prat->motifs_visite))) {
            throw new EntityNotFoundException("motif visite praticien introuvable", "motif visite praticien");
        }

        try {
            //vérification si le créneau est disponible
            $date_heure_debut = DateTime::createFromFormat('Y-m-d H:i:s', $dto->date_heure_debut);
            $date_heure_fin = DateTime::createFromFormat('Y-m-d H:i:s', $dto->date_heure_fin);
        } catch (\Throwable $th) {
            throw new Exception("Erreur format date");
        }

        $heureDebut = (int)$date_heure_debut->format('H');
        $minuteDebut = (int)$date_heure_debut->format('i');
        $heureFin = (int)$date_heure_fin->format('H');
        $minuteFin = (int)$date_heure_fin->format('i');

        if (!(($heureDebut >= 8) && ($heureFin < 19 || ($heureFin === 19 && $minuteFin === 0)))) {
            //entre 8h et 19h
            throw new CreneauException("Les horaires doivent etre compris entre 8h et 19h.");
        }


        //horaire debut < horaire fin
        if (($heureDebut > $heureFin) || (($heureDebut === $heureFin) && ($minuteFin <= $minuteDebut))) {
            throw new CreneauException("Les horaires de fin du rdv ne peuvent etre avant les horaires de debut.");
        }

        $indisponibilites = $this->servicePraticien->getIndisponibilite($dto->praticien_id);
        foreach ($indisponibilites as $indisponibilite) {
            $indisponibilite->date_debut = DateTime::createFromFormat('Y-m-d H:i:s', $indisponibilite->date_debut);
            $indisponibilite->date_fin = DateTime::createFromFormat('Y-m-d H:i:s', $indisponibilite->date_fin);
            if (
                ($date_heure_debut >= $indisponibilite->date_debut &&
                $date_heure_debut <= $indisponibilite->date_fin) ||
                ($date_heure_fin >= $indisponibilite->date_debut &&
                $date_heure_fin <= $indisponibilite->date_fin)
            ) {
                throw new CreneauException("Le praticien est indisponible durant cette periode");
            }
        }

        $nJourDebut = (int)$date_heure_debut->format('N');
        $nJourFin = (int)$date_heure_fin->format('N');
        //du lundi au venredi
        if (($nJourDebut > 5) || ($nJourFin > 5)) {
            throw new CreneauException("Le rendez-vous doit etre compris entre lundi et vendredi.");
        }

        //horaire debut = horaire fin
        if ($nJourFin !== $nJourDebut) {
            throw new CreneauException("Le jour de debut et le jour de fin doivent etre identiques");
        }

        try {
            //vérification praticien disponible
            $rdvs = $this->listerRDV(0, $dto->praticien_id, $dto->date_heure_debut, $dto->date_heure_fin);
        } catch (\Exception $e) {
            throw new Exception("Erreur liste des RDV");
        }

        if ($rdvs != []) {
            throw new CreneauException("Creneau déjà occupé");
        }


        try {
            $this->rendezVousRepository->createRdv($dto);
        } catch (\Exception $e) {
            throw new Exception("Erreur lors de la creation d'un rdv");
        }

        try {
            $dateRdv = $date_heure_debut->format('d/m/Y à H:i');

            $message_patient = "Confirmation de votre rendez-vous le $dateRdv avec le Dr. {$prat->nom}.";
            $this->mailer->send($message_patient, $patient->email, "patient", "CREATE");

            $message_prat = "Nouveau rendez-vous programmé le $dateRdv avec le patient {$patient->nom} {$patient->prenom}.";
            $this->mailer->send($message_prat, $prat->email, "praticien", "CREATE");
        } catch (Exception $e) {
            throw new MailException("Erreur lors de l'envoi de l'email de confirmation : " . $e->getMessage(), 503);
        }

        return [
            'success' => true,
            "message" => "RDV cree.",
        ];

    }

    /**
     * @throws Exception
     */
    public function annulerRendezVous($id_prat, $id_rdv): array {
        try {
            $rdv = $this->rendezVousRepository->getRDV(0, $id_prat, $id_rdv);
            $rdv->annuler();
        } catch (\Throwable $th) {
            throw new Exception("Erreur ".$th->getCode().": probleme lors de l'annulation du rendez-vous.");
        }

        try {
            $dateRdv = DateTime::createFromFormat('d/m/Y à H:i', $rdv->date_heure_debut);
            $patient = $this->servicePatient->getPatient($rdv->patient_id);
            $prat = $this->servicePraticien->getPraticien($rdv->praticien_id);

            $message_patient = "Votre rendez-vous du $dateRdv avec le Dr. {$prat->nom} est annulé.";
            $this->mailer->send($message_patient, $patient->email, "patient", "CREATE");

            $message_prat = "Rendez-vous du  $dateRdv avec le patient {$patient->nom} {$patient->prenom} annulé.";
            $this->mailer->send($message_prat, $prat->email, "praticien", "CREATE");
        } catch (Exception $e) {
            throw new MailException("Erreur lors de l'envoi de l'email de confirmation : " . $e->getMessage(), 503);
        }

        return [
            "success" => true,
            "message" => "Le rendez-vous a bien ete annule."
        ];
    }

    public function honorerRDV(string $id_prat, string $id_rdv, bool $statut) {
        try {
            $this->rendezVousRepository->honorerRDV($id_prat, $id_rdv, $statut);
        } catch (\Throwable $th) {
            throw new Exception("Erreur ".$th->getCode()." : ".$th->getMessage());
        }
    }
}