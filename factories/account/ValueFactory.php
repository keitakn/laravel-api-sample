<?php
/**
 * アカウント周りのValueObjectを生成するクラス
 *
 * @author keita-nishimoto
 * @since 2016-09-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Factories\Account;

use Domain\Account\EmailValue;
use Domain\Account\PasswordValue;

/**
 * Class ValueFactory
 *
 * @category laravel-api-sample
 * @package Factories\Account
 * @author keita-nishimoto
 * @since 2016-09-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ValueFactory
{

    /**
     * メールアドレスオブジェクトを生成する
     *
     * @param $params
     * @return EmailValue
     */
    public static function createEmailValue($params)
    {
        $email         = $params['email'];
        $emailVerified = $params['emailVerified'];

        $lockVersion = 0;
        if (array_key_exists('lockVersion', $params) === true) {
            if (is_int($params['lockVersion']) === true) {
                $lockVersion = $params['lockVersion'];
            }
        }

        $emailId = 0;
        if (array_key_exists('id', $params) === true) {
            if (is_int($params['id']) === true && $params['id'] > 0) {
                $emailId = $params['id'];
            }
        }

        $emailValue = new EmailValue(
            $email,
            $emailVerified,
            $lockVersion,
            $emailId
        );

        return $emailValue;
    }

    /**
     * パスワードオブジェクトを生成する
     *
     * @param $params
     * @return PasswordValue
     */
    public static function createPasswordValue($params)
    {
        $password     = '';
        $passwordHash = '';
        $lockVersion  = 0;
        $passwordId   = 0;

        if (array_key_exists('password', $params) === true) {
            $password = $params['password'];
        }

        if (array_key_exists('passwordHash', $params) === true) {
            $passwordHash = $params['passwordHash'];
        }

        if (array_key_exists('lockVersion', $params) === true) {
            if (is_int($params['lockVersion']) === true) {
                $lockVersion = $params['lockVersion'];
            }
        }

        if (array_key_exists('id', $params) === true) {
            if (is_int($params['id']) === true && $params['id'] > 0) {
                $passwordId = $params['id'];
            }
        }

        $passwordValue = new PasswordValue(
            $password,
            $passwordHash,
            $lockVersion,
            $passwordId
        );

        return $passwordValue;
    }
}
