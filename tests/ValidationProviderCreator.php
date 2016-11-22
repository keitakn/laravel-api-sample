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
    public static function createEmailInRequiredParams()
    {
        return [
            '.から始まるメールアドレス' => [
                '.keita-koga@gmail.com',
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
     * 会員登録トークン（必須パラメータの場合）
     *
     * @return array
     */
    public static function createRegisterTokenRequiredParams()
    {
        return [
            'マルチバイト文字' => [
                'q1あいうえおK',
            ],
            'JSON' => [
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                )
            ],
            '長い文字列' => [
                str_repeat('a', 65),
            ],
            '大きな数値' => [
                4294967296,
            ],
            'ホワイトリストに含まれていない数値' => [
                0
            ],
            '記号が含まれた文字列' => [
                'a.a@',
            ],
        ];
    }

    /**
     * パスワード（必須パラメータの場合）
     *
     * @return array
     */
    public static function createPasswordTokenRequiredParams()
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
            '大きな文字列' => [
                str_repeat('p1', 51),
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
     * 姓名（必須パラメータの場合）
     *
     * @return array
     */
    public static function createNameRequiredParams()
    {
        return [
            '数値' => [
                1,
                1,
            ],
            '大きな文字列（氏名は100文字まで）' => [
                str_repeat('漢', 101),
                str_repeat('漢', 101),
            ],
            'NULL' => [
                null,
                null,
            ],
            '空' => [
                '',
                '',
            ],
            'スクリプト' => [
                '<script>alert(1);</script>',
                '<script>alert(1);</script>',
            ],
            '!' => [
                '!',
                '!',
            ],
            '"' => [
                '"',
                '"',
            ],
            '#' => [
                '#',
                '#',
            ],
            '$' => [
                '$',
                '$',
            ],
            '%' => [
                '%',
                '%',
            ],
            '&' => [
                '&',
                '&',
            ],
            '\'' => [
                '\'',
                '\'',
            ],
            '(' => [
                '(',
                '(',
            ],
            ')' => [
                ')',
                ')',
            ],
            '*' => [
                '*',
                '*',
            ],
            '+' => [
                '+',
                '+',
            ],
            ',' => [
                ',',
                ',',
            ],
            '-' => [
                '-',
                '-',
            ],
            '.' => [
                '.',
                '.',
            ],
            '/' => [
                '/',
                '/',
            ],
            ':' => [
                ':',
                ':',
            ],
            ';' => [
                ';',
                ';',
            ],
            '<' => [
                '<',
                '<',
            ],
            '=' => [
                '=',
                '=',
            ],
            '>' => [
                '>',
                '>',
            ],
            '?' => [
                '?',
                '?',
            ],
            '@' => [
                '@',
                '@',
            ],
            '[' => [
                '[',
                '[',
            ],
            '\n' => [
                '\n',
                '\n',
            ],
            ']' => [
                ']',
                ']',
            ],
            '^' => [
                '^',
                '^',
            ],
            '_' => [
                '_',
                '_',
            ],
            '`' => [
                '`',
                '`',
            ],
            '{' => [
                '{',
                '{',
            ],
            '}' => [
                '}',
                '}',
            ],
            '|' => [
                '|',
                '|',
            ],
            '~' => [
                '~',
                '~',
            ],
            '¥' => [
                '¥',
                '¥',
            ],
        ];
    }

    /**
     * 姓名（ふりがな）（任意パラメータの場合）
     *
     * @return array
     */
    public static function createNameKanaOptionalParams()
    {
        return [
            'Array' => [
                // given_name_kana
                [],
                // family_name_kana
                [],
            ],
            '大きな数値' => [
                // given_name_kana
                99999999999,
                // family_name_kana
                99999999999,
            ],
            '大きな文字列' => [
                // given_name_kana
                str_repeat('な', 101),
                // family_name_kana
                str_repeat('な', 101),
            ],
            '漢字が含まれている' => [
                '漢字なまえ',
                '漢字なまえ',
            ],
        ];
    }

    /**
     * 性別（任意パラメータの場合）
     *
     * @return array
     */
    public static function createGenderInRequiredParams()
    {
        return [
            'マルチバイト文字' => [
                'q1あいうえおK',
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
            '大きな数値' => [
                4294967296,
            ],
            'ホワイトリストに含まれていない数値' => [
                10,
            ],
            '記号が含まれた文字列' => [
                'a./+*{}~¥¥¥<>?_*}{|~=---}a',
            ],
        ];
    }

    /**
     * 国コード（必須パラメータの場合）
     *
     * @return array
     */
    public static function createCountryInRequiredParams()
    {
        return [
            'マルチバイト文字' => [
                'q1あいうえおK',
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
            '大きな数値' => [
                4294967296,
            ],
            'ホワイトリストに含まれていない数値' => [
                10,
            ],
            '記号が含まれた文字列' => [
                'a./+*{}~¥¥¥<>?_*}{|~=---}a',
            ],
            '2桁の国コード' => [
                'JP'
            ],
        ];
    }

    /**
     * regionのバリデーション
     *
     * @return array
     */
    public static function createRegionInRequiredParams()
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
                [1, 2, 3],
            ],
            'JSON' => [
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                )
            ],
            '大きな数値' => [
                4294967296,
            ],
            '記号が含まれた文字列' => [
                'a./+*{}~¥¥¥<>?_*}{|~=---}a',
            ],
            '登録されていないコード' => [
                48
            ],
        ];
    }

    /**
     * 職業コード（任意パラメータの場合）
     *
     * @return array
     */
    public static function createOccupationCodeInOptionalParams()
    {
        return [
            'マルチバイト文字' => [
                'q1あいうえおK',
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
            'ホワイトリストに含まれていない数値' => [
                0
            ],
            '記号が含まれた文字列' => [
                'a./+*{}~¥¥¥<>?_*}{|~=---}a',
            ],
        ];
    }

    /**
     * 職業コード（必須パラメータの場合）
     *
     * @return array
     */
    public static function createOccupationCodeInRequiredParams()
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
            'ホワイトリストに含まれていない数値' => [
                0
            ],
            '記号が含まれた文字列' => [
                'a./+*{}~¥¥¥<>?_*}{|~=---}a',
            ],
        ];
    }

    /**
     * 卒業年（任意パラメータ）
     *
     * @return array
     */
    public function createGraduationYearInOptionalParams()
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
            'ホワイトリストに含まれていない数値' => [
                0
            ],
            '記号が含まれた文字列' => [
                'a./+*{}~¥¥¥<>?_*}{|~=---}a',
            ],
            '5桁の数字' => [
                20001
            ],
            'マイナスの数値' => [
                -1111
            ],
        ];
    }

    /**
     * 誕生日（任意パラメータ）
     *
     * @return array
     */
    public static function createBirthdateInOptionalParams()
    {
        return [
            'NULL' => [
                null,
            ],
            '指定外のフォーマット' => [
                '2015/01/01',
            ],
            '日時形式のフォーマット' => [
                // birthdate
                '2015-01-01 00:00:00',
            ],
        ];
    }

    /**
     * is_login_auto（任意パラメータ）
     *
     * @return array
     */
    public static function createIsLoginAutoInOptionalParams()
    {
        return [
            'マルチバイト文字' => [
                'q1あいうえおK',
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
            '0, 1以外の数値' => [
                2
            ],
            '記号が含まれた文字列' => [
                'a./+*{}~¥¥¥<>?_*}{|~=---}a',
            ],
        ];
    }

    /**
     * ユーザーID（必須パラメータ）
     *
     * @return array
     */
    public static function createSubInRequiredParams()
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

    /**
     * 認証メールアドレス（必須パラメータ）
     *
     * @return array
     */
    public static function createAuthenticationEmailInRequiredParams()
    {
        return [
            '空文字' => [
                '',
            ],
            'NULL' => [
                null,
            ],
            'マルチバイト文字' => [
                'あいうえお',
            ],
            '記号' => [
                '.+*{}~¥¥¥<>?_*}{|~=---}',
            ],
            '129文字以上のメールアドレス' => [
                str_repeat('@', 129),
            ],
        ];
    }

    /**
     * 認証パスワード（必須パラメータ）
     *
     * @return array
     */
    public static function createAuthenticationPasswordInRequiredParams()
    {
        return [
            '空文字' => [
                '',
            ],
            'NULL' => [
                null,
            ],
            'マルチバイト文字' => [
                'あいうえお',
            ],
            '記号' => [
                '.+*{}~¥¥¥<>?_*}{|~=---}',
            ],
            '3桁以下のパスワード' => [
                'pas',
            ],
        ];
    }

    /**
     * メールアドレス検証トークン（必須パラメータ）
     *
     * @return array
     */
    public static function createEmailVerifyTokenInRequiredParams()
    {
        return [
            'マルチバイト文字' => [
                'あいうえお',
            ],
            '記号' => [
                '{@@@}',
            ],
            'bigInt' => [
                99999999999,
                99999999999,
            ],
            '大きな文字列' => [
                str_repeat('q', 65),
            ],
        ];
    }
}
