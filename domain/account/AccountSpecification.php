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
use Repositories\Mysql\AccountRepository;

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
     * @return AccountEntity
     */
    public static function newAccountEntity()
    {
        $accountRepository = AccountRepository::getInstance();

        $params = [
            'status'       => 0,
            'lock_version' => 0,
        ];

        $accountEntity = $accountRepository->createAccountEntity($params);

        return $accountEntity;
    }
}
