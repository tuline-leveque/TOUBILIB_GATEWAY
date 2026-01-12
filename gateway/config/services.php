<?php

namespace gateway\config;


use gateway\api\actions\GatewayPraticiensAction;
use gateway\api\actions\GatewayRendezVousAction;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;

return [
    'praticiens.guzzle.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.gateway.praticiens'),
        ]);
    },
    GatewayPraticiensAction::class => function (ContainerInterface $c) {
        return new GatewayPraticiensAction($c->get('praticiens.guzzle.client'));
    },
    'rdvs.guzzle.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.gateway.rdvs'),
        ]);
    },
    GatewayRendezVousAction::class => function (ContainerInterface $c) {
        return new GatewayPraticiensAction($c->get('rdvs.guzzle.client'));
    },
];