<?php
declare(strict_types=1);

use rdvs\api\actions\RdvDetailsAction;
use rdvs\api\middlewares\AuthzAccessRdvDetailMiddleware;
use rdvs\api\middlewares\AuthzAccessRdvsMiddleware;
use rdvs\api\middlewares\AuthzSuppressionMiddleware;
use rdvs\api\middlewares\CreerRendezVousValidationMiddleware;
use rdvs\api\middlewares\JwtAuthMiddleware;
use Slim\App;
use rdvs\api\actions\AnnulerRdvAction;
use rdvs\api\actions\CreateRdvAction;
use rdvs\api\actions\PatientRdvAction;
use rdvs\api\actions\PraticienRdvAction;
use rdvs\api\actions\ValiderRdvAction;


return function( App $app): App {
//    GET
    $app->get("/praticiens/{id_prat}/rdvs", PraticienRdvAction::class)
        ->add(AuthzAccessRdvsMiddleware::class)
        ->add(JwtAuthMiddleware::class);

    $app->get("/praticiens/{id_prat}/rdvs/{id_rdv}", RdvDetailsAction::class)
        ->add(AuthzAccessRdvDetailMiddleware::class)
        ->add(JwtAuthMiddleware::class);

    $app->get("/patients/{id_pat}/rdvs", PatientRdvAction::class)
        ->add(AuthzAccessRdvsMiddleware::class)
        ->add(JwtAuthMiddleware::class);

//    POST
    $app->post("/praticiens/{id_prat}/rdvs", CreateRdvAction::class)
        ->add(new CreerRendezVousValidationMiddleware())
        ->add(JwtAuthMiddleware::class);

//    DELETE
    $app->delete("/praticiens/{id_prat}/rdvs/{id_rdv}", AnnulerRdvAction::class)
        ->add(AuthzSuppressionMiddleware::class)
        ->add(JwtAuthMiddleware::class);
    // Status RDV -> 1 = Annuler

//    PATCH
    $app->patch("/praticiens/{id_prat}/rdvs/{id_rdv}", ValiderRdvAction::class)
        ->add(AuthzSuppressionMiddleware::class)
        ->add(JwtAuthMiddleware::class);
    // Status RDV -> -1 = Non honorer
    // Status RDV ->  2 =  honorer

    return $app;
};