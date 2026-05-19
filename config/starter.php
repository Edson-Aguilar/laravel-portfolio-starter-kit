<?php

return [
    'modules' => [
        'projects' => env('STARTER_MODULE_PROJECTS', true),
        'appearance' => env('STARTER_MODULE_APPEARANCE', true),
        'activity_log' => env('STARTER_MODULE_ACTIVITY_LOG', false),
        'api' => env('STARTER_MODULE_API', true),
        'exports' => env('STARTER_MODULE_EXPORTS', false),
    ],
];
