<?php
/**
 * ValidationService
 * ドメイン層のバリデーションロジックを担当する
 *
 * @author keita-nishimoto
 * @since 2016-09-15
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain;

use Exceptions\DomainException;
use Exceptions\RuntimeException;
use Exceptions\ValidationException;

/**
 * Class ValidationService
 *
 * @category laravel-api-sample
 * @package Domain
 * @author keita-nishimoto
 * @since 2016-09-15
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ValidationService
{

    /**
     * バリデーションを実行する
     * ドメイン層のサービスメソッドの開始時に必ず実行する
     *
     * @param string $serviceName
     * @param string $executeMethod
     * @param array $params
     * @throws ValidationException
     */
    public static function doValidate(
        string $serviceName,
        string $executeMethod,
        array $params = []
    ) {
        $validationRules = self::getValidationRules($serviceName, $executeMethod);

        $validator = \Validator::make(
            $params,
            $validationRules
        );

        if ($validator->passes() === false) {
            $messages = $validator->getMessageBag()->getMessages();
            throw new ValidationException($messages);
        }
    }

    /**
     * バリデーション属性定義を取得する
     *
     * config/validation_attributes.php に定義ファイルがあるのでそこに属性定義を追加する。
     *
     * @param string $attributeKey
     * @return string
     * @throws RuntimeException
     * @link https://readouble.com/laravel/5.3/ja/validation.html
     */
    private static function getValidationAttribute(string $attributeKey): string
    {
        $validationAttribute = \Config::get('validation_attributes.' . $attributeKey);
        if (empty($validationAttribute) === true) {
            throw new RuntimeException(1);
        }

        return $validationAttribute;
    }

    /**
     * バリデーションパラメータ定義を取得する
     *
     * config/validation_params.php に定義ファイルがあるのでそこにパラメータ定義を追加する。
     * 対象パラメータが必須かそうでないかを記載する。
     *
     * @param string $serviceName
     * @param string $executeMethod
     * @return array
     * @throws RuntimeException
     * @link https://readouble.com/laravel/5.3/ja/validation.html
     */
    private static function getValidationParams(
        string $serviceName,
        string $executeMethod
    ): array {
        $paramsKey = sprintf('validation_params.%s.%s', $serviceName, $executeMethod);
        $validationParams = \Config::get($paramsKey);

        if (empty($validationParams) === true) {
            throw new RuntimeException(0);
        }

        return $validationParams;
    }

    /**
     * バリデーションルールを取得する
     *
     * @param string $serviceName
     * @param string $executeMethod
     * @return array
     * @throws DomainException
     * @see getValidationParams
     * @see getValidationAttribute
     * @link https://readouble.com/laravel/5.3/ja/validation.html
     */
    private static function getValidationRules(
        string $serviceName,
        string $executeMethod
    ): array {
        $validationParams = self::getValidationParams($serviceName, $executeMethod);

        foreach ($validationParams as $validationParamKey => $validationRule) {
            $validationAttribute = self::getValidationAttribute($validationParamKey);
            $validationRules[$validationParamKey] = $validationRule . $validationAttribute;
        }

        return $validationRules;
    }
}
