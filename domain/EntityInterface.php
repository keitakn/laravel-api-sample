<?php
/**
 * Entityクラスのインターフェース
 * 各Entityクラスは必ずimplementsする事
 *
 * @author keita-nishimoto
 * @since 2016-11-18
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain;

/**
 * Interface EntityInterface
 *
 * @category laravel-api-sample
 * @package Domain
 * @author keita-nishimoto
 * @since 2016-11-18
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
interface EntityInterface
{
    /**
     * 自身が空のEntityか判定する
     * 何をもって空とするかは各Entityクラスで実装を行う
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
