<?php
/**
 * PasswordValue
 *
 * @author keita-nishimoto
 * @since 2016-10-05
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

use Domain\ValueObjectInterface;

/**
 * Class PasswordValue
 *
 * @category laravel-api-sample
 * @package Domain\Account
 * @author keita-nishimoto
 * @since 2016-10-05
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class PasswordValue implements ValueObjectInterface
{
    /**
     * パスワード
     *
     * @var string
     */
    private $password;

    /**
     * パスワードハッシュ
     *
     * @var string
     */
    private $passwordHash;

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
     * PasswordValue constructor.
     *
     * @param string $password
     * @param string $passwordHash
     * @param int $lockVersion
     * @param int $id
     */
    public function __construct(
        string $password = '',
        string $passwordHash = '',
        int $lockVersion = 0,
        int $id = 0
    ) {
        $this->setPassword($password)
            ->setPasswordHash($passwordHash)
            ->setLockVersion($lockVersion)
            ->setId($id);

        if (empty($this->getPassword()) === false && empty($this->getPasswordHash()) === true) {
            $this->generatePasswordHash();
        }
    }

    /**
     * 自身が空のオブジェクトか判定する
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        $password     = $this->getPassword();
        $passwordHash = $this->getPasswordHash();

        if ($password === '' && $passwordHash === '') {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return PasswordValue
     */
    private function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param string $passwordHash
     * @return PasswordValue
     */
    private function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;

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
     * @return PasswordValue
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
     * @return PasswordValue
     */
    private function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * パスワードハッシュを生成する
     *
     * @return PasswordValue
     */
    private function generatePasswordHash(): self
    {
        $password = $this->getPassword();

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->setPasswordHash($passwordHash);

        return $this;
    }

    /**
     * パスワードが合致するか？
     *
     * @param PasswordValue $passwordValue
     * @return bool
     */
    public function isPasswordMatch(PasswordValue $passwordValue): bool
    {
        return password_verify(
            $passwordValue->getPassword(),
            $this->getPasswordHash()
        );
    }
}
