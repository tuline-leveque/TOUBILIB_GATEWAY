<?php

use Psr\Container\ContainerInterface;
use toubilib\api\actions\PraticiensAction;
use toubilib\infra\repositories\interface\PraticienRepositoryInterface;
use toubilib\infra\repositories\PDOPraticienRepository;

return [
    // settings
    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',
    'db.config' => __DIR__ . '/.env',

    // infra
    'auth.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('db.config'));
        $dsn = "{$config['auth.driver']}:host={$config['auth.host']};dbname={$config['auth.database']}";
        $user = $config['auth.username'];
        $password = $config['auth.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },
];

