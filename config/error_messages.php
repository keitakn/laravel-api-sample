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
    422   => 'パラメータが正しく設定されていません。',
    10000 => '予期せぬエラーが発生しました。',
    10001 => 'バリデーションパラメータ定義が設定されていません。',
    10002 => 'バリデーション属性定義が設定されていません。',
    20000 => 'DBへのデータ登録に失敗しました。',
    20001 => 'DBに異常が発生しました。',
    40000 => 'メールアドレスは既に登録されています。',
];
