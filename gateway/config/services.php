<?php

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use toubilib\api\actions\PraticiensAction;

return [
    'praticien.guzzle.client' => function (ContainerInterface $c) {
        return new Client([
            'base_url' => $c->get('toubilib.praticien.api')
        ]);
    },
    PraticiensAction::class => function (ContainerInterface $c) {
        return new PraticiensAction($c->get('praticien.guzzle.client'));
    }
];