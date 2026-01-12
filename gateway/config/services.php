<?php

namespace gateway\config;


use gateway\api\actions\GatewayPraticiensAction;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;

return [
    'praticien.guzzle.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => $c->get('api.gateway.toubilib'),
        ]);
    },
    GatewayPraticiensAction::class => function (ContainerInterface $c) {
        return new GatewayPraticiensAction($c->get('praticien.guzzle.client'));
    }
];