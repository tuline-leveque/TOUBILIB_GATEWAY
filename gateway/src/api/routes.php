<?php
declare(strict_types=1);

use gateway\api\actions\GatewayAuthAction;
use gateway\api\actions\GatewayPraticiensAction;
use gateway\api\actions\GatewayRendezVousAction;
use gateway\api\middlewares\ValidationTokenMiddleware;
use Slim\App;

return function( App $app): App {
//    GET
    $app->get('/praticiens', GatewayPraticiensAction::class)
        ->add(ValidationTokenMiddleware::class);
    $app->get('/praticiens/{id}', GatewayPraticiensAction::class);
    $app->get('/praticiens/{id}/rdvs', GatewayRendezVousAction::class);

//    POST
    $app->post('/signin', GatewayAuthAction::class);
    $app->post('/register', GatewayAuthAction::class);
    $app->post('/refresh', GatewayAuthAction::class);

    return $app;
};