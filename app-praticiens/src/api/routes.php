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
    $app->get('/praticiens', PraticiensAction::class); // pas authz

    $app->get('/praticiens/{id_prat}', PraticienAction::class); // pas d'authz

    $app->post("/praticiens/{id_prat}/indisponibilite", AjouterIndisponibiliteAction::class);

    // exemple /praticiens/4305f5e9-be5a-4ccf-8792-7e07d7017363/rdvs?duree=30&date_heure_debut=2025-10-07 15:00:00&date_heure_fin=2025-10-07 15:30:00&id_pat=d975aca7-50c5-3d16-b211-cf7d302cba50
    $app->post("/signin", SigninAction::class)
        ->add(AuthnSigninValidationMiddleware::class);
    $app->post('/register', RegisterPatientAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());

    $app->post("/refresh", RefreshAction::class);

    return $app;
};