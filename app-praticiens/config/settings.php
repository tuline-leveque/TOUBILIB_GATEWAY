<?php

use Psr\Container\ContainerInterface;

return [
    // settings
    'displayErrorDetails' => true,
    'logs.dir' => __DIR__ . '/../var/logs',
    'db.config' => __DIR__ . '/.env',

    // infra
     'prat.pdo' => function (ContainerInterface $c) {
        $config = parse_ini_file($c->get('db.config'));
        $dsn = "{$config['prat.driver']}:host={$config['prat.host']};dbname={$config['prat.database']}";
        $user = $config['prat.username'];
        $password = $config['prat.password'];
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

