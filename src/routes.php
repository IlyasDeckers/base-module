<?php
use Illuminate\Http\Request;

Route::middleware('auth:api')
    ->prefix('api/v2/')
    ->namespace('Clockwork\Base\Auth\Http\Controllers')
    ->group(function () {
        Route::get('oauth/hello', 'AuthController@hello');
    }
);

Route::middleware('api')
    ->prefix('api/v2/')
    ->namespace('Clockwork\Base\Auth\Http\Controllers')
    ->group(function () {
        Route::post('oauth/forgot', 'ResetController@forgot');
        Route::post('oauth/login', 'AuthController@login');
        Route::post('oauth/reset', 'ResetController@reset');
    }
);