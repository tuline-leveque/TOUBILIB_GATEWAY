<?php

use auth\api\actions\RegisterPatientAction;
use auth\api\actions\SigninAction;
use auth\api\middlewares\AuthnSigninValidationMiddleware;
use auth\api\middlewares\AuthzAccessRdvDetailMiddleware;
use auth\api\middlewares\AuthzAccessRdvsMiddleware;
use auth\api\middlewares\AuthzCreationIndisponibiliteMiddleware;
use auth\api\middlewares\AuthzCreationMiddleware;
use auth\api\middlewares\AuthzSuppressionMiddleware;
use auth\api\middlewares\JwtAuthMiddleware;
use auth\core\application\usecases\interfaces\ServiceAuthnInterface;
use auth\core\application\usecases\interfaces\ServiceAuthzPatientInterface;
use auth\core\application\usecases\interfaces\ServiceAuthzPraticienInterface;
use Psr\Container\ContainerInterface;

return [
    // application
    SigninAction::class=> function (ContainerInterface $c) {
        return new SigninAction($c->get(ServiceAuthnInterface::class));
    },

    RegisterPatientAction::class=> function (ContainerInterface $c) {
        return new RegisterPatientAction($c->get(ServiceAuthnInterface::class));
    },

    AuthzCreationMiddleware::class => function (ContainerInterface $c) {
        return new AuthzCreationMiddleware($c->get(ServiceAuthzPatientInterface::class), $c->get(ServiceAuthzPraticienInterface::class));
    },

    AuthzCreationIndisponibiliteMiddleware::class => function (ContainerInterface $c) {
        return new AuthzCreationIndisponibiliteMiddleware($c->get(ServiceAuthzPraticienInterface::class));
    },

    AuthzAccessRdvsMiddleware::class => function (ContainerInterface $c) {
        return new AuthzAccessRdvsMiddleware($c->get(ServiceAuthzPraticienInterface::class));
    },

    AuthzAccessRdvDetailMiddleware::class => function (ContainerInterface $c) {
        return new AuthzAccessRdvDetailMiddleware($c->get(ServiceAuthzPatientInterface::class), $c->get(ServiceAuthzPraticienInterface::class), $c->get(ServiceRendezVousInterface::class));
    },

    AuthzSuppressionMiddleware::class => function (ContainerInterface $c) {
        return new AuthzSuppressionMiddleware($c->get(ServiceAuthzPatientInterface::class), $c->get(ServiceAuthzPraticienInterface::class), $c->get(ServiceRendezVousInterface::class));
    },

    AuthnSigninValidationMiddleware::class => function (ContainerInterface $c) {
        return new AuthnSigninValidationMiddleware();
    },

    JwtAuthMiddleware::class => function (ContainerInterface $c) {
        return new JwtAuthMiddleware(parse_ini_file($c->get('db.config'))["JWT_SECRET"]);
    }
];

