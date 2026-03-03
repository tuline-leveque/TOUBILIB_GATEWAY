<?php
declare(strict_types=1);

use gateway\api\actions\GatewayAuthAction;
use gateway\api\actions\GatewayPraticiensAction;
use gateway\api\actions\GatewayRendezVousAction;
use gateway\api\middlewares\ValidationTokenMiddleware;
use Slim\App;

return function( App $app): App {
//    GET
    // PRATICIENS
    $app->get('/praticiens', GatewayPraticiensAction::class);
    $app->get('/praticiens/{id_prat}', GatewayPraticiensAction::class)
        ->add(ValidationTokenMiddleware::class);

    // RDV
    $app->get('/praticiens/{id_prat}/rdvs', GatewayRendezVousAction::class)
        ->add(ValidationTokenMiddleware::class);
    $app->get('/patients/{id_pat}/rdvs', GatewayRendezVousAction::class)
        ->add(ValidationTokenMiddleware::class);
    $app->get('/praticiens/{id_prat}/rdvs/{id_rdv}', GatewayRendezVousAction::class);

//    POST
    // AUTH
    $app->post('/signin', GatewayAuthAction::class);
    $app->post('/register', GatewayAuthAction::class);
    $app->post('/refresh', GatewayAuthAction::class);

    // PRATICIENS
    $app->post("/praticiens/{id_prat}/indisponibilites", GatewayPraticiensAction::class)
        ->add(ValidationTokenMiddleware::class);

    // RDV
    $app->post('/praticiens/{id_prat}/rdvs', GatewayRendezVousAction::class)
        ->add(ValidationTokenMiddleware::class);

//    DELETE
    // RDV
    $app->delete('/praticiens/{id_prat}/rdvs/{id_rdv}', GatewayRendezVousAction::class)
        ->add(ValidationTokenMiddleware::class);

//    PATCH
    // RDV
    $app->patch('/praticiens/{id_prat}/rdvs/{id_rdv}', GatewayRendezVousAction::class)
        ->add(ValidationTokenMiddleware::class);
    return $app;
};