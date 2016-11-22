<?php
/**
 * ユーザー系APIのルーティング設定
 *
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @see \App\Providers\RouteServiceProvider::mapV1UsersRoutes()
 */

/**
 * @link https://dev.laravel-api.net/v1/users/{sub}
 */
Route::get(
    'users/{sub}',
    'V1\UsersController@show'
);
