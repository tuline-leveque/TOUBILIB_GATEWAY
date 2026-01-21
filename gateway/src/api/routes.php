<?php
declare(strict_types=1);

use gateway\api\actions\GatewayAuthAction;
use gateway\api\actions\GatewayPraticiensAction;
use gateway\api\actions\GatewayRendezVousAction;
use Slim\App;

return function( App $app): App {
//    GET
    // PRATICIENS
    $app->get('/praticiens', GatewayPraticiensAction::class);
    $app->get('/praticiens/{id}', GatewayPraticiensAction::class);

    // RDV
    $app->get('/praticiens/{id}/rdvs', GatewayRendezVousAction::class);
    $app->get('/patients/{id}/rdvs', GatewayRendezVousAction::class);
    $app->get('/praticiens/{id}/rdvs/{id_rdv}', GatewayRendezVousAction::class);

//    POST
    // AUTH
    $app->post('/signin', GatewayAuthAction::class);
    $app->post('/register', GatewayAuthAction::class);
    $app->post('/refresh', GatewayAuthAction::class);

    // PRATICIENS
    $app->post("/praticiens/{id_prat}/indisponibilites", GatewayPraticiensAction::class);

    // RDV
    $app->post('/praticiens/{id}/rdvs', GatewayRendezVousAction::class);

//    DELETE
    // RDV
    $app->delete('/praticiens/{id}/rdvs/{id_rdv}', GatewayRendezVousAction::class);

//    PATCH
    // RDV
    $app->patch('/praticiens/{id}/rdvs/{id_rdv}', GatewayRendezVousAction::class);
    return $app;
};