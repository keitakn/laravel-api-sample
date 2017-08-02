<?php
/**
 * アカウント周りのEntityを生成するクラス
 *
 * @author keita-nishimoto
 * @since 2016-10-06
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Factories\Account;

use Domain\Account\AccountEntity;

/**
 * Class EntityFactory
 *
 * @category laravel-api-sample
 * @package Factories\Account
 * @author keita-nishimoto
 * @since 2016-10-06
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class EntityFactory
{
    /**
     * インスタンス格納用の変数
     *
     * @var array
     */
    private static $instancePool = [];

    /**
     * アカウントEntityを生成する
     *
     * @param int $sub
     * @return AccountEntity
     */
    public static function createAccountEntity(int $sub): AccountEntity
    {
        $instanceKey = 'AccountEntity' . $sub;
        if (array_key_exists($instanceKey, self::$instancePool)) {
            if (self::$instancePool[$instanceKey] instanceof AccountEntity) {
                return self::$instancePool[$instanceKey];
            }
        }

        self::$instancePool[$instanceKey] = new AccountEntity($sub);
        return self::$instancePool[$instanceKey];
    }

    /**
     * 空のアカウントEntityを生成する
     *
     * @return AccountEntity
     */
    public static function createEmptyAccountEntity(): AccountEntity
    {
        $sub = 0;

        return self::createAccountEntity($sub);
    }
}
