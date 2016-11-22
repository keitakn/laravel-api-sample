<?php
/**
 * カスタムバリデーションを定義するクラス
 *
 * @author keita-nishimoto
 * @since 2016-10-31
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain;

use Illuminate\Validation\Validator;

/**
 * Class CustomValidator
 *
 * @category laravel-api-sample
 * @package Domain
 * @author keita-nishimoto
 * @since 2016-10-31
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class CustomValidator extends Validator
{
    /**
     * パスワードのバリデーション
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validatePassword($attribute, $value, $parameters): bool
    {
        if (is_string($value) === false) {
            return false;
        }

        if (is_null($value) === true) {
            return false;
        }

        // 半角英数字をそれぞれ1種類以上含む8文字以上100文字以下
        if (preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}+\z/i', $value) === 0) {
            return false;
        }

        return true;
    }

    /**
     * 姓名のバリデーション
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateName($attribute, $value, $parameters): bool
    {
        if (mb_strlen($value) > 100) {
            return false;
        }

        // 記号と数値判定
        if (preg_match('/[ -\/:-@¥\[-`\{-\~(0-9)]/', $value) !== 0) {
            return false;
        }

        return true;
    }

    /**
     * 姓名（ふりがな）のバリデーション
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateKana($attribute, $value, $parameters): bool
    {
        if (is_string($value) === false) {
            return false;
        }

        if (mb_strlen($value) > 100) {
            return false;
        }

        mb_regex_encoding('UTF-8');
        if (preg_match('/^[ぁ-んァ-ヶー]+$/u', $value) !== 0) {
            return true;
        }

        return false;
    }
}
