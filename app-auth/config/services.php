<?php

use auth\core\application\usecases\AuthnProvider;
use auth\core\application\usecases\interfaces\AuthnProviderInterface;
use auth\core\application\usecases\interfaces\ServiceAuthnInterface;
use auth\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use auth\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use auth\core\application\usecases\ServiceAuthn;
use auth\core\application\usecases\ServiceAuthzPatient;
use auth\core\application\usecases\ServiceAuthzPraticien;
use auth\infra\repositories\interface\AuthnRepositoryInterface;
use auth\infra\repositories\PDOAuthnRepository;
use Psr\Container\ContainerInterface;

return [
    // SERVICES
    AuthnRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDOAuthnRepository($c->get("auth.pdo"));
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

