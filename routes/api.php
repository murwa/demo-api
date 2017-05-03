<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$namespace = 'App\Http\Controllers\\';
# API Routes
app('Dingo\Api\Routing\Router')->version('v1', function (\Dingo\Api\Routing\Router $api) use ($namespace) {
    $api->post('auth', [
        'uses' => $namespace . 'AuthController@auth',
        'as'   => 'auth.auth',
    ]);
});
