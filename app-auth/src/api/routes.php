<?php
declare(strict_types=1);

use auth\api\actions\RefreshAction;
use auth\api\actions\RegisterPatientAction;
use auth\api\middlewares\AuthnSigninValidationMiddleware;
use auth\api\middlewares\EnregistrerUtilisateurMiddleware;
use Slim\App;
use auth\api\actions\SigninAction;


return function( App $app): App {
    $app->post("/signin", SigninAction::class)
        ->add(AuthnSigninValidationMiddleware::class);
    $app->post('/register', RegisterPatientAction::class)
        ->add(new EnregistrerUtilisateurMiddleware());

    $app->post("/refresh", RefreshAction::class);

    return $app;
};