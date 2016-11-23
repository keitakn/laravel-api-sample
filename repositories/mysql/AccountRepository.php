<?php
/**
 * アカウントリポジトリ
 *
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @link https://readouble.com/laravel/5.3/ja/controllers.html
 */

namespace Repositories\Mysql;

use Domain\Account\AccountEntity;
use Domain\Account\AccountRepositoryInterface;
use Domain\Account\EmailValue;
use Domain\Account\EmailVerifyTokenEntity;
use Domain\User\UserEntity;
use Exceptions\DomainException;
use Factories\Account\EntityFactory;
use Factories\Account\ValueFactory;

/**
 * Class AccountRepository
 *
 * @category laravel-api-sample
 * @package Repositories
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class AccountRepository implements AccountRepositoryInterface
{
    /**
     * 自身のインスタンスを生成する
     *
     * @return \Repositories\Mysql\AccountRepository
     */
    public static function getInstance()
    {
        $instanceKey = 'AccountRepository';
        try {
            $instance = \App::make($instanceKey);
            if ($instance instanceof AccountRepository) {
                return $instance;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Repositories\Mysql\AccountRepository');
            $instance = \App::make($instanceKey);

            return $instance;
        }
    }

    /**
     * アカウントEntityを新規で作成し保存する
     *
     * @param array $params
     * @return AccountEntity
     * @throws DomainException
     */
    public function createAccountEntity(array $params): AccountEntity
    {
        $values = [
            'status'       => $params['status'],
            'lock_version' => $params['lock_version'],
        ];

        $result = \DB::table('accounts')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        $lastInsertId = \DB::getPdo()->lastInsertId();

        $accountEntity = EntityFactory::createAccountEntity(
            (int)$lastInsertId
        );

        $accountEntity->setAccountStatus($values['status'])
            ->setLockVersion($values['lock_version']);

        return $accountEntity;
    }

    /**
     * メールアドレスオブジェクトを保存する
     *
     * @param AccountEntity $accountEntity
     * @return AccountEntity
     * @throws DomainException
     */
    public function saveEmail(AccountEntity $accountEntity): AccountEntity
    {
        $emailValue = $accountEntity->getEmailValue();

        $values = [
            'account_id'     => $accountEntity->getSub(),
            'email'          => $emailValue->getEmail(),
            'email_verified' => $emailValue->isEmailVerified()
        ];

        $result = \DB::table('accounts_emails')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        $emailId = \DB::getPdo()->lastInsertId();

        $newEmailValue = ValueFactory::createEmailValue(
            [
                'email'         => $emailValue->getEmail(),
                'emailVerified' => $emailValue->isEmailVerified(),
                'id'            => (int)$emailId,
            ]
        );

        $accountEntity->setEmailValue($newEmailValue);

        return $accountEntity;
    }

    /**
     * パスワードオブジェクトを保存する
     *
     * @param AccountEntity $accountEntity
     * @return AccountEntity
     * @throws DomainException
     */
    public function savePassword(AccountEntity $accountEntity): AccountEntity
    {
        $passwordValue = $accountEntity->getPasswordValue();

        $values = [
            'account_id'    => $accountEntity->getSub(),
            'password_hash' => $passwordValue->getPasswordHash(),
        ];

        $result = \DB::table('accounts_passwords')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        $passwordId = \DB::getPdo()->lastInsertId();

        $newPasswordValue = ValueFactory::createPasswordValue(
            [
                'password'     => $passwordValue->getPassword(),
                'passwordHash' => $passwordValue->getPasswordHash(),
                'lockVersion'  => 0,
                'id'           => (int)$passwordId,
            ]
        );

        $accountEntity->setPasswordValue($newPasswordValue);

        return $accountEntity;
    }

    /**
     * アカウントEntityを取得する
     *
     * @param int $sub
     * @return AccountEntity
     */
    public function findAccountEntity(int $sub): AccountEntity
    {
        $selectColumns = [
            'a.id AS sub',
            'a.register_id',
            'a.status',
            'a.lock_version AS account_lock_version',
            'em.id AS email_id',
            'em.email',
            'em.email_verified',
            'em.lock_version AS email_lock_version',
            'ap.id AS password_id',
            'ap.password_hash',
            'ap.lock_version AS accounts_passwords_lock_version',
        ];

        $account = \DB::table('accounts AS a')
            ->select($selectColumns)
            ->join('accounts_emails AS em', 'a.id', '=', 'em.account_id')
            ->join('accounts_passwords AS ap', 'a.id', '=', 'ap.account_id')
            ->where('a.id', '=', $sub)
            ->first();

        if (is_null($account) === true) {
            return $account;
        }

        $accountEntity = EntityFactory::createAccountEntity(
            $sub
        );

        $accountEntity
            ->setAccountStatus($account->status)
            ->setLockVersion($account->account_lock_version);

        $emailCreateParams = [
            'id'            => $account->email_id,
            'email'         => $account->email,
            'emailVerified' => $account->email_verified,
            'lockVersion'   => $account->email_lock_version,
        ];

        $emailValue = ValueFactory::createEmailValue(
            $emailCreateParams
        );

        $accountEntity->setEmailValue($emailValue);

        $passwordValue = ValueFactory::createPasswordValue(
            [
                'passwordHash' => $account->password_hash,
                'lockVersion'  => $account->lock_version,
                'lockVersion'  => $account->accounts_passwords_lock_version,
                'id'           => $account->password_id,
            ]
        );

        $accountEntity->setPasswordValue($passwordValue);

        return $accountEntity;
    }

    /**
     * メールアドレスからアカウントEntityを検索する
     *
     * @param EmailValue $emailValue
     * @return AccountEntity
     */
    public function findAccountEntityByEmail(EmailValue $emailValue): AccountEntity
    {
        $selectColumns = [
            'a.id',
            'a.status',
            'a.lock_version AS accounts_lock_version',
            'ae.id AS email_id',
            'ae.email',
            'ae.email_verified',
            'ae.lock_version AS accounts_emails_lock_version',
            'ap.id AS password_id',
            'ap.password_hash',
            'ap.lock_version AS accounts_passwords_lock_version',
        ];

        $account = \DB::table('accounts_emails AS ae')
            ->select($selectColumns)
            ->join('accounts AS a', 'a.id', '=', 'ae.account_id')
            ->join('accounts_passwords AS ap', 'a.id', '=', 'ap.account_id')
            ->where('ae.email', '=', $emailValue->getEmail())
            ->first();

        if (is_null($account) === true) {
            return EntityFactory::createEmptyAccountEntity();
        }

        $accountEntity = EntityFactory::createAccountEntity($account->id);
        $emailValue = ValueFactory::createEmailValue(
            [
                'email'         => $account->email,
                'emailVerified' => $account->email_verified,
                'lockVersion'   => $account->accounts_emails_lock_version,
                'id'            => $account->email_id,
            ]
        );

        $accountEntity->setEmailValue($emailValue)
            ->setLockVersion($account->accounts_lock_version);

        $passwordValue = ValueFactory::createPasswordValue(
            [
                'passwordHash' => $account->password_hash,
                'lockVersion'  => $account->accounts_passwords_lock_version,
                'id'           => $account->password_id,
            ]
        );

        $accountEntity->setPasswordValue($passwordValue);

        return $accountEntity;
    }
}
