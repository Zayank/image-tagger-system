<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'storage' => env('STORAGE_URL', 'storage'),


    'posts' =>  [

        'posts_tmp_url' =>  env('POSTS_TEMPORARY_URL'   , 'posts/tmp'),
        'posts_url'     =>  env('POSTS_URL'             , 'posts')

    ]
];
