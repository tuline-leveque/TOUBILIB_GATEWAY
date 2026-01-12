<?php
declare(strict_types=1);

use gateway\api\actions\GatewayPraticiensAction;
use Slim\App;

return function( App $app): App {
//    GET
    $app->get('/praticiens', GatewayPraticiensAction::class);
    $app->get('/praticiens/{id}', GatewayPraticiensAction::class);
    $app->get('/praticiens/{id}/rdvs', GatewayPraticiensAction::class);

    return $app;
};