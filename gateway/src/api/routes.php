<?php
declare(strict_types=1);

use gateway\api\actions\GatewayPraticiensAction;
use Slim\App;

return function( App $app): App {
//    GET
    $app->get('/praticiens', GatewayPraticiensAction::class);

    return $app;
};