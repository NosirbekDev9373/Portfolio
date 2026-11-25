<?php

return [
    'locales' => [
        'supported' => ['uz', 'en'],
        'default' => 'uz',
        'og' => [
            'uz' => 'uz_UZ',
            'en' => 'en_US',
        ],
    ],

    'owner' => [
        'name' => 'Nurullayev Nosirbek Ibrohim o‘g‘li',
        'email' => 'nosirbeknurullayev9373@gmail.com',
        'linkedin' => 'https://www.linkedin.com/in/nosirbek-nurullayev-14660a446/',
        'github' => 'https://github.com/NosirbekDev9373',
    ],

    'nav' => [
        ['key' => 'home', 'route' => 'home'],
        ['key' => 'about', 'route' => 'home', 'anchor' => '#about'],
        ['key' => 'projects', 'route' => 'projects.index'],
        ['key' => 'skills', 'route' => 'home', 'anchor' => '#skills'],
        ['key' => 'experience', 'route' => 'home', 'anchor' => '#experience'],
        ['key' => 'github', 'route' => 'home', 'anchor' => '#github'],
        ['key' => 'contact', 'route' => 'home', 'anchor' => '#contact'],
    ],

    'skill_groups' => [
        'backend' => 'site.skills.groups.backend',
        'database' => 'site.skills.groups.database',
        'frontend' => 'site.skills.groups.frontend',
        'tools' => 'site.skills.groups.tools',
    ],

    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@portfolio.test'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],
];
