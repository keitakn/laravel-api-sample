<?php
/**
 * 会員登録APIのルーティング設定
 *
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @see \App\Providers\RouteServiceProvider::mapV1RegistersRoutes()
 */

/**
 * 会員登録申請
 *
 * @link https://dev.laravel-api.net/v1/registers
 */
Route::post(
    'registers',
    'V1\RegistersController@store'
);

/**
 * 会員登録完了
 *
 * @link https://dev.laravel-api.net/v1/registers/{register_token}
 */
Route::put(
    'registers/{register_token}',
    'V1\RegistersController@update'
);

/**
 * 会員登録情報取得
 *
 * @link https://dev.laravel-api.net/v1/registers/{register_token}
 */
Route::get(
    'registers/{register_token}',
    'V1\RegistersController@show'
);
