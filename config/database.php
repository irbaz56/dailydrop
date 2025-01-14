<?php
return [

    /*
    |--------------------------------------------------------------------------
    | PDO Fetch Style
    |--------------------------------------------------------------------------
    |
    | By default, database results will be returned as instances of the PHP
    | stdClass object; however, you may desire to retrieve records in an
    | array format for simplicity. Here you can tweak the fetch style.
    |
     */

    'fetch'       => PDO::FETCH_CLASS,

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
     */

    'default'     => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
     */

    'connections' => [
        'mysql'               => [
            'driver'    => 'mysql',
            'read'      => [
                'host' => env('DB_READ_HOST', '127.0.0.1'),
                'port' => env('DB_READ_PORT', 3306),
            ],
            'write'     => [
                'host' => env('DB_WRITE_HOST', '127.0.0.1'),
                'port' => env('DB_WRITE_PORT', 3306),
            ],
            'database'  => env('DB_DATABASE', 'daily_drop'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix'    => env('DB_PREFIX', ''),
            //'timezone'  => env('DB_TIMEZONE', date('P')),
            'strict'    => env('DB_STRICT_MODE', false),
            'sticky'    => true,
            'options'   => [
                PDO::ATTR_PERSISTENT => env('DB_PERSISTENT', true),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
     */

    'migrations'  => 'migrations',

];
