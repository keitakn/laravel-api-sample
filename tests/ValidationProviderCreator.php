<?php
/**
 * データプロバイダで利用するデータを作成するクラス
 *
 * `@dataProvider` を利用する際にこちらの利用する。
 * 使い方としては各 `@dataProvider` から本クラスの静的メソッドを呼び出す。
 * 本クラスで返却するのはバリデーションが通らない値に限定する事。
 * 各テストクラス個別でしか利用しないようなデータは無理にこちらに共通化する必要はない。
 *
 * @author keita-nishimoto
 * @since 2016-11-16
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @link https://phpunit.de/manual/current/ja/writing-tests-for-phpunit.html#writing-tests-for-phpunit.data-providers
 */

namespace Tests;

/**
 * Class ValidationProvider
 *
 * @category laravel-api-sample
 * @package Tests
 * @author keita-nishimoto
 * @since 2016-11-16
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ValidationProviderCreator
{

    /**
     * メールアドレス（必須パラメータの場合）
     *
     * @return array
     */
    public static function emailIsRequiredParams(): array
    {
        return [
            '.から始まるメールアドレス' => [
                '.keita-koga@example.com',
            ],
            // 128文字以上
            '長いメールアドレス' => [
                'keita-koga-' . str_repeat('moo', 36) . '@gmail.com',
            ],
            '空文字' => [
                '',
            ],
            'マルチバイト文字' => [
                'q1あいうえお@gmail.com',
            ],
            'NULL' => [
                null,
            ],
            'Array' => [
                [1, 2, 3],
            ],
            'JSON' => [
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                )
            ],
            '長い文字列' => [
                str_repeat('S', 256),
            ],
            '大きな数値' => [
                4294967296,
            ],
            '数値の0' => [
                0
            ],
            '記号が含まれた文字列' => [
                'a./+*{}~¥¥¥<>?_*}{|~=---}a',
            ],
        ];
    }

    /**
     * パスワード（必須パラメータの場合）
     *
     * @return array
     */
    public static function passwordIsRequiredParams(): array
    {
        return [
            '空文字' => [
                '',
            ],
            'マルチバイト文字' => [
                'q1あいうえおK',
            ],
            'NULL' => [
                null,
            ],
            'Array' => [
                ['a' => 'a'],
            ],
            'JSON' => [
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                )
            ],
            '101文字以上のパスワード' => [
                str_repeat('p1', 50) . 'A',
            ],
            '数値のみのパスワード' => [
                12345678
            ],
            '小文字のみのパスワード' => [
                'password',
            ],
            '大文字のみのパスワード' => [
                'PASSWORD'
            ],
            '記号が含まれているパスワード' => [
                'Passwd\n',
            ],
            '8文字未満のパスワード' => [
                'PassWd1',
            ],
        ];
    }

    /**
     * email_verified（任意パラメータ）
     *
     * @return array
     */
    public static function emailVerifiedIsOptionalParams(): array
    {
        return [
            'NULL' => [
                null,
            ],
            'Array' => [
                ['a' => 'a'],
            ],
            'JSON' => [
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                )
            ],
            '1以外の数値' => [
                2,
            ],
            'マイナス数値' => [
                -1
            ],
        ];
    }

    /**
     * ユーザーID（sub）（必須パラメータ）
     *
     * @return array
     */
    public static function subIsRequiredParams(): array
    {
        return [
            'マルチバイト文字' => [
                'あいうえお',
            ],
            '記号が含まれている' => [
                'a}l;p@',
            ],
            // JSON文字列
            'JSON' => [
                json_encode(
                    ['a', 'b', 'c']
                ),
            ],
            '大きな数字' => [
                4294967295,
            ],
            '数値の0' => [
                0,
            ],
            '負の数' => [
                -1
            ],
        ];
    }
}
