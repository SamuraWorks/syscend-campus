<?php

return [

    'default' => env('VIEW_COMPILER', 'blade'),

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
