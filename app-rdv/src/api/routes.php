<?php
declare(strict_types=1);

use Slim\App;
use toubilib\api\actions\AnnulerRdvAction;
use toubilib\api\actions\CreateRdvAction;
use toubilib\api\actions\PatientRdvAction;
use toubilib\api\actions\PraticienRdvAction;
use toubilib\api\actions\RdvDetailsAction;
use toubilib\api\actions\ValiderRdvAction;
use toubilib\api\middlewares\CreerRendezVousValidationMiddleware;


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