<?php

use Psr\Container\ContainerInterface;
use rdvs\core\application\usecases\AuthnProvider;
use rdvs\core\application\usecases\interfaces\AuthnProviderInterface;
use rdvs\core\application\usecases\interfaces\ServiceAuthnInterface;
use rdvs\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use rdvs\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use rdvs\core\application\usecases\interfaces\ServicePatientInterface;
use rdvs\core\application\usecases\interfaces\ServicePraticienInterface;
use rdvs\core\application\usecases\interfaces\ServiceRendezVousInterface;
use rdvs\core\application\usecases\ServiceAuthn;
use rdvs\core\application\usecases\ServiceAuthzPatient;
use rdvs\core\application\usecases\ServiceAuthzPraticien;
use rdvs\core\application\usecases\ServicePatient;
use rdvs\core\application\usecases\ServicePraticien;
use rdvs\core\application\usecases\ServiceRendezVous;
use rdvs\infra\repositories\interface\AuthnRepositoryInterface;
use rdvs\infra\repositories\interface\PatientRepositoryInterface;
use rdvs\infra\repositories\interface\PraticienRepositoryInterface;
use rdvs\infra\repositories\interface\RendezVousRepositoryInterface;
use rdvs\infra\repositories\PDOAuthnRepository;
use rdvs\infra\repositories\PDOPatientRepository;
use rdvs\infra\repositories\PDOPraticienRepository;
use rdvs\infra\repositories\PDORendezVousRepository;

return [
    // SERVICES
    PraticienRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPraticienRepository($c->get("prat.pdo"));
    },

    RendezVousRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDORendezVousRepository($c->get("rdv.pdo"));
    },

    PatientRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOPatientRepository($c->get("pat.pdo"));
    },

    AuthnRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthnRepository($c->get("auth.pdo"));
    },

    ServicePraticienInterface::class => function (ContainerInterface $c) {
        return new ServicePraticien($c->get(PraticienRepositoryInterface::class));
    },

    ServiceRendezVousInterface::class => function (ContainerInterface $c) {
        return new ServiceRendezVous($c->get(RendezVousRepositoryInterface::class), $c->get(ServicePraticienInterface::class), $c->get(ServicePatientInterface::class));
    },

    ServicePatientInterface::class => function (ContainerInterface $c) {
        return new ServicePatient($c->get(PatientRepositoryInterface::class));
    },

    ServiceAuthnInterface::class => function (ContainerInterface $c) {
        return new ServiceAuthn($c->get(AuthnProviderInterface::class), $c->get(AuthnRepositoryInterface::class),parse_ini_file($c->get('db.config'))["JWT_SECRET"]);
    },

    AuthnProviderInterface::class => function (ContainerInterface $c) {
        return new AuthnProvider($c->get(AuthnRepositoryInterface::class));
    },

    ServiceAuthzPatientInterface::class => function () {
        return new ServiceAuthzPatient();
    },

    ServiceAuthzPraticienInterface::class => function () {
        return new ServiceAuthzPraticien();
    },
];

