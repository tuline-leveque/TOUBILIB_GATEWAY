<?php
declare(strict_types=1);

use gateway\api\actions\GatewayPraticiensAction;
use gateway\api\actions\GatewayRendezVousAction;
use Slim\App;

return function( App $app): App {
//    GET
    $app->get('/praticiens', GatewayPraticiensAction::class);
    $app->get('/praticiens/{id}', GatewayPraticiensAction::class);
    $app->get('/praticiens/{id}/rdvs', GatewayRendezVousAction::class);

    return $app;
};