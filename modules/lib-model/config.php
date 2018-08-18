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
        'required' => [],
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
                'LibModel\\Library\\Config::reconfig' => true
            ]
        ]
    ],
];