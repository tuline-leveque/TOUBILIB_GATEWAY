<?php
declare(strict_types=1);

use Slim\App;
use toubilib\api\actions\AjouterIndisponibiliteAction;
use toubilib\api\actions\AnnulerRdvAction;
use toubilib\api\actions\CreateRdvAction;
use toubilib\api\actions\PatientRdvAction;
use toubilib\api\actions\PraticienRdvAction;
use toubilib\api\actions\PraticiensAction;
use toubilib\api\actions\PraticienAction;
use toubilib\api\actions\RdvDetailsAction;
use toubilib\api\actions\RefreshAction;
use toubilib\api\actions\RegisterPatientAction;
use toubilib\api\actions\SigninAction;
use toubilib\api\actions\ValiderRdvAction;
use toubilib\api\middlewares\AuthnSigninValidationMiddleware;
use toubilib\api\middlewares\AuthzAccessRdvDetailMiddleware;
use toubilib\api\middlewares\AuthzAccessRdvsMiddleware;
use toubilib\api\middlewares\AuthzCreationIndisponibiliteMiddleware;
use toubilib\api\middlewares\AuthzCreationMiddleware;
use toubilib\api\middlewares\AuthzSuppressionMiddleware;
use toubilib\api\middlewares\CreerRendezVousValidationMiddleware;
use toubilib\api\middlewares\EnregistrerUtilisateurMiddleware;
use toubilib\api\middlewares\JwtAuthMiddleware;


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