<?php

return [

    // The folder where the locking files will be stored.
    'folder' => storage_path('app/supervisor'),

    // Default queue, called when no "name" param is passed to supervisor.
    'default' => [],

    'queues' => [

        // Example of a prioritized queue. You can run this using the following command:
        // artisan supervisor:run --name=high
        'high' => [
            '--queue' => 'high,default',
        ],

        // Example of a queue with a maximum of 3 retries. You can run this using the following command:
        // artisan supervisor:run --name=retry
        'retry' => [
            ' --tries' => '3',
        ],

    ],

];
