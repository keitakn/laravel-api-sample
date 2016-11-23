<?php
/**
 * アカウントリポジトリのインターフェース
 *
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

/**
 * Interface AccountRepositoryInterface
 *
 * @category laravel-api-sample
 * @package Domain\Account
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
interface AccountRepositoryInterface
{
    /**
     * 自身のインスタンスを生成する
     *
     * @return $this
     */
    public static function getInstance();

    /**
     * アカウントEntityを新規で作成し保存する
     *
     * @param array $params
     * @return AccountEntity
     */
    public function createAccountEntity(array $params): AccountEntity;

    /**
     * メールアドレスオブジェクトを保存する
     *
     * @param AccountEntity $accountEntity
     * @return AccountEntity
     */
    public function saveEmail(AccountEntity $accountEntity): AccountEntity;

    /**
     * パスワードを保存する
     *
     * @param AccountEntity $accountEntity
     * @return AccountEntity
     */
    public function savePassword(AccountEntity $accountEntity): AccountEntity;

    /**
     * アカウントEntityを取得する
     *
     * @param int $sub
     * @return AccountEntity
     */
    public function findAccountEntity(int $sub): AccountEntity;

    /**
     * メールアドレスからアカウントEntityを取得する
     *
     * @param EmailValue $emailValue
     * @return AccountEntity
     */
    public function findAccountEntityByEmail(EmailValue $emailValue): AccountEntity;
}
