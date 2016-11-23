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
     * アカウントEntityを生成する
     *
     * @param int $sub
     * @return AccountEntity
     */
    public static function createAccountEntity(int $sub): AccountEntity
    {
        $instanceKey = 'AccountEntity' . $sub;
        try {
            $accountEntity = \App::make($instanceKey);
            if ($accountEntity instanceof AccountEntity) {
                return $accountEntity;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Domain\Account\AccountEntity');
            $accountEntity = \App::make($instanceKey, [$sub]);

            return $accountEntity;
        }
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
