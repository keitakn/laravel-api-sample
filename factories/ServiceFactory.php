<?php
/**
 * ドメイン層のサービスクラスを生成する
 *
 * @author keita-nishimoto
 * @since 2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Factories;

/**
 * Class ServiceFactory
 *
 * @category laravel-api-sample
 * @package Factories
 * @author keita-nishimoto
 * @since 2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ServiceFactory
{
    /**
     * ドメイン層のサービスクラスフォーマット
     */
    const SERVICE_CLASS_FORMAT = '\\Domain\\%s\\%sService';

    /**
     * サービスクラスのインスタンスプール
     *
     * @var array
     */
    private static $instancePool = [];

    /**
     * サービスクラスを生成する
     *
     * @param $serviceName
     * @param $executeMethod
     * @return mixed
     * @throws \Exception
     */
    public static function create($serviceName, $executeMethod)
    {
        // TODO 20160909 オブジェクトの生成は後でLaravelのDIコンテナに置き換える @keita-koga
        $serviceName = ucfirst($serviceName);

        $className = sprintf(
            self::SERVICE_CLASS_FORMAT,
            $serviceName,
            $serviceName
        );

        if (class_exists($className) === false) {
            // TODO 20160909 ちゃんとした例外の設計を行う @keita-koga
            throw new \Exception('クラス名が存在しません。');
        }

        if (array_key_exists($serviceName, self::$instancePool) === true) {
            if (method_exists(self::$instancePool[$serviceName], $executeMethod) === false) {
                // TODO 20160909 ちゃんとした例外の設計を行う @keita-koga
                throw new \Exception('メソッドが存在しません。');
            }
            return self::$instancePool[$serviceName];
        }

        $service = new $className();
        if (method_exists($service, $executeMethod) === false) {
            // TODO 20160909 ちゃんとした例外の設計を行う @keita-koga
            throw new \Exception('メソッドが存在しません。');
        }

        self::$instancePool[$serviceName] = $service;

        return self::$instancePool[$serviceName];
    }
}
