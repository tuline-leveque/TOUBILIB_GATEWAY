<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use rdvs\api\middlewares\CorsMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();


$builder = new ContainerBuilder();
$builder->useAutowiring(false);
$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/api.php');
$builder->addDefinitions(__DIR__ . '/services.php');

$c = $builder->build();
$app = AppFactory::createFromContainer($c);

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware($c->get('displayErrorDetails'), false, false)
    ->getDefaultErrorHandler()
    ->forceContentType('application/json')
;
//$app->add(new CorsMiddleware());

$app = (require_once __DIR__ . '/../src/api/routes.php')($app);


return $app;