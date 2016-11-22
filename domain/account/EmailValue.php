<?php
/**
 * メールアドレス ValueObject
 *
 * @author keita-nishimoto
 * @since 2016-09-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

/**
 * Class EmailValue
 *
 * @category laravel-api-sample
 * @package Domain\Account
 * @author keita-nishimoto
 * @since 2016-09-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class EmailValue
{
    /**
     * メールアドレス
     *
     * @var string
     */
    private $email;

    /**
     * メールアドレス検証済ステータス
     *
     * @var bool
     */
    private $emailVerified;

    /**
     * ロックバージョン
     *
     * @var int
     */
    private $lockVersion;

    /**
     * データ識別子
     *
     * @var int
     */
    private $id;

    /**
     * EmailValue constructor.
     *
     * @param string $email
     * @param int $emailVerified
     * @param int $lockVersion
     * @param int $id
     */
    public function __construct(
        string $email,
        int $emailVerified,
        int $lockVersion = 0,
        int $id = 0
    ) {
        $this->setEmail($email)
            ->setEmailVerified($emailVerified)
            ->setLockVersion($lockVersion)
            ->setId($id);
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param $email
     * @return EmailValue
     */
    private function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return int
     */
    public function getEmailVerified(): int
    {
        return $this->emailVerified;
    }

    /**
     * メールアドレスが検証済か確認する
     *
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return (bool)$this->getEmailVerified();
    }

    /**
     * @param int $emailVerified
     * @return $this
     */
    private function setEmailVerified(int $emailVerified)
    {
        $this->emailVerified = $emailVerified;

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
     * @return EmailValue
     */
    private function setLockVersion(int $lockVersion): self
    {
        $this->lockVersion = $lockVersion;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return EmailValue
     */
    private function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}
