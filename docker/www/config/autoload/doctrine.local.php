<?php
declare(strict_types=1);

use Doctrine\DBAL\Driver\PDO\MySQL\Driver as MySQL;
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver as PgSQL;

$__docker_db_driver_class = match (strtolower(getenv('DB_DRIVER'))) {
    'mysql' => MySQL::class,
    'pgsql' => PgSQL::class,
    default => throw new RuntimeException("Database driver is not specified or has invalid value"),
};

$__docker_db_driver_port = getenv('DB_PORT') ?: match ($__docker_db_driver_class) {
    'mysql' => 3306,
    'pgsql' => 5432,
    default => throw new RuntimeException("Database port not specified")
};

$__docker_db_driver_class_params = match ($__docker_db_driver_class) {
    'mysql' => [
        'charset'  => 'utf8mb4',
        'driverOptions' => [
            1002 => 'SET NAMES utf8mb4',
        ],
    ],
    default => [],
};

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driver_class' => $__docker_db_driver_class,
                'params'       => [
                    'host'     => getenv('DB_HOST') ?: 'localhost',
                    'port'     => getenv('DB_PORT') ?: $__docker_db_driver_port,
                    'user'     => getenv('DB_USER__FILE') ? file_get_contents(getenv('DB_USER__FILE')) : getenv('DB_USER'),
                    'password' => getenv('DB_PASSWORD__FILE') ? file_get_contents(getenv('DB_PASSWORD__FILE')) : getenv('DB_PASSWORD'),
                    'dbname'   => getenv('DB_DBNAME') ?: 'project_data',
                    ...$__docker_db_driver_class_params
                ],
            ],
        ],
    ],
];
