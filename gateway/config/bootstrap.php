<?php

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();


$builder = new ContainerBuilder();
$builder->useAutowiring(false);
$builder->addDefinitions(__DIR__ . '/services.php');

$c = $builder->build();
$app = AppFactory::createFromContainer($c);

$app = (require_once __DIR__ . '/../src/api/routes.php')($app);


return $app;