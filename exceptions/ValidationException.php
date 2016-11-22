<?php
/**
 * ドメイン層で利用するバリデーション用の例外クラス
 *
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Exceptions;

/**
 * Class ValidationException
 *
 * @category laravel-api-sample
 * @package Exceptions
 * @author keita-nishimoto
 * @since 2016-09-14
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ValidationException extends DomainException
{
    /**
     * バリデーションエラー時のエラーコード
     */
    const VALIDATION_ERROR_CODE = 422;

    /**
     * バリデーションエラー詳細
     *
     * @var array
     */
    private $validationErrors;

    /**
     * ValidateException constructor.
     *
     * @param array $validationErrors
     * @param \Exception|null $previous
     */
    public function __construct($validationErrors = [], \Exception $previous = null)
    {
        parent::__construct(self::VALIDATION_ERROR_CODE, $previous);
        $this->setValidationErrors($validationErrors);
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * @param array $validationErrors
     */
    private function setValidationErrors($validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }
}
