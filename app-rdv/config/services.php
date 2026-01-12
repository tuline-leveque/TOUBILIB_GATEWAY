<?php

use Psr\Container\ContainerInterface;
use toubilib\core\application\usecases\AuthnProvider;
use toubilib\core\application\usecases\interfaces\AuthnProviderInterface;
use toubilib\core\application\usecases\interfaces\ServiceAuthnInterface;
use toubilib\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use toubilib\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use toubilib\core\application\usecases\interfaces\ServicePatientInterface;
use toubilib\core\application\usecases\interfaces\ServicePraticienInterface;
use toubilib\core\application\usecases\interfaces\ServiceRendezVousInterface;
use toubilib\core\application\usecases\ServiceAuthn;
use toubilib\core\application\usecases\ServiceAuthzPatient;
use toubilib\core\application\usecases\ServiceAuthzPraticien;
use toubilib\core\application\usecases\ServicePatient;
use toubilib\core\application\usecases\ServicePraticien;
use toubilib\core\application\usecases\ServiceRendezVous;
use toubilib\infra\repositories\interface\AuthnRepositoryInterface;
use toubilib\infra\repositories\interface\PatientRepositoryInterface;
use toubilib\infra\repositories\interface\PraticienRepositoryInterface;
use toubilib\infra\repositories\interface\RendezVousRepositoryInterface;
use toubilib\infra\repositories\PDOAuthnRepository;
use toubilib\infra\repositories\PDOPatientRepository;
use toubilib\infra\repositories\PDOPraticienRepository;
use toubilib\infra\repositories\PDORendezVousRepository;

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

