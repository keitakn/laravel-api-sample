<?php
/**
 * Entityを生成するクラス
 *
 * @category laravel-api-sample
 * @package Factories
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Factories;

use Domain\RequestEntity;
use Domain\ResponseEntity;
use Illuminate\Http\Request;

/**
 * Class EntityFactory
 *
 * @category laravel-api-sample
 * @package Factories
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class EntityFactory
{
    /**
     * インスタンス格納用の変数
     *
     * @var array
     */
    private static $instancePool = [];

    /**
     * RequestEntityを生成する
     *
     * @param string $XRequestId
     * @param \Illuminate\Http\Request $request
     * @param array $urlParams
     * @return \Domain\RequestEntity
     */
    public static function createRequestEntity($XRequestId, Request $request, $urlParams = [])
    {
        $instanceKey = 'RequestEntity' . $XRequestId;
        if (array_key_exists($instanceKey, self::$instancePool)) {
            if (self::$instancePool[$instanceKey] instanceof RequestEntity) {
                return self::$instancePool[$instanceKey];
            }
        }

        self::$instancePool[$instanceKey] = new RequestEntity($XRequestId, $request, $urlParams);
        return self::$instancePool[$instanceKey];
    }

    /**
     * ResponseEntityを生成する
     *
     * @param \Domain\RequestEntity $requestEntity
     * @return \Domain\ResponseEntity
     */
    public static function createResponseEntity(RequestEntity $requestEntity)
    {
        $instanceKey = 'ResponseEntity' . $requestEntity->getXRequestId();
        if (array_key_exists($instanceKey, self::$instancePool)) {
            if (self::$instancePool[$instanceKey] instanceof ResponseEntity) {
                return self::$instancePool[$instanceKey];
            }
        }

        self::$instancePool[$instanceKey] = new ResponseEntity($requestEntity);
        return self::$instancePool[$instanceKey];
    }
}
