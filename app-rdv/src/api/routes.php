<?php
declare(strict_types=1);

use rdvs\api\actions\RdvDetailsAction;
use rdvs\api\middlewares\CreerRendezVousValidationMiddleware;
use Slim\App;
use rdvs\api\actions\AnnulerRdvAction;
use rdvs\api\actions\CreateRdvAction;
use rdvs\api\actions\PatientRdvAction;
use rdvs\api\actions\PraticienRdvAction;
use rdvs\api\actions\ValiderRdvAction;


return function( App $app): App {
//    GET
    $app->get("/praticiens/{id_prat}/rdvs", PraticienRdvAction::class);

    $app->get("/praticiens/{id_prat}/rdvs/{id_rdv}", RdvDetailsAction::class);
    $app->get("/patients/{id_pat}/rdvs", PatientRdvAction::class);

//    POST
    $app->post("/praticiens/{id_prat}/rdvs", CreateRdvAction::class)
        ->add(new CreerRendezVousValidationMiddleware());

//    DELETE
    $app->delete("/praticiens/{id_prat}/rdvs/{id_rdv}", AnnulerRdvAction::class);
    // Status RDV -> 1 = Annuler

//    PATCH
    $app->patch("/praticiens/{id_prat}/rdvs/{id_rdv}", ValiderRdvAction::class);
    // Status RDV -> -1 = Non honorer
    // Status RDV ->  2 =  honorer

    return $app;
};