<?php

namespace gateway\config;


use gateway\api\actions\GatewayAuthAction;
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
        return new GatewayRendezVousAction($c->get('rdvs.guzzle.client'));
    },
    'auth.guzzle.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.gateway.auth'),
        ]);
    },
    GatewayAuthAction::class => function (ContainerInterface $c) {
        return new GatewayAuthAction($c->get('auth.guzzle.client'));
    }
];