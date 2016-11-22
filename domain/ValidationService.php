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
     * @param $serviceName
     * @param $executeMethod
     * @param array $params
     * @throws ValidationException
     */
    public static function doValidate($serviceName, $executeMethod, $params = [])
    {
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
     * @param $attributeKey
     * @return mixed
     * @throws DomainException
     * @link https://readouble.com/laravel/5.3/ja/validation.html
     */
    private static function getValidationAttribute($attributeKey)
    {
        $validationAttribute = \Config::get('validation_attributes.' . $attributeKey);
        if (empty($validationAttribute) === true) {
            throw new DomainException(10002);
        }

        return $validationAttribute;
    }

    /**
     * バリデーションパラメータ定義を取得する
     *
     * config/validation_params.php に定義ファイルがあるのでそこにパラメータ定義を追加する。
     * 対象パラメータが必須かそうでないかを記載する。
     *
     * 詳しくは@linkのLaravel バリデーションを参照
     *
     * @param $serviceName
     * @param $executeMethod
     * @return mixed
     * @throws DomainException
     * @link https://readouble.com/laravel/5.3/ja/validation.html
     */
    private static function getValidationParams($serviceName, $executeMethod)
    {
        $paramsKey = sprintf('validation_params.%s.%s', $serviceName, $executeMethod);
        $validationParams = \Config::get($paramsKey);

        if (empty($validationParams) === true) {
            throw new DomainException(10001);
        }

        return $validationParams;
    }

    /**
     * バリデーションルールを取得する
     *
     * @param $serviceName
     * @param $executeMethod
     * @return mixed
     * @throws DomainException
     * @see getValidationParams
     * @see getValidationAttribute
     * @link https://readouble.com/laravel/5.3/ja/validation.html
     */
    private static function getValidationRules($serviceName, $executeMethod)
    {
        $validationParams = self::getValidationParams($serviceName, $executeMethod);

        foreach ($validationParams as $validationParamKey => $validationRule) {
            $validationAttribute = self::getValidationAttribute($validationParamKey);
            $validationRules[$validationParamKey] = $validationRule . $validationAttribute;
        }

        return $validationRules;
    }
}
