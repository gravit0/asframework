<?php
return [
    'db' => [
        'connect' => 'mysql:host=localhost;dbname=framework',
        'login' => 'framework',
        'password' => ''
    ],
    'users' => [
        'groupmap' => [
            PERM_READ => 'READ',
            PERM_MODER => 'MODER',
            PERM_ADMIN => 'ADM',
            PERM_SUPERUSER => 'SUPERUSER'
        ],
        'flagmap' => [
            FLAG_HIDDEN => 'HIDDEN',
            FLAG_SYSTEM => 'SYSTEM',
            FLAG_NOLOGIN => 'NOLOGIN',
            FLAG_FATALBAN => 'FATALBAN'
        ]
    ],
    'framework' => [
        'name' => 'ASFramework',
        'version' => '0.2.0',
        'release' => 'alpha',
        'builder' => 'Gravit',
        'info' => 'Test Server'
    ],
    'visual' => [
        'stdLayout' => 'main',
        'stdJavaScript' => 'index.js',
        'stdCSS' => 'index.css'
    ]
];
