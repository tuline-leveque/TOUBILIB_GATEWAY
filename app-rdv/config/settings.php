<?php

use Psr\Container\ContainerInterface;
use toubilib\api\actions\PraticiensAction;
use toubilib\infra\repositories\interface\PraticienRepositoryInterface;
use toubilib\infra\repositories\PDOPraticienRepository;

return [
    'api.praticiens' => 'http://api.praticiens/',
    // settings
    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',
    'db.config' => __DIR__ . '/.env',

    // infra

    'rdv.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('db.config'));
        $dsn = "{$config['rdv.driver']}:host={$config['rdv.host']};dbname={$config['rdv.database']}";
        $user = $config['rdv.username'];
        $password = $config['rdv.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    },

    'pat.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('db.config'));
        $dsn = "{$config['pat.driver']}:host={$config['pat.host']};dbname={$config['pat.database']}";
        $user = $config['pat.username'];
        $password = $config['pat.password'];
        return new \PDO($dsn, $user, $password, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }
];

