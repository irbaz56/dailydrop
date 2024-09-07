<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/db/health_check', 'AppController@healthCheck');

$router->post('/otp/send', 'AuthController@otpSend');
$router->post('/otp/verify', 'AuthController@otpVerify');


$router->get('{slug:[A-z]+}', ['uses' => 'CrudController@index']); 
$router->get('{slug:[A-z]+}/{id:[0-9,]+}', ['uses' => 'CrudController@index']); 
$router->put('{slug:[A-z]+}/{id:[0-9,]+}', ['uses' => 'CrudController@update']); 
$router->post('{slug:[A-z]+}', ['uses' => 'CrudController@store']); 
$router->delete('{slug:[A-z]+}/{id:[0-9,]+}', ['uses' => 'CrudController@delete']);

