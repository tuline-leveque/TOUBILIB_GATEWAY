<?php

use praticiens\api\actions\IndisponibiliteAction;
use Psr\Container\ContainerInterface;
use praticiens\api\actions\AjouterIndisponibiliteAction;
use praticiens\api\actions\PraticiensAction;
use praticiens\api\actions\PraticienAction;
use praticiens\core\application\usecases\interfaces\ServicePraticienInterface;

return [
    // application
    PraticiensAction::class=> function (ContainerInterface $c) {
        return new PraticiensAction($c->get(ServicePraticienInterface::class));
    },
    PraticienAction::class=> function (ContainerInterface $c) {
        return new PraticienAction($c->get(ServicePraticienInterface::class));
    },
    AjouterIndisponibiliteAction::class=> function (ContainerInterface $c) {
        return new AjouterIndisponibiliteAction($c->get(ServicePraticienInterface::class));
    },
    IndisponibiliteAction::class=> function (ContainerInterface $c) {
        return new IndisponibiliteAction($c->get(ServicePraticienInterface::class));
    }
];

