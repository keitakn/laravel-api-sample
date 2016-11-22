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
    10000 => 500,
    11000 => 422,
    20001 => 500,
    40000 => 409,
    40001 => 409,
    40002 => 404,
    40004 => 404,
    40005 => 401,
    40007 => 404,
    40008 => 403,
    40009 => 403,
    40010 => 409,
    40011 => 404,
];
