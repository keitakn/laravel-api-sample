<?php
/**
 * メールアドレス仕様クラス
 *
 * @author keita-nishimoto
 * @since 2016-11-21
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

use Repositories\Mysql\AccountRepository;

/**
 * Class RegisterSpecification
 *
 * @category laravel-api-sample
 * @package Domain\Account
 * @author keita-nishimoto
 * @since 2016-11-21
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class EmailSpecification
{
    /**
     * メールアドレスが登録可能か判定する
     *
     * @param EmailValue $emailValue
     * @return bool
     */
    public static function canRegisterableEmail(EmailValue $emailValue): bool
    {
        $accountRepository = AccountRepository::getInstance();
        $accountEntity = $accountRepository->findAccountEntityByEmail($emailValue);

        if ($accountEntity instanceof AccountEntity) {
            return false;
        }

        return true;
    }
}
