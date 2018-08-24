<?php

return [
    '__name' => 'lib-model',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/lib-model.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/lib-model' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'cli' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'LibModel\\Iface' => [
                'type' => 'file',
                'base' => 'modules/lib-model/interface'
            ],
            'Mim\\Model' => [
                'type' => 'file',
                'base' => 'modules/lib-model/system/Model.php'
            ],
            'LibModel\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-model/library'
            ],
            'LibModel\\Controller' => [
                'type' => 'file',
                'base' => 'modules/lib-model/controller'
            ]
        ],
        'files' => []
    ],
    'libModel' => [
        'target' => [
            'read' => 'default',
            'write' => 'default'
        ]
    ],
    'callback' => [
        'app' => [
            'reconfig' => [
                'LibModel\\Library\\Config::reconfig' => TRUE
            ]
        ]
    ],
    'gates' => [
        'lib-model' => [
            'host' => [
                'value' => 'CLI'
            ],
            'path' => [
                'value' => 'migrate'
            ]
        ]
    ],
    'routes' => [
        'lib-model' => [
            404 => [
                'handler' => 'Cli\\Controller::show404'
            ],
            500 => [
                'handler' => 'Cli\\Controller::show500'
            ],
            'libModelMigrateTest' => [
                'info' => 'Test migration',
                'path' => [
                    'value' => 'test'
                ],
                'handler' => 'LibModel\\Controller\\Migrate::test'
            ],
            'libModelMigrateStart' => [
                'info' => 'Start migration',
                'path' => [
                    'value' => 'start'
                ],
                'handler' => 'LibModel\\Controller\\Migrate::start'
            ],
            'libModelMigrateSchema' => [
                'info' => 'Start migration and put the query to some file in target dir',
                'path' => [
                    'value' => 'schema (:dirname)',
                    'params' => [
                        'dirname' => 'any'
                    ]
                ],
                'handler' => 'LibModel\\Controller\\Migrate::schema'
            ]
        ]
    ]
];