<?php
require_once __DIR__.'/environment.php';

    return [
        'paths' => [
          'migrations' => __DIR__.'/migrations'
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_database'        => 'local',
            'local'                   => [
                'adapter' => 'sqlite',
                'name'    => './database'
            ]
        ]
    ];