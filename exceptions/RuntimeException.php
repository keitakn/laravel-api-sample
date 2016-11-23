<?php
/**
 * ドメイン層で発生した実行時エラー
 * このクラスをそのまま上位にthrowしない事
 * 異常系のレスポンスを返す際は必ず本例外クラスを補足して \Exceptions\DomainExceptionに変換してから行う事
 *
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Exceptions;

/**
 * Class RuntimeException
 *
 * @category laravel-api-sample
 * @package Exceptions
 * @author keita-nishimoto
 * @since 2016-11-18
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class RuntimeException extends \RuntimeException
{
    /**
     * エラーメッセージのリスト
     *
     * @var array
     */
    private static $errorMessages = [
        0 => 'バリデーションパラメータ定義が設定されていません。',
        1 => 'バリデーション属性定義が設定されていません。',
        2 => '指定されたサービスクラスは存在しません。',
        3 => '指定されたサービスメソッドが存在しません。',
    ];

    /**
     * エラーコード
     *
     * @var int
     */
    private $errorCode;

    /**
     * エラーメッセージ
     *
     * @var string
     */
    private $errorMessage;

    /**
     * RuntimeException constructor.
     *
     * @param int $errorCode
     * @param \Exception $previous
     */
    public function __construct($errorCode, \Exception $previous = null)
    {
        $this->setErrorCode($errorCode);
        $errorMessage = $this->getErrorMessage();

        parent::__construct($errorMessage, $errorCode, $previous);
    }

    /**
     * @return array
     */
    public function getErrorMessages(): array
    {
        return self::$errorMessages;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * エラーメッセージを取得する
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        $errorMessages = $this->getErrorMessages();

        $errorCode = $this->getErrorCode();
        $errorMessage = $errorMessages[$errorCode];

        if (empty($errorMessage) === true) {
            return '';
        }

        $this->setErrorMessage($errorMessage);

        return $errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    private function setErrorMessage(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param int $errorCode
     */
    private function setErrorCode(int $errorCode)
    {
        $this->errorCode = $errorCode;
    }
}
