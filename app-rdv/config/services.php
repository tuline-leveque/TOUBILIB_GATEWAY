<?php

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use rdvs\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use rdvs\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use rdvs\core\application\usecases\interfaces\ServicePatientInterface;
use rdvs\core\application\usecases\interfaces\ServicePraticienInterface;
use rdvs\core\application\usecases\interfaces\ServiceRendezVousInterface;
use rdvs\core\application\usecases\ServiceAuthzPatient;
use rdvs\core\application\usecases\ServiceAuthzPraticien;
use rdvs\core\application\usecases\ServiceRendezVous;
use rdvs\infra\adapters\ServicePraticienAdaptor;
use rdvs\infra\repositories\interface\RendezVousRepositoryInterface;
use rdvs\infra\repositories\PDORendezVousRepository;

return [
    'praticiens.guzzle.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.praticiens'),
        ]);
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticienAdaptor($c->get("praticiens.guzzle.client"));
    },

    // SERVICES
    RendezVousRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDORendezVousRepository($c->get("rdv.pdo"));
    },
    ServiceRendezVousInterface::class => function (ContainerInterface $c) {
        return new ServiceRendezVous($c->get(RendezVousRepositoryInterface::class), $c->get(ServicePraticienInterface::class), $c->get(ServicePatientInterface::class));
    },
    ServiceAuthzPatientInterface::class => function () {
        return new ServiceAuthzPatient();
    },

    ServiceAuthzPraticienInterface::class => function () {
        return new ServiceAuthzPraticien();
    },

    // TODO : DELETE IT
    \rdvs\infra\repositories\interface\PatientRepositoryInterface::class => function (ContainerInterface $c) {
        return new \rdvs\infra\repositories\PDOPatientRepository($c->get("pat.pdo"));
    },

    \rdvs\core\application\usecases\interfaces\ServicePatientInterface::class => function (ContainerInterface $c) {
        return new \rdvs\core\application\usecases\ServicePatient($c->get(\rdvs\infra\repositories\interface\PatientRepositoryInterface::class));
    }
];

