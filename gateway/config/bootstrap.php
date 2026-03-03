<?php

use DI\ContainerBuilder;
use gateway\api\middlewares\CorsMiddleware;
use Slim\Factory\AppFactory;



$builder = new ContainerBuilder();
$builder->useAutowiring(false);
$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/services.php');

$c = $builder->build();
$app = AppFactory::createFromContainer($c);

$app = (require_once __DIR__ . '/../src/api/routes.php')($app);

$app->add(new CorsMiddleware());


return $app;