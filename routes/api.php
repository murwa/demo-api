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
app('Dingo\Api\Routing\Router')->version('v1', ['middleware' => 'bindings'], function (\Dingo\Api\Routing\Router $api) use ($namespace) {
    $api->post('auth', [
        'uses' => $namespace . 'AuthController@login',
        'as'   => 'auth.auth',
    ]);
    $api->get('accounts/{account}/balance', [
        'uses' => $namespace . 'AccountController@balance',
        'as'   => 'accounts.balance',
    ]);
    $api->post('accounts/{account}/deposit', [
        'uses' => $namespace . 'AccountController@deposit',
        'as'   => 'accounts.deposit',
    ]);
    $api->post('accounts/{account}/withdraw', [
        'uses' => $namespace . 'AccountController@withdraw',
        'as'   => 'accounts.withdraw',
    ]);
    $api->post('accounts/{account}/transfer', [
        'uses' => $namespace . 'AccountController@transfer',
        'as'   => 'accounts.transfer',
    ]);
});
