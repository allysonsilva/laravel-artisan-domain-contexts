<?php

use Allyson\ArtisanDomainContext\Inputs\Context;

return [

    'inputs' => [
        Context::class,
    ],

    'folders' => [
        'domain' => 'Domain',
        'pattern' => '/{,*/,*/*/,*/*/*/,*/*/*/*/}/',
        'components' => [
            'migrations' => 'Database/Migrations',
            'seeders' => 'Database/Seeders',
            'factories' => 'Database/Factories',
            'middlewares' => 'Http/Middlewares',
            'requests' => 'Http/Requests',
            'resources' => 'Http/Resources',
            'casts' => 'Casts',
            'channel' => 'Broadcasting',
            'console' => 'Console/Commands',
            'events' => 'Events',
            'exceptions' => 'Exceptions',
            'jobs' => 'Jobs',
            'listeners' => 'Listeners',
            'mail' => 'Mail',
            'models' => 'Models',
            'notifications' => 'Notifications',
            'observers' => 'Observers',
            'policies' => 'Policies',
            'providers' => 'Providers',
            'rules' => 'Rules',
        ],
    ],
];
