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
        'modules/lib-model' => ['install','update','remove'],
        'etc/locale/en-US/form/error/model.php' => ['install','update','remove'],
        'etc/locale/id-ID/form/error/model.php' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'cli' => NULL
            ]
        ],
        'optional' => [
            [
                'lib-model-mysql' => NULL
            ]
        ]
    ],
    'autoload' => [
        'classes' => [
            'LibModel\\Controller' => [
                'type' => 'file',
                'base' => 'modules/lib-model/controller'
            ],
            'LibModel\\Formatter' => [
                'type' => 'file',
                'base' => 'modules/lib-model/formatter'
            ],
            'LibModel\\Iface' => [
                'type' => 'file',
                'base' => 'modules/lib-model/interface'
            ],
            'LibModel\\Library' => [
                'type' => 'file',
                'base' => 'modules/lib-model/library'
            ],
            'LibModel\\Validator' => [
                'type' => 'file',
                'base' => 'modules/lib-model/validator'
            ],
            'Mim\\Model' => [
                'type' => 'file',
                'base' => 'modules/lib-model/system/Model.php'
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
            'priority' => 3000,
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
    ],
    'libValidator' => [
        'validators' => [
            'unique' => 'LibModel\\Validator\\Model::unique'
        ],
        'errors' => [
            '14.0' => 'form.error.model.not_unique'
        ]
    ],
    'libFormatter' => [
        'handlers' => [
            'chain' => [
                'handler' => 'LibModel\\Formatter\\Model::chain',
                'collective' => true,
                'field' => 'id'
            ],
            'multiple-object' => [
                'handler' => 'LibModel\\Formatter\\Model::multipleObject',
                'collective' => true,
                'field' => null
            ],
            'object' => [
                'handler' => 'LibModel\\Formatter\\Model::object',
                'collective' => true,
                'field' => null
            ],
            'partial' => [
                'handler' => 'LibModel\\Formatter\\Model::partial',
                'collective' => true,
                'field' => 'id'
            ]
        ]
    ]
];