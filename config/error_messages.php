<?php
/**
 * エラーコードとエラーメッセージのマッピング
 *
 * @author keita-nishimoto
 * @since 2016-10-18
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @see \Exceptions\DomainException
 */
return [
    404   => 'Not Found',
    403   => 'Forbidden',
    405   => 'Method Not Allowed',
    409   => 'Conflict',
    422   => 'Unprocessable Entity',
    500   => 'An unexpected error has occurred while processing',
    10001 => 'サービスは実行不可能な状態です。',
    10002 => 'バリデーション属性定義が設定されていません。',
    20000 => 'DBへのデータ登録に失敗しました。',
    20001 => 'DBに異常が発生しました。',
    40000 => 'email has already been registered.',
];
