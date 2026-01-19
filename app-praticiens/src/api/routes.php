<?php
declare(strict_types=1);

use praticiens\api\actions\IndisponibiliteAction;
use Slim\App;
use praticiens\api\actions\AjouterIndisponibiliteAction;
use praticiens\api\actions\PraticiensAction;
use praticiens\api\actions\PraticienAction;


return function( App $app): App {
//    GET
    $app->get('/praticiens', PraticiensAction::class); // pas authz

    $app->get('/praticiens/{id_prat}', PraticienAction::class); // pas d'authz

    $app->get('/praticiens/{id_prat}/indisponibilites', IndisponibiliteAction::class);

//    POST
    $app->post("/praticiens/{id_prat}/indisponibilites", AjouterIndisponibiliteAction::class);

    return $app;
};