<?php

namespace gateway\config;


use Psr\Container\ContainerInterface;
use gateway\api\actions\GatewayPraticiensAction;
use GuzzleHttp\Client;

return [
    'praticien.guzzle.client' => function (ContainerInterface $c) {
        return new Client([
            'base_url' => $c->get('api.gateway.toubilib'),
        ]);
    },
    GatewayPraticiensAction::class => function (ContainerInterface $c) {
        return new GatewayPraticiensAction($c->get('praticien.guzzle.client'));
    }
];