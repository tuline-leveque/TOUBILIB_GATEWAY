<?php

use Psr\Container\ContainerInterface;
use rdvs\api\actions\AnnulerRdvAction;
use rdvs\api\actions\CreateRdvAction;
use rdvs\api\actions\PatientRdvAction;
use rdvs\api\actions\PraticienRdvAction;
use rdvs\api\actions\RdvDetailsAction;
use rdvs\api\actions\ValiderRdvAction;
use rdvs\core\application\usecases\interfaces\ServiceRendezVousInterface;

return [
    // application
    PraticienRdvAction::class=> function (ContainerInterface $c) {
        return new PraticienRdvAction($c->get(ServiceRendezVousInterface::class));
    },
    RdvDetailsAction::class=> function (ContainerInterface $c) {
        return new RdvDetailsAction($c->get(ServiceRendezVousInterface::class));
    },
    CreateRdvAction::class=> function (ContainerInterface $c) {
        return new CreateRdvAction($c->get(ServiceRendezVousInterface::class));
    },
    AnnulerRdvAction::class=> function (ContainerInterface $c) {
        return new AnnulerRdvAction($c->get(ServiceRendezVousInterface::class));
    },
    ValiderRdvAction::class=> function (ContainerInterface $c) {
        return new ValiderRdvAction($c->get(ServiceRendezVousInterface::class));
    },
    PatientRdvAction::class=> function (ContainerInterface $c) {
        return new PatientRdvAction($c->get(ServiceRendezVousInterface::class));
    }
];

