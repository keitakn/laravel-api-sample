<?php
/**
 * AccountEntity
 *
 * @author keita-nishimoto
 * @since 2016-10-06
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

use Repositories\Mysql\AccountRepository;

/**
 * Class AccountEntity
 *
 * @category laravel-api-sample
 * @package Domain\Account
 * @author keita-nishimoto
 * @since 2016-10-06
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class AccountEntity
{
    /**
     * ユーザーID
     *
     * @var int
     */
    private $sub;

    /**
     * アカウントステータス
     *
     * @var int
     */
    private $accountStatus;

    /**
     * メールアドレス ValueObject
     *
     * @var \Domain\Account\EmailValue
     */
    private $emailValue;

    /**
     * パスワード PasswordValue
     *
     * @var PasswordValue[]
     */
    private $passwordValues;

    /**
     * ロックバージョン
     *
     * @var int
     */
    private $lockVersion;

    /**
     * AccountEntity constructor.
     *
     * @param int $sub
     */
    public function __construct(int $sub)
    {
        // ユビキタス言語的にはアカウントIDをsubと呼ぶ
        // 命名はOpenIDConnect 5.1. Standard Claimsより
        $this->setSub($sub);
    }

    /**
     * @return int
     */
    public function getSub(): int
    {
        return $this->sub;
    }

    /**
     * @param int $sub
     * @return AccountEntity
     */
    private function setSub(int $sub): self
    {
        $this->sub = $sub;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccountStatus(): int
    {
        return $this->accountStatus;
    }

    /**
     * @param int $accountStatus
     * @return AccountEntity
     */
    public function setAccountStatus(int $accountStatus): self
    {
        $this->accountStatus = $accountStatus;

        return $this;
    }

    /**
     * @return EmailValue
     */
    public function getEmailValue(): EmailValue
    {
        return $this->emailValue;
    }

    /**
     * @param EmailValue $emailValue
     * @return AccountEntity
     */
    public function setEmailValue(EmailValue $emailValue): self
    {
        $this->emailValue = $emailValue;

        return $this;
    }

    /**
     * @return PasswordValue
     */
    public function getPasswordValue(): PasswordValue
    {
        return $this->passwordValues;
    }

    /**
     * @param PasswordValue $passwordValues
     * @return AccountEntity
     */
    public function setPasswordValue(PasswordValue $passwordValues): self
    {
        $this->passwordValues = $passwordValues;

        return $this;
    }

    /**
     * @return int
     */
    public function getLockVersion(): int
    {
        return $this->lockVersion;
    }

    /**
     * @param int $lockVersion
     * @return AccountEntity
     */
    public function setLockVersion(int $lockVersion): self
    {
        $this->lockVersion = $lockVersion;

        return $this;
    }

    /**
     * アカウントステータスを文字列に変換する
     *
     * @return string
     */
    public function convertAccountStatusToString(): string
    {
        switch ($this->getAccountStatus()) {
            case 0:
                // 有効
                return 'enabled';
                break;
            case 1:
                // 退会
                return 'canceled';
                break;
            default:
                return 'enabled';
            break;
        }
    }

    /**
     * 退会アカウント？
     *
     * @return bool
     */
    public function isCanceled(): bool
    {
        if ($this->getAccountStatus() === 1) {
            return true;
        }

        return false;
    }

    /**
     * 自身をDBに登録する
     *
     * @return AccountEntity
     */
    public function save(): self
    {
        $accountRepository = AccountRepository::getInstance();
        $accountRepository->saveAccountEntity($this);

        return $this;
    }

    /**
     * メールアドレスをDBに保存する
     *
     * @return $this
     */
    public function saveEmail()
    {
        $accountRepository = AccountRepository::getInstance();
        $accountRepository->saveEmail($this);

        return $this;
    }

    /**
     * パスワードをDBに保存する
     *
     * @return $this
     */
    public function savePassword()
    {
        $accountRepository = AccountRepository::getInstance();
        $accountRepository->savePassword($this);

        return $this;
    }
}
