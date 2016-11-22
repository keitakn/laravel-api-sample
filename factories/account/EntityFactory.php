<?php
/**
 * アカウント周りのEntityを生成するクラス
 *
 * @author keita-nishimoto
 * @since 2016-10-06
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Factories\Account;

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
     * ユーザーEntityを生成する
     *
     * @param $sub
     * @return \Domain\Account\AccountEntity
     */
    public static function createAccountEntity($sub)
    {
        $instanceKey = 'AccountEntity' . $sub;
        try {
            $accountEntity = \App::make($instanceKey);
            if ($accountEntity instanceof \Domain\Account\AccountEntity) {
                return $accountEntity;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Domain\Account\AccountEntity');
            $accountEntity = \App::make($instanceKey, [$sub]);

            return $accountEntity;
        }
    }
}
