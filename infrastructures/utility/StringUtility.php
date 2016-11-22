<?php
/**
 * 文字列操作のUtilityクラス
 *
 * @author keita-nishimoto
 * @since 2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Infrastructures\Utility;

use Ramsey\Uuid\Uuid;

/**
 * Class StringUtility
 *
 * @category laravel-api-sample
 * @package Infrastructures\Utility
 * @author keita-nishimoto
 * @since 2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class StringUtility
{
    /**
     * UUIDを生成する
     *
     * @return string
     */
    public static function generateUuid()
    {
        return Uuid::uuid4()->toString();
    }
}
