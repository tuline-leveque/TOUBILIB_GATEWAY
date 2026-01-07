<?php
declare(strict_types=1);

use gateway\api\actions\PraticiensAction;
use Slim\App;

return function( App $app): App {
//    GET
    $app->get('/praticiens', PraticiensAction::class);

    return $app;
};