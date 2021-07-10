<?php

return [

    // The folder where the locking files will be stored.
    'folder' => storage_path('app/supervisor'),

    // Long lived commands that will be started by supervisor
    'commands' => [

        // A user chosen unique name for this command
        'queue' => [
            'command' => 'queue:work',
            'params' => [
                '--queue' => 'high,default',
            ],
        ],

        /*
        // Let's run a websocket server
        'queue' => [
            'command' => 'websockets:serve',
            'params' => [],
        ],
        // */
    ],

];
