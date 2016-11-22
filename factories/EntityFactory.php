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
     * RequestEntityを生成する
     *
     * @param string $XRequestId
     * @param \Illuminate\Http\Request $request
     * @param array $urlParams
     * @return \Domain\RequestEntity
     */
    public static function createRequestEntity($XRequestId, \Illuminate\Http\Request $request, $urlParams = [])
    {
        $instanceKey = 'RequestEntity' . $XRequestId;
        try {
            $requestEntity = \App::make($instanceKey);
            if ($requestEntity instanceof \Domain\RequestEntity) {
                return $requestEntity;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Domain\RequestEntity');
            $requestEntity = \App::make($instanceKey, [$XRequestId, $request, $urlParams]);

            return $requestEntity;
        }
    }

    /**
     * ResponseEntityを生成する
     *
     * @param \Domain\RequestEntity $requestEntity
     * @return \Domain\ResponseEntity
     */
    public static function createResponseEntity(\Domain\RequestEntity $requestEntity)
    {
        $instanceKey = 'ResponseEntity' . $requestEntity->getXRequestId();
        try {
            $responseEntity = \App::make($instanceKey);
            if ($responseEntity instanceof \Domain\ResponseEntity) {
                return $responseEntity;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Domain\ResponseEntity');
            $responseEntity = \App::make($instanceKey, [$requestEntity]);

            return $responseEntity;
        }
    }
}
