<?php
/**
 * アカウント系APIのルーティング設定
 *
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @see \App\Providers\RouteServiceProvider::mapV1AccountsRoutes()
 */

/**
 * アカウント取得
 *
 * @link https://dev.laravel-api.net/v1/accounts/{sub}
 */
Route::post(
    'accounts/{sub}',
    'V1\AccountsController@show'
);

/**
 * アカウント登録
 *
 * @link https://dev.laravel-api.net/v1/accounts/{sub}/emails/{email_verify_token}
 */
Route::post(
    'accounts',
    'V1\AccountsController@store'
);
