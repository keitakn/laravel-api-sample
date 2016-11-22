<?php
/**
 * ユーザー仕様クラス
 *
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

use Domain\User\UserEntity;
use Factories\Account\EntityFactory;
use Infrastructures\Utility\StringUtility;

/**
 * Class UserSpecification
 *
 * @category laravel-api-sample
 * @package Domain\Account
 * @author keita-nishimoto
 * @since 2016-09-30
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class AccountSpecification
{

    /**
     * アカウントEntityを新規で作成する
     *
     * @param int $sub
     * @return AccountEntity
     */
    public static function newAccountEntity(int $sub)
    {
        $accountEntity = EntityFactory::createAccountEntity(
            $sub
        );

        // アカウントステータスは0固定
        $accountEntity
            ->setAccountStatus(0)
            ->setLockVersion(0);

        return $accountEntity;
    }
}
