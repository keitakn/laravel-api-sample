<?php
/**
 * エラーコードとHTTPステータスコードのマッピング
 *
 * @author keita-nishimoto
 * @since 2016-10-18
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @see \Domain\ResponseEntity::convertHttpStatusCodeFromErrorCodeIfNeeded()
 */
return [
    403   => 403,
    404   => 404,
    405   => 405,
    409   => 409,
    422   => 422,
    10000 => 500,
    20001 => 500,
    40000 => 409,
];
