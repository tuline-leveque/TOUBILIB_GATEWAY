<?php

namespace toubilib\core\application\usecases;
use toubilib\api\dtos\PatientDTO;
use toubilib\core\application\usecases\interfaces\ServicePatientInterface;
use toubilib\core\exceptions\EntityNotFoundException;
use toubilib\infra\repositories\interface\PatientRepositoryInterface;

class ServicePatient implements ServicePatientInterface {
    private PatientRepositoryInterface $patientRepository;

    public function __construct(PatientRepositoryInterface $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    /**
     * @throws \Exception
     */
    public function getPatient(string $id): PatientDTO {
        try {
            $pat = $this->patientRepository->getPatient($id);
        } catch (EntityNotFoundException $e) {
            throw new EntityNotFoundException($e->getEntity()." not found", $e->getEntity());
        } catch (\Exception $e) {
            throw new \Exception("probleme lors de la reception du patient.", $e->getCode());
        }

        return new PatientDTO(
            id: $pat->id,
            nom: $pat->nom,
            prenom: $pat->prenom,
            telephone: $pat->telephone,
            date_naissance: $pat->date_naissance,
            adresse: $pat->adresse,
            code_postal: $pat->code_postal,
            ville: $pat->ville,
            email: $pat->email
        );
    }
}