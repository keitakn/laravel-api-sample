<?php
/**
 * ドメイン層で利用する例外クラス
 *
 * @author keita-nishimoto
 * @since 2016-09-14
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Exceptions;

/**
 * Class DomainException
 *
 * @category laravel-api-sample
 * @package Exceptions
 * @author keita-nishimoto
 * @since 2016-09-14
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class DomainException extends \Exception
{
    /**
     * エラーコード
     *
     * @var int
     */
    protected $errorCode;

    /**
     * エラーメッセージ
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * DomainException constructor.
     *
     * @param int $errorCode
     * @param \Exception|null $previous
     */
    public function __construct($errorCode = 10000, \Exception $previous = null)
    {
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $this->setErrorCode($errorCode);
        $this->setErrorMessage($errorMessage);

        parent::__construct(
            $errorMessage,
            $errorCode,
            $previous
        );
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    protected function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    protected function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }
}
