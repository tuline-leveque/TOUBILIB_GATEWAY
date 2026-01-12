<?php

use Psr\Container\ContainerInterface;
use praticiens\core\application\usecases\interfaces\ServicePatientInterface;
use praticiens\core\application\usecases\interfaces\ServicePraticienInterface;
use praticiens\core\application\usecases\ServicePatient;
use praticiens\core\application\usecases\ServicePraticien;
use praticiens\infra\repositories\interface\PatientRepositoryInterface;
use praticiens\infra\repositories\interface\PraticienRepositoryInterface;
use praticiens\infra\repositories\PDOPatientRepository;
use praticiens\infra\repositories\PDOPraticienRepository;

return [
    // SERVICES
    PraticienRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPraticienRepository($c->get("prat.pdo"));
    },

    PatientRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPatientRepository($c->get("pat.pdo"));
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticien($c->get(PraticienRepositoryInterface::class));
    },

    ServicePatientInterface::class => function (ContainerInterface $c) {
        return new ServicePatient($c->get(PatientRepositoryInterface::class));
    }
];

