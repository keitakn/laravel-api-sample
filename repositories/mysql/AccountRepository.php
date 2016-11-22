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
            if ($instance instanceof \Repositories\Mysql\AccountRepository) {
                return $instance;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Repositories\Mysql\AccountRepository');
            $instance = \App::make($instanceKey);

            return $instance;
        }
    }

    /**
     * アカウントEntityを保存する
     *
     * @param AccountEntity $accountEntity
     * @return AccountEntity
     * @throws DomainException
     */
    public function saveAccountEntity(AccountEntity $accountEntity)
    {
        $values = [
            'id'          => $accountEntity->getSub(),
            'register_id' => $accountEntity->getRegisterId(),
            'status'      => $accountEntity->getAccountStatus(),
        ];

        $result = \DB::table('accounts')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        return $accountEntity;
    }

    /**
     * メールアドレスオブジェクトを保存する
     *
     * @param AccountEntity $accountEntity
     * @return AccountEntity
     * @throws DomainException
     */
    public function saveEmail(AccountEntity $accountEntity)
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
                'id'            => $emailId,
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
        $passwordValue = $accountEntity->getAuthenticationPassword();

        $values = [
            'account_id'    => $accountEntity->getSub(),
            'password_hash' => $passwordValue->getPasswordHash(),
            'password_type' => $passwordValue->getPasswordType(),
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
                'passwordType' => $passwordValue->getPasswordType(),
                'lockVersion'  => 0,
                'id'           => (int)$passwordId,
            ]
        );

        $accountEntity->setAuthenticationPassword($newPasswordValue);

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
            return $account;
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
                'lockVersion'  => $account->lock_version,
                'lockVersion'  => $account->accounts_passwords_lock_version,
                'id'           => $account->password_id,
            ]
        );

        $accountEntity->setPasswordValue($passwordValue);

        return $accountEntity;
    }

    /**
     * ユーザーIDから認証用パスワードを検索する
     *
     * @param $sub
     * @return null|\stdClass
     */
    private function findAuthenticationPasswordBySub($sub)
    {
        $selectColumns = [
            'id',
            'password_hash',
            'password_type',
            'lock_version',
        ];

        $passwords = \DB::table('accounts_passwords')
            ->select($selectColumns)
            ->where('account_id', '=', $sub)
            ->first();

        return $passwords;
    }
}
