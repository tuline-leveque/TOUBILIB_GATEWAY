<?php

use Psr\Container\ContainerInterface;
use rdvs\core\application\usecases\interfaces\ServiceRendezVousInterface;
use rdvs\core\application\usecases\ServiceRendezVous;
use rdvs\infra\repositories\interface\RendezVousRepositoryInterface;
use rdvs\infra\repositories\PDORendezVousRepository;

return [
    // SERVICES
    RendezVousRepositoryInterface::class => function (ContainerInterface $c) {
        return new PDORendezVousRepository($c->get("rdv.pdo"));
    },
    ServiceRendezVousInterface::class => function (ContainerInterface $c) {
        return new ServiceRendezVous($c->get(RendezVousRepositoryInterface::class), $c->get(ServicePraticienInterface::class), $c->get(ServicePatientInterface::class));
    }
];

