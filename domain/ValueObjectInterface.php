<?php
/**
 * ValueObjectのインターフェース
 * 各ValueObjectは必ずimplementsする事
 *
 * @author keita-nishimoto
 * @since 2016-11-18
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain;

/**
 * Interface ValueObjectInterface
 *
 * @category laravel-api-sample
 * @package Domain
 * @author keita-nishimoto
 * @since 2016-11-18
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
interface ValueObjectInterface
{
    /**
     * 自身が空のオブジェクトか判定する
     * 何をもって空とするかは各ValueObjectで実装を行う
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
