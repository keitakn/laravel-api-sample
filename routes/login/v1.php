<?php
/**
 * ログイン系APIのルーティング設定
 *
 * @author keita-nishimoto
 * @since 2016-10-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @see \App\Providers\RouteServiceProvider::mapV1AccountsRoutes()
 */

/**
 * @link https://dev.laravel-api.net/v1/login/password
 */
Route::post(
    'login/password',
    'V1\LoginController@store'
);
