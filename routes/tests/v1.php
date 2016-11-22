<?php
/**
 * テスト系APIのルーティング設定
 * 本番環境では正常動作させない
 *
 * @author keita-nishimoto
 * @since 2016-11-01
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @see \App\Providers\RouteServiceProvider::mapV1TestsRoutes()
 */

/**
 * @link https://dev.laravel-api.net/v1/tests
 */
Route::post(
    'tests',
    'V1\TestsController@databaseQueryException'
);

/**
 * @link https://dev.laravel-api.net/v1/tests
 */
Route::put(
    'tests',
    'V1\TestsController@unexpectedError'
);

/**
 * @link https://dev.laravel-api.net/v1/tests
 */
Route::delete(
    'tests',
    'V1\TestsController@dbDuplicateEntry'
);
